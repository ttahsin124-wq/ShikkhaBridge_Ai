<?php
class Auth
{
    public const ROLES = ['student', 'teacher', 'admin'];
    public const STATUSES = ['pending', 'active', 'suspended'];

    public static function user(): ?array
    {
        static $user = false;
        if ($user === false) {
            $user = isset($_SESSION['uid']) ? User::find((int)$_SESSION['uid']) : null;
            // a suspended (or not yet approved) account is signed out on its next request
            if ($user !== null && $user['status'] !== 'active') $user = null;
            if ($user === null) unset($_SESSION['uid']);
        }
        return $user;
    }

    public static function check(): bool { return self::user() !== null; }
    public static function id(): ?int { return self::user()['id'] ?? null; }

    public static function hasRole(string ...$roles): bool
    {
        $u = self::user();
        return $u !== null && in_array($u['role'], $roles, true);
    }

    /** Can the current user open this portal? Admins can open every portal. */
    public static function canUsePortal(string $portal): bool
    {
        return self::hasRole($portal, 'admin');
    }

    public static function login(array $user): void
    {
        session_regenerate_id(true);
        $_SESSION['uid'] = (int)$user['id'];
    }

    public static function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
    }

    // ---- login attempt limiting ----
    public static function tooManyAttempts(string $email): bool
    {
        $minutes = (int)config('lockout_minutes', 15);
        $count = (int)Database::value(
            'SELECT COUNT(*) FROM login_attempts WHERE email = ? AND attempted_at > (NOW() - INTERVAL ' . $minutes . ' MINUTE)',
            [strtolower($email)]
        );
        return $count >= (int)config('max_login_attempts', 5);
    }

    public static function recordFailure(string $email): void
    {
        Database::execute('INSERT INTO login_attempts (email, ip) VALUES (?, ?)', [strtolower($email), $_SERVER['REMOTE_ADDR'] ?? '']);
    }

    public static function clearFailures(string $email): void
    {
        Database::execute('DELETE FROM login_attempts WHERE email = ?', [strtolower($email)]);
    }
}
