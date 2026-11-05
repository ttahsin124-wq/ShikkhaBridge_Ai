<?php
class ProfileController extends Controller
{
    public function show(): void
    {
        $this->requireLogin();
        $this->view('profile', ['title' => 'My profile', 'errors' => $this->errors()]);
    }

    public function update(): void
    {
        $this->requireLogin();
        $user = Auth::user();
        $v = new Validator($_POST);
        $v->required('name', 'Name')->max('name', 100, 'Name')->max('bio', 500, 'Bio')->in('language_pref', ['bn', 'en'], 'Language');
        $errors = $v->errors;

        [$avatar, $avatarError] = $this->handleAvatar($_FILES['avatar'] ?? null);
        if ($avatarError) $errors['avatar'] = $avatarError;
        if ($errors) $this->withErrors($errors, ['name' => $_POST['name'] ?? '', 'bio' => $_POST['bio'] ?? ''], '/profile');

        User::updateProfile((int)$user['id'], trim($_POST['name']), trim($_POST['bio'] ?? ''), $_POST['language_pref'], $avatar);
        if ($avatar !== null && $user['avatar']) {
            $oldFile = APP_ROOT . '/public/uploads/avatars/' . basename($user['avatar']);
            if (is_file($oldFile)) @unlink($oldFile);
        }
        flash('success', 'Profile updated.');
        $this->redirect('/profile');
    }

    /** Returns [filename|null, error|null]. Checks size and real MIME type (not just the file extension). */
    private function handleAvatar(?array $file): array
    {
        if (!$file || $file['error'] === UPLOAD_ERR_NO_FILE) return [null, null];
        if ($file['error'] !== UPLOAD_ERR_OK) return [null, 'Upload failed, please try again.'];
        if ($file['size'] > config('upload.max_size')) return [null, 'Photo must be 2 MB or smaller.'];

        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
        $allowed = config('upload.types');
        if (!isset($allowed[$mime]) || @getimagesize($file['tmp_name']) === false) {
            return [null, 'Only JPG, PNG or WebP images are allowed.'];
        }
        $dir = APP_ROOT . '/public/uploads/avatars';
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        $name = bin2hex(random_bytes(10)) . '.' . $allowed[$mime];
        if (!move_uploaded_file($file['tmp_name'], $dir . '/' . $name)) return [null, 'Could not save the photo.'];
        return [$name, null];
    }

    public function changePassword(): void
    {
        $this->requireLogin();
        $user = Auth::user();
        $v = new Validator($_POST);
        $v->required('current_password', 'Current password')->required('new_password', 'New password')
          ->min('new_password', 8, 'New password')->password('new_password', 'New password')->matches('new_password_confirm', 'new_password', 'Password confirmation');
        $errors = $v->errors;
        if (!isset($errors['current_password']) && !password_verify((string)$_POST['current_password'], $user['password_hash'])) {
            $errors['current_password'] = 'Current password is wrong.';
        }
        if ($errors) $this->withErrors($errors, [], '/profile');

        User::updatePassword((int)$user['id'], password_hash($_POST['new_password'], PASSWORD_DEFAULT));
        flash('success', 'Password changed.');
        $this->redirect('/profile');
    }

    /** Fetch action: saves dark mode per user. */
    public function toggleTheme(): void
    {
        $this->requireLogin();
        $on = ($_POST['dark'] ?? '0') === '1';
        User::setDarkMode(Auth::id(), $on);
        $this->json(['ok' => true, 'dark' => $on]);
    }
}
