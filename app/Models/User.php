<?php
class User
{
    public static function find(int $id): ?array
    {
        return Database::one('SELECT * FROM users WHERE id = ?', [$id]);
    }

    public static function findByEmail(string $email): ?array
    {
        return Database::one('SELECT * FROM users WHERE email = ?', [strtolower($email)]);
    }

    public static function create(string $name, string $email, string $hash, string $role = 'student', string $status = 'active'): int
    {
        return Database::insert(
            'INSERT INTO users (name, email, password_hash, role, status, dark_mode) VALUES (?, ?, ?, ?, ?, 1)',
            [$name, strtolower($email), $hash, $role, $status]
        );
    }

    public static function updateProfile(int $id, string $name, string $bio, string $lang, ?string $avatar): void
    {
        if ($avatar !== null) {
            Database::execute('UPDATE users SET name=?, bio=?, language_pref=?, avatar=? WHERE id=?', [$name, $bio, $lang, $avatar, $id]);
        } else {
            Database::execute('UPDATE users SET name=?, bio=?, language_pref=? WHERE id=?', [$name, $bio, $lang, $id]);
        }
    }

    public static function updatePassword(int $id, string $hash): void
    {
        Database::execute('UPDATE users SET password_hash=? WHERE id=?', [$hash, $id]);
    }

    public static function setDarkMode(int $id, bool $on): void
    {
        Database::execute('UPDATE users SET dark_mode=? WHERE id=?', [$on ? 1 : 0, $id]);
    }

    public static function setRole(int $id, string $role): void
    {
        Database::execute('UPDATE users SET role=? WHERE id=?', [$role, $id]);
    }

    public static function setStatus(int $id, string $status): void
    {
        Database::execute('UPDATE users SET status=? WHERE id=?', [$status, $id]);
    }

    public static function delete(int $id): void
    {
        Database::execute('DELETE FROM users WHERE id=?', [$id]);
    }

    /** Admin user list filters: free-text search, role and status. */
    private static function where(string $q, string $role = '', string $status = ''): array
    {
        $w = [];
        $p = [];
        if ($q !== '') { $w[] = '(name LIKE ? OR email LIKE ?)'; $p[] = "%$q%"; $p[] = "%$q%"; }
        if ($role !== '') { $w[] = 'role = ?'; $p[] = $role; }
        if ($status !== '') { $w[] = 'status = ?'; $p[] = $status; }
        return [$w ? ' WHERE ' . implode(' AND ', $w) : '', $p];
    }

    public static function count(string $q = '', string $role = '', string $status = ''): int
    {
        [$w, $p] = self::where($q, $role, $status);
        return (int)Database::value('SELECT COUNT(*) FROM users' . $w, $p);
    }

    public static function search(string $q, int $limit, int $offset, string $role = '', string $status = ''): array
    {
        [$w, $p] = self::where($q, $role, $status);
        return Database::all('SELECT id, name, email, role, status, created_at FROM users' . $w . ' ORDER BY id DESC LIMIT ' . (int)$limit . ' OFFSET ' . (int)$offset, $p);
    }

    public static function all(): array
    {
        return Database::all('SELECT id, name, email, role, status, created_at FROM users ORDER BY id');
    }

    // ---- password reset ----
    public static function createResetToken(int $userId): string
    {
        $token = bin2hex(random_bytes(32));
        Database::execute('UPDATE password_resets SET used=1 WHERE user_id=?', [$userId]);
        Database::execute(
            'INSERT INTO password_resets (user_id, token_hash, expires_at) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 1 HOUR))',
            [$userId, hash('sha256', $token)]
        );
        return $token;
    }

    public static function findValidReset(string $token): ?array
    {
        return Database::one(
            'SELECT * FROM password_resets WHERE token_hash=? AND used=0 AND expires_at > NOW()',
            [hash('sha256', $token)]
        );
    }

    public static function markResetUsed(int $id): void
    {
        Database::execute('UPDATE password_resets SET used=1 WHERE id=?', [$id]);
    }
}
