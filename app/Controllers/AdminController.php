<?php
/** Admin portal: oversees both the student and the teacher portals. */
class AdminController extends Controller
{
    public function index(): void
    {
        $this->requireRole('admin');
        $count = fn(string $sql) => (int)Database::value($sql);
        $stats = [
            'students' => $count("SELECT COUNT(*) FROM users WHERE role = 'student'"),
            'teachers' => $count("SELECT COUNT(*) FROM users WHERE role = 'teacher' AND status = 'active'"),
            'pending' => $count("SELECT COUNT(*) FROM users WHERE status = 'pending'"),
            'suspended' => $count("SELECT COUNT(*) FROM users WHERE status = 'suspended'"),
            'new_users' => $count('SELECT COUNT(*) FROM users WHERE created_at > NOW() - INTERVAL 7 DAY'),
            'resources' => $count('SELECT COUNT(*) FROM resources'),
            'questions' => $count('SELECT COUNT(*) FROM questions'),
            'attempts' => $count('SELECT COUNT(*) FROM quiz_attempts'),
            'attempts_week' => $count('SELECT COUNT(*) FROM quiz_attempts WHERE created_at > NOW() - INTERVAL 7 DAY'),
            'groups' => $count('SELECT COUNT(*) FROM study_groups'),
        ];
        $roles = Database::all('SELECT role, COUNT(*) AS n FROM users GROUP BY role');
        $perDay = Database::all(
            'SELECT DATE(created_at) AS day, COUNT(*) AS n FROM quiz_attempts
             WHERE created_at > NOW() - INTERVAL 7 DAY GROUP BY DATE(created_at) ORDER BY day'
        );
        $this->view('admin/index', [
            'title' => 'Admin dashboard',
            'useChart' => true,
            'stats' => $stats,
            'roles' => $roles,
            'perDay' => $perDay,
            'pendingTeachers' => User::search('', 5, 0, 'teacher', 'pending'),
        ]);
    }

    /** Current list filters, kept across POST actions so the admin returns to the same page. */
    private function filters(array $src): array
    {
        $role = in_array($src['role'] ?? '', Auth::ROLES, true) ? $src['role'] : '';
        $status = in_array($src['status'] ?? '', Auth::STATUSES, true) ? $src['status'] : '';
        return ['q' => trim((string)($src['q'] ?? '')), 'role' => $role, 'status' => $status, 'page' => max(1, (int)($src['page'] ?? 1))];
    }

    /** List filters come back as f_q / f_role / ... so they never clash with the posted `role` or `status` value. */
    private function backToUsers(): never
    {
        $f = $this->filters([
            'q' => $_POST['f_q'] ?? '', 'role' => $_POST['f_role'] ?? '',
            'status' => $_POST['f_status'] ?? '', 'page' => $_POST['f_page'] ?? 1,
        ]);
        if ($f['page'] === 1) unset($f['page']);
        $this->redirect('/admin/users?' . http_build_query(array_filter($f)));
    }

    public function users(): void
    {
        $this->requireRole('admin');
        $f = $this->filters($_GET);
        $pager = paginate(User::count($f['q'], $f['role'], $f['status']));
        $this->view('admin/users', [
            'title' => 'Manage users',
            'users' => User::search($f['q'], $pager['perPage'], $pager['offset'], $f['role'], $f['status']),
            'pager' => $pager,
            'f' => $f,
            'pendingCount' => User::count('', '', 'pending'),
            'errors' => $this->errors(),
        ]);
    }

    /** Admin creates an account directly (e.g. a teacher who does not need to wait for approval). */
    public function createUser(): void
    {
        $this->requireRole('admin');
        $d = $_POST;
        $v = new Validator($d);
        $v->required('name', 'Name')->max('name', 100, 'Name')
          ->required('email', 'Email')->email('email')->max('email', 150, 'Email')
          ->required('password', 'Password')->min('password', 8, 'Password')->password('password')
          ->in('role', Auth::ROLES, 'Role');
        $errors = $v->errors;
        if (!$errors && User::findByEmail(trim($d['email']))) $errors['email'] = 'This email is already registered.';
        if ($errors) $this->withErrors($errors, ['name' => $d['name'] ?? '', 'email' => $d['email'] ?? '', 'role' => $d['role'] ?? ''], '/admin/users#add-user');

        User::create(trim($d['name']), trim($d['email']), password_hash($d['password'], PASSWORD_DEFAULT), $d['role'], 'active');
        flash('success', ucfirst($d['role']) . ' account created for ' . trim($d['name']) . '.');
        $this->redirect('/admin/users?role=' . $d['role']);
    }

    public function setRole(int $id): void
    {
        $this->requireRole('admin');
        $role = $_POST['role'] ?? '';
        if (!in_array($role, Auth::ROLES, true)) $this->abort(422, 'Invalid role.');
        if ($id === Auth::id()) {
            flash('warning', "You can't change your own role.");
        } elseif (User::find($id)) {
            User::setRole($id, $role);
            flash('success', 'Role updated.');
        }
        $this->backToUsers();
    }

    /** Approve a pending teacher, suspend an account, or reactivate it. */
    public function setStatus(int $id): void
    {
        $this->requireRole('admin');
        $status = $_POST['status'] ?? '';
        if (!in_array($status, ['active', 'suspended'], true)) $this->abort(422, 'Invalid status.');
        $user = User::find($id);
        if ($id === Auth::id()) {
            flash('warning', "You can't change your own account status.");
        } elseif ($user) {
            User::setStatus($id, $status);
            flash('success', match (true) {
                $status === 'suspended' => $user['name'] . ' has been suspended.',
                $user['status'] === 'pending' => $user['name'] . ' is approved and can now log in.',
                default => $user['name'] . ' is active again.',
            });
        }
        $this->backToUsers();
    }

    public function deleteUser(int $id): void
    {
        $this->requireRole('admin');
        if ($id === Auth::id()) {
            flash('warning', "You can't delete your own account.");
        } elseif (User::find($id)) {
            User::delete($id);
            flash('success', 'User deleted.');
        }
        $this->backToUsers();
    }

    public function exportUsers(): void
    {
        $this->requireRole('admin');
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="users.csv"');
        $out = fopen('php://output', 'w');
        fputcsv($out, ['ID', 'Name', 'Email', 'Role', 'Status', 'Joined']);
        foreach (User::all() as $u) fputcsv($out, [$u['id'], $u['name'], $u['email'], $u['role'], $u['status'], $u['created_at']]);
        fclose($out);
        exit;
    }
}
