<?php
class AuthController extends Controller
{
    /** Portals people can sign up for / log in to. Admin accounts are created by another admin. */
    private const PORTALS = ['student', 'teacher'];

    private function portal(?string $value): string
    {
        return in_array($value, self::PORTALS, true) ? $value : 'student';
    }

    public function signupForm(): void
    {
        if (Auth::check()) $this->redirect(portal_home(Auth::user()));
        $portal = $this->portal($_SESSION['old']['portal'] ?? $_GET['as'] ?? null);
        $this->view('auth/signup', ['title' => 'Create your account', 'shell' => 'auth', 'portal' => $portal, 'errors' => $this->errors()]);
    }

    public function signup(): void
    {
        $d = $_POST;
        $portal = $this->portal($d['portal'] ?? null);
        $v = new Validator($d);
        $v->required('name', 'Name')->max('name', 100, 'Name')
          ->required('email', 'Email')->email('email')->max('email', 150, 'Email')
          ->required('password', 'Password')->min('password', 8, 'Password')->password('password')
          ->matches('password_confirm', 'password', 'Password confirmation');
        $errors = $v->errors;
        if (!$errors && User::findByEmail(trim($d['email']))) {
            $errors['email'] = 'This email is already registered.';
        }
        if ($errors) $this->withErrors($errors, ['name' => $d['name'] ?? '', 'email' => $d['email'] ?? '', 'portal' => $portal], '/signup?as=' . $portal);

        $hash = password_hash($d['password'], PASSWORD_DEFAULT);
        if ($portal === 'teacher') {
            // teacher accounts can publish content, so an admin approves them first
            User::create(trim($d['name']), trim($d['email']), $hash, 'teacher', 'pending');
            flash('info', 'Thanks for signing up as a teacher. An admin will review your account; you can log in once it is approved.');
            $this->redirect('/login?as=teacher');
        }

        $id = User::create(trim($d['name']), trim($d['email']), $hash, 'student', 'active');
        Auth::login(User::find($id));
        flash('success', 'Welcome to ShikkhaBridge AI! Your student account is ready.');
        $this->redirect('/student');
    }

    public function loginForm(): void
    {
        if (Auth::check()) $this->redirect(portal_home(Auth::user()));
        $portal = $this->portal($_SESSION['old']['portal'] ?? $_GET['as'] ?? null);
        $this->view('auth/login', ['title' => 'Log in', 'shell' => 'auth', 'portal' => $portal, 'errors' => $this->errors()]);
    }

    public function login(): void
    {
        $email = trim($_POST['email'] ?? '');
        $password = (string)($_POST['password'] ?? '');
        $portal = $this->portal($_POST['portal'] ?? null);
        $old = ['email' => $email, 'portal' => $portal];
        $back = '/login?as=' . $portal;

        $v = new Validator($_POST);
        $v->required('email', 'Email')->email('email')->required('password', 'Password');
        if ($v->fails()) $this->withErrors($v->errors, $old, $back);

        if (Auth::tooManyAttempts($email)) {
            flash('danger', 'Too many failed attempts. Please wait ' . config('lockout_minutes') . ' minutes and try again.');
            $this->redirect($back);
        }
        $user = User::findByEmail($email);
        if (!$user || !password_verify($password, $user['password_hash'])) {
            Auth::recordFailure($email);
            $this->withErrors(['password' => 'Wrong email or password.'], $old, $back);
        }
        // Status and role are only revealed after a correct password.
        if ($user['status'] === 'pending') {
            $this->withErrors(['email' => 'Your teacher account is waiting for admin approval.'], $old, $back);
        }
        if ($user['status'] === 'suspended') {
            $this->withErrors(['email' => 'This account has been suspended. Please contact the admin.'], $old, $back);
        }
        if ($user['role'] !== 'admin' && $user['role'] !== $portal) {
            $right = $user['role'];
            $this->withErrors(['email' => "This is a $right account. Please use the " . ucfirst($right) . ' login.'], ['email' => $email, 'portal' => $right], '/login?as=' . $right);
        }

        if (password_needs_rehash($user['password_hash'], PASSWORD_DEFAULT)) {
            User::updatePassword((int)$user['id'], password_hash($password, PASSWORD_DEFAULT));
        }
        Auth::clearFailures($email);
        Auth::login($user);
        flash('success', 'Welcome back, ' . $user['name'] . '!');
        $this->redirect(portal_home($user));
    }

    public function logout(): void
    {
        Auth::logout();
        session_start();
        flash('info', 'You have been logged out.');
        $this->redirect('/');
    }

    public function forgotForm(): void
    {
        $this->view('auth/forgot', ['title' => 'Forgot password', 'shell' => 'auth', 'errors' => $this->errors()]);
    }

    public function forgot(): void
    {
        $email = trim($_POST['email'] ?? '');
        $v = new Validator($_POST);
        $v->required('email', 'Email')->email('email');
        if ($v->fails()) $this->withErrors($v->errors, ['email' => $email], '/forgot-password');

        $user = User::findByEmail($email);
        if ($user) {
            $token = User::createResetToken((int)$user['id']);
            if (config('show_reset_link')) {
                flash('info', 'Demo mode (no mail server): <a href="' . e(url('/reset-password/' . $token)) . '">click here to reset your password</a>. The link works once and expires in 1 hour.');
            }
        }
        flash('success', 'If that email is registered, a reset link has been created.');
        $this->redirect('/forgot-password');
    }

    public function resetForm(string $token): void
    {
        if (!User::findValidReset($token)) {
            flash('danger', 'This reset link is invalid or has expired.');
            $this->redirect('/forgot-password');
        }
        $this->view('auth/reset', ['title' => 'Reset password', 'shell' => 'auth', 'token' => $token, 'errors' => $this->errors()]);
    }

    public function reset(string $token): void
    {
        $row = User::findValidReset($token);
        if (!$row) {
            flash('danger', 'This reset link is invalid or has expired.');
            $this->redirect('/forgot-password');
        }
        $v = new Validator($_POST);
        $v->required('password', 'Password')->min('password', 8, 'Password')->password('password')->matches('password_confirm', 'password', 'Password confirmation');
        if ($v->fails()) $this->withErrors($v->errors, [], '/reset-password/' . $token);

        User::updatePassword((int)$row['user_id'], password_hash($_POST['password'], PASSWORD_DEFAULT));
        User::markResetUsed((int)$row['id']);
        flash('success', 'Password changed. You can log in now.');
        $this->redirect('/login');
    }
}
