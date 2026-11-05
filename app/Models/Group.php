<?php
class Group
{
    private static function where(string $q, int $subjectId): array
    {
        $w = [];
        $p = [];
        if ($q !== '') { $w[] = '(g.name LIKE ? OR g.description LIKE ?)'; $p[] = "%$q%"; $p[] = "%$q%"; }
        if ($subjectId > 0) { $w[] = 'g.subject_id = ?'; $p[] = $subjectId; }
        return [$w ? ' WHERE ' . implode(' AND ', $w) : '', $p];
    }

    public static function count(string $q, int $subjectId): int
    {
        [$w, $p] = self::where($q, $subjectId);
        return (int)Database::value('SELECT COUNT(*) FROM study_groups g' . $w, $p);
    }

    public static function search(string $q, int $subjectId, int $limit, int $offset): array
    {
        [$w, $p] = self::where($q, $subjectId);
        return Database::all(
            'SELECT g.*, s.name AS subject_name, u.name AS owner_name,
                    (SELECT COUNT(*) FROM group_members m WHERE m.group_id = g.id AND m.status = "approved") AS member_count
             FROM study_groups g JOIN subjects s ON s.id = g.subject_id JOIN users u ON u.id = g.owner_id' . $w .
            ' ORDER BY g.id DESC LIMIT ' . (int)$limit . ' OFFSET ' . (int)$offset,
            $p
        );
    }

    public static function find(int $id): ?array
    {
        return Database::one(
            'SELECT g.*, s.name AS subject_name, u.name AS owner_name,
                    (SELECT COUNT(*) FROM group_members m WHERE m.group_id = g.id AND m.status = "approved") AS member_count
             FROM study_groups g JOIN subjects s ON s.id = g.subject_id JOIN users u ON u.id = g.owner_id WHERE g.id = ?',
            [$id]
        );
    }

    public static function create(int $ownerId, int $subjectId, string $name, string $desc, string $time, int $max): int
    {
        $pdo = Database::pdo();
        $pdo->beginTransaction();
        try {
            $id = Database::insert(
                'INSERT INTO study_groups (owner_id, subject_id, name, description, meeting_time, max_members) VALUES (?,?,?,?,?,?)',
                [$ownerId, $subjectId, $name, $desc ?: null, $time ?: null, $max]
            );
            Database::execute('INSERT INTO group_members (group_id, user_id, status) VALUES (?,?,"approved")', [$id, $ownerId]);
            $pdo->commit();
            return $id;
        } catch (Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public static function membership(int $groupId, int $userId): ?array
    {
        return Database::one('SELECT * FROM group_members WHERE group_id=? AND user_id=?', [$groupId, $userId]);
    }

    public static function members(int $groupId, string $status): array
    {
        return Database::all(
            'SELECT m.id AS membership_id, u.id AS user_id, u.name FROM group_members m JOIN users u ON u.id = m.user_id
             WHERE m.group_id = ? AND m.status = ? ORDER BY m.id',
            [$groupId, $status]
        );
    }

    /** Join request. Returns false if the user already asked or is a member (UNIQUE key protects against double requests). */
    public static function requestJoin(int $groupId, int $userId): bool
    {
        if (self::membership($groupId, $userId)) return false;
        Database::execute('INSERT INTO group_members (group_id, user_id, status) VALUES (?,?,"pending")', [$groupId, $userId]);
        return true;
    }

    /** Owner approves a request. Seat limit rule: cannot exceed max_members. */
    public static function approve(int $membershipId, array $group): bool
    {
        $pdo = Database::pdo();
        $pdo->beginTransaction();
        try {
            // lock the group row so two approvals at once cannot exceed the limit
            Database::one('SELECT id FROM study_groups WHERE id = ? FOR UPDATE', [$group['id']]);
            $count = (int)Database::value('SELECT COUNT(*) FROM group_members WHERE group_id=? AND status="approved"', [$group['id']]);
            if ($count >= (int)$group['max_members']) {
                $pdo->rollBack();
                return false;
            }
            Database::execute('UPDATE group_members SET status="approved" WHERE id=? AND group_id=? AND status="pending"', [$membershipId, $group['id']]);
            $pdo->commit();
            return true;
        } catch (Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public static function reject(int $membershipId, int $groupId): void
    {
        Database::execute('UPDATE group_members SET status="rejected" WHERE id=? AND group_id=? AND status="pending"', [$membershipId, $groupId]);
    }

    public static function mine(int $userId): array
    {
        return Database::all(
            'SELECT g.id, g.name, s.name AS subject_name FROM group_members m
             JOIN study_groups g ON g.id = m.group_id JOIN subjects s ON s.id = g.subject_id
             WHERE m.user_id = ? AND m.status = "approved" ORDER BY g.id DESC LIMIT 5',
            [$userId]
        );
    }

    // ---- chat ----
    public static function messagesAfter(int $groupId, int $afterId): array
    {
        return Database::all(
            'SELECT gm.id, gm.body, gm.created_at, u.name, gm.user_id FROM group_messages gm JOIN users u ON u.id = gm.user_id
             WHERE gm.group_id = ? AND gm.id > ? ORDER BY gm.id ASC LIMIT 100',
            [$groupId, $afterId]
        );
    }

    public static function lastMessages(int $groupId, int $limit = 50): array
    {
        $rows = Database::all(
            'SELECT gm.id, gm.body, gm.created_at, u.name, gm.user_id FROM group_messages gm JOIN users u ON u.id = gm.user_id
             WHERE gm.group_id = ? ORDER BY gm.id DESC LIMIT ' . (int)$limit,
            [$groupId]
        );
        return array_reverse($rows);
    }

    public static function addMessage(int $groupId, int $userId, string $body): int
    {
        return Database::insert('INSERT INTO group_messages (group_id, user_id, body) VALUES (?,?,?)', [$groupId, $userId, $body]);
    }
}
