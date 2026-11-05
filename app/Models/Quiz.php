<?php
class Quiz
{
    public const QUESTIONS_PER_QUIZ = 10;
    public const WEAK_THRESHOLD = 0.6;   // below 60% accuracy = weak topic
    public const MIN_ANSWERS = 3;        // need at least 3 answers on a topic before judging it

    public static function randomQuestions(int $subjectId, int $topicId, int $limit = self::QUESTIONS_PER_QUIZ): array
    {
        $sql = 'SELECT q.id, q.question_text, q.option_a, q.option_b, q.option_c, q.option_d, q.topic_id
                FROM questions q JOIN topics t ON t.id = q.topic_id WHERE t.subject_id = ?';
        $p = [$subjectId];
        if ($topicId > 0) { $sql .= ' AND q.topic_id = ?'; $p[] = $topicId; }
        return Database::all($sql . ' ORDER BY RAND() LIMIT ' . (int)$limit, $p);
    }

    public static function questionsByIds(array $ids): array
    {
        if (!$ids) return [];
        $in = implode(',', array_fill(0, count($ids), '?'));
        $rows = Database::all(
            'SELECT q.*, t.name AS topic_name FROM questions q JOIN topics t ON t.id = q.topic_id WHERE q.id IN (' . $in . ')',
            array_values($ids)
        );
        $byId = [];
        foreach ($rows as $r) $byId[$r['id']] = $r;
        return $byId;
    }

