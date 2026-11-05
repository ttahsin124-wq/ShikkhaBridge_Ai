<?php
class Resource
{
    private static function where(string $q, int $subjectId, int $userId = 0): array
    {
        $w = [];
        $p = [];
        if ($q !== '') { $w[] = '(r.title LIKE ? OR r.description LIKE ?)'; $p[] = "%$q%"; $p[] = "%$q%"; }
        if ($subjectId > 0) { $w[] = 'r.subject_id = ?'; $p[] = $subjectId; }
        if ($userId > 0) { $w[] = 'r.user_id = ?'; $p[] = $userId; }
        return [$w ? ' WHERE ' . implode(' AND ', $w) : '', $p];
    }

    public static function count(string $q, int $subjectId, int $userId = 0): int
    {
        [$w, $p] = self::where($q, $subjectId, $userId);
        return (int)Database::value('SELECT COUNT(*) FROM resources r' . $w, $p);
    }

    public static function search(string $q, int $subjectId, int $limit, int $offset, int $userId = 0): array
    {
        [$w, $p] = self::where($q, $subjectId, $userId);
        return Database::all(
            'SELECT r.id, r.user_id, r.title, r.description, r.created_at, s.name AS subject_name, u.name AS author
             FROM resources r JOIN subjects s ON s.id = r.subject_id JOIN users u ON u.id = r.user_id' . $w .
            ' ORDER BY r.id DESC LIMIT ' . (int)$limit . ' OFFSET ' . (int)$offset,
            $p
        );
    }

    public static function find(int $id): ?array
    {
        return Database::one(
            'SELECT r.*, s.name AS subject_name, u.name AS author FROM resources r
             JOIN subjects s ON s.id = r.subject_id JOIN users u ON u.id = r.user_id WHERE r.id = ?',
            [$id]
        );
    }

    public static function create(int $userId, int $subjectId, string $title, string $desc, string $content, string $url): int
    {
        return Database::insert(
            'INSERT INTO resources (user_id, subject_id, title, description, content, url) VALUES (?,?,?,?,?,?)',
            [$userId, $subjectId, $title, $desc, $content, $url !== '' ? $url : null]
        );
    }

    public static function update(int $id, int $subjectId, string $title, string $desc, string $content, string $url): void
    {
        Database::execute(
            'UPDATE resources SET subject_id=?, title=?, description=?, content=?, url=? WHERE id=?',
            [$subjectId, $title, $desc, $content, $url !== '' ? $url : null, $id]
        );
    }

    public static function delete(int $id): void
    {
        Database::execute('DELETE FROM resources WHERE id=?', [$id]);
    }

    public static function forSubjects(array $subjectIds, int $limit = 4): array
    {
        if (!$subjectIds) return [];
        $in = implode(',', array_fill(0, count($subjectIds), '?'));
        return Database::all(
            'SELECT id, title, description FROM resources WHERE subject_id IN (' . $in . ') ORDER BY id DESC LIMIT ' . (int)$limit,
            array_values($subjectIds)
        );
    }
}