    /** Saves an attempt and its answers in one transaction. $selected = [questionId => 'A'|'B'|...] */
    public static function saveAttempt(int $userId, int $subjectId, array $questionIds, array $selected): int
    {
        $questions = self::questionsByIds($questionIds);
        $pdo = Database::pdo();
        $pdo->beginTransaction();
        try {
            $score = 0;
            $rows = [];
            foreach ($questionIds as $qid) {
                if (!isset($questions[$qid])) continue;
                $pick = strtoupper((string)($selected[$qid] ?? ''));
                $pick = in_array($pick, ['A', 'B', 'C', 'D'], true) ? $pick : null;
                $ok = $pick !== null && $pick === $questions[$qid]['correct_option'] ? 1 : 0;
                $score += $ok;
                $rows[] = [$qid, $pick, $ok];
            }
            $attemptId = Database::insert(
                'INSERT INTO quiz_attempts (user_id, subject_id, score, total) VALUES (?,?,?,?)',
                [$userId, $subjectId, $score, count($rows)]
            );
            foreach ($rows as [$qid, $pick, $ok]) {
                Database::execute(
                    'INSERT INTO attempt_answers (attempt_id, question_id, selected_option, is_correct) VALUES (?,?,?,?)',
                    [$attemptId, $qid, $pick, $ok]
                );
            }
            $pdo->commit();
            return $attemptId;
        } catch (Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public static function attempt(int $id, int $userId): ?array
    {
        return Database::one(
            'SELECT a.*, s.name AS subject_name FROM quiz_attempts a JOIN subjects s ON s.id = a.subject_id WHERE a.id = ? AND a.user_id = ?',
            [$id, $userId]
        );
    }

    public static function attemptAnswers(int $attemptId): array
    {
        return Database::all(
            'SELECT aa.selected_option, aa.is_correct, q.id AS question_id, q.question_text, q.option_a, q.option_b, q.option_c, q.option_d,
                    q.correct_option, q.explanation, t.name AS topic_name
             FROM attempt_answers aa JOIN questions q ON q.id = aa.question_id JOIN topics t ON t.id = q.topic_id
             WHERE aa.attempt_id = ? ORDER BY aa.id',
            [$attemptId]
        );
    }

    public static function countAttempts(int $userId): int
    {
        return (int)Database::value('SELECT COUNT(*) FROM quiz_attempts WHERE user_id = ?', [$userId]);
    }

    public static function attempts(int $userId, int $limit, int $offset): array
    {
        return Database::all(
            'SELECT a.*, s.name AS subject_name FROM quiz_attempts a JOIN subjects s ON s.id = a.subject_id
             WHERE a.user_id = ? ORDER BY a.id DESC LIMIT ' . (int)$limit . ' OFFSET ' . (int)$offset,
            [$userId]
        );
    }

    public static function allAttempts(int $userId): array
    {
        return Database::all(
            'SELECT a.id, a.created_at, s.name AS subject_name, a.score, a.total FROM quiz_attempts a
             JOIN subjects s ON s.id = a.subject_id WHERE a.user_id = ? ORDER BY a.id',
            [$userId]
        );
    }

    /** Last 20 attempts, oldest first, for the progress chart. */
    public static function progress(int $userId): array
    {
        $rows = Database::all(
            'SELECT a.id, a.created_at, s.name AS subject_name, ROUND(a.score / a.total * 100) AS percent
             FROM quiz_attempts a JOIN subjects s ON s.id = a.subject_id
             WHERE a.user_id = ? AND a.total > 0 ORDER BY a.id DESC LIMIT 20',
            [$userId]
        );
        return array_reverse($rows);
    }

    /** The key rule: topics where the student's accuracy is below 60% (after at least 3 answers). */
    public static function weakTopics(int $userId, int $limit = 5): array
    {
        return Database::all(
            'SELECT t.id, t.name AS topic_name, t.subject_id, s.name AS subject_name,
                    COUNT(*) AS answered, SUM(aa.is_correct) AS correct,
                    ROUND(SUM(aa.is_correct) / COUNT(*) * 100) AS percent
             FROM attempt_answers aa
             JOIN quiz_attempts a ON a.id = aa.attempt_id
             JOIN questions q ON q.id = aa.question_id
             JOIN topics t ON t.id = q.topic_id
             JOIN subjects s ON s.id = t.subject_id
             WHERE a.user_id = ?
             GROUP BY t.id, t.name, t.subject_id, s.name
             HAVING COUNT(*) >= ' . self::MIN_ANSWERS . ' AND SUM(aa.is_correct) / COUNT(*) < ' . self::WEAK_THRESHOLD . '
             ORDER BY percent ASC LIMIT ' . (int)$limit,
            [$userId]
        );
    }

    // ---- teacher portal: how students do on a teacher's questions ----
    /** $teacherId = null means every question (used when an admin opens the teacher portal). */
    private static function authorFilter(?int $teacherId): array
    {
        return $teacherId === null ? ['', []] : [' WHERE q.created_by = ?', [$teacherId]];
    }

    public static function teacherSummary(?int $teacherId): array
    {
        [$w, $p] = self::authorFilter($teacherId);
        return Database::one(
            'SELECT COUNT(*) AS answers, COUNT(DISTINCT a.user_id) AS students, COALESCE(ROUND(SUM(aa.is_correct) / COUNT(*) * 100), 0) AS percent
             FROM attempt_answers aa JOIN quiz_attempts a ON a.id = aa.attempt_id JOIN questions q ON q.id = aa.question_id' . $w,
            $p
        ) ?? ['answers' => 0, 'students' => 0, 'percent' => 0];
    }

    /** Accuracy per topic, weakest first, so a teacher sees where the class struggles. */
    public static function teacherTopicStats(?int $teacherId, int $limit = 8): array
    {
        [$w, $p] = self::authorFilter($teacherId);
        return Database::all(
            'SELECT t.name AS topic_name, s.name AS subject_name, COUNT(*) AS answered, SUM(aa.is_correct) AS correct,
                    ROUND(SUM(aa.is_correct) / COUNT(*) * 100) AS percent
             FROM attempt_answers aa JOIN questions q ON q.id = aa.question_id
             JOIN topics t ON t.id = q.topic_id JOIN subjects s ON s.id = t.subject_id' . $w . '
             GROUP BY t.id, t.name, s.name ORDER BY percent ASC, answered DESC LIMIT ' . (int)$limit,
            $p
        );
    }

    /** Questions students get wrong most often (at least MIN_ANSWERS answers). */
    public static function hardestQuestions(?int $teacherId, int $limit = 5): array
    {
        [$w, $p] = self::authorFilter($teacherId);
        return Database::all(
            'SELECT q.id, q.question_text, COUNT(*) AS answered, ROUND(SUM(aa.is_correct) / COUNT(*) * 100) AS percent
             FROM attempt_answers aa JOIN questions q ON q.id = aa.question_id' . $w . '
             GROUP BY q.id, q.question_text HAVING COUNT(*) >= ' . self::MIN_ANSWERS . '
             ORDER BY percent ASC LIMIT ' . (int)$limit,
            $p
        );
    }

    // ---- question bank (teachers/admins) ----
    private static function qWhere(int $topicId): array
    {
        return $topicId > 0 ? [' WHERE q.topic_id = ?', [$topicId]] : ['', []];
    }

    public static function countQuestions(int $topicId): int
    {
        [$w, $p] = self::qWhere($topicId);
        return (int)Database::value('SELECT COUNT(*) FROM questions q' . $w, $p);
    }

    public static function listQuestions(int $topicId, int $limit, int $offset): array
    {
        [$w, $p] = self::qWhere($topicId);
        return Database::all(
            'SELECT q.id, q.question_text, q.correct_option, q.created_by, t.name AS topic_name, s.name AS subject_name
             FROM questions q JOIN topics t ON t.id = q.topic_id JOIN subjects s ON s.id = t.subject_id' . $w .
            ' ORDER BY q.id DESC LIMIT ' . (int)$limit . ' OFFSET ' . (int)$offset,
            $p
        );
    }

    public static function findQuestion(int $id): ?array
    {
        return Database::one('SELECT * FROM questions WHERE id = ?', [$id]);
    }

    public static function createQuestion(int $userId, array $d): int
    {
        return Database::insert(
            'INSERT INTO questions (topic_id, created_by, question_text, option_a, option_b, option_c, option_d, correct_option, explanation)
             VALUES (?,?,?,?,?,?,?,?,?)',
            [(int)$d['topic_id'], $userId, trim($d['question_text']), trim($d['option_a']), trim($d['option_b']),
             trim($d['option_c']), trim($d['option_d']), strtoupper($d['correct_option']), trim($d['explanation'] ?? '') ?: null]
        );
    }

    public static function deleteQuestion(int $id): void
    {
        Database::execute('DELETE FROM questions WHERE id = ?', [$id]);
    }
}
