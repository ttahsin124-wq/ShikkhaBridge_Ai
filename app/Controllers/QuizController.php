<?php
class QuizController extends Controller
{
    public function index(): void
    {
        $this->requirePortal('student');
        $this->view('quiz/index', ['title' => 'Take a quiz', 'subjects' => Catalog::subjects(), 'topics' => Catalog::topics()]);
    }

    public function start(): void
    {
        $this->requirePortal('student');
        $subjectId = (int)($_GET['subject_id'] ?? 0);
        $topicId = (int)($_GET['topic_id'] ?? 0);
        if (!Catalog::subject($subjectId)) {
            flash('warning', 'Please choose a subject first.');
            $this->redirect('/student/quiz');
        }
        $questions = Quiz::randomQuestions($subjectId, $topicId);
        if (!$questions) {
            flash('warning', 'No questions available for that choice yet.');
            $this->redirect('/student/quiz');
        }
        $_SESSION['quiz'] = ['subject_id' => $subjectId, 'ids' => array_map('intval', array_column($questions, 'id'))];
        $this->view('quiz/take', ['title' => 'Quiz', 'questions' => $questions, 'subject' => Catalog::subject($subjectId)]);
    }

    public function submit(): void
    {
        $this->requirePortal('student');
        $quiz = $_SESSION['quiz'] ?? null;
        if (!$quiz) {
            flash('warning', 'That quiz has expired. Please start a new one.');
            $this->redirect('/student/quiz');
        }
        unset($_SESSION['quiz']);
        $selected = is_array($_POST['answers'] ?? null) ? $_POST['answers'] : [];
        $attemptId = Quiz::saveAttempt(Auth::id(), (int)$quiz['subject_id'], $quiz['ids'], $selected);
        $this->redirect('/student/quiz/result/' . $attemptId);
    }

    public function result(int $id): void
    {
        $this->requirePortal('student');
        $attempt = Quiz::attempt($id, Auth::id()) ?? $this->abort(404);
        $this->view('quiz/result', ['title' => 'Quiz result', 'attempt' => $attempt, 'answers' => Quiz::attemptAnswers($id)]);
    }

    public function history(): void
    {
        $this->requirePortal('student');
        $pager = paginate(Quiz::countAttempts(Auth::id()));
        $this->view('quiz/history', [
            'title' => 'Quiz history',
            'attempts' => Quiz::attempts(Auth::id(), $pager['perPage'], $pager['offset']),
            'pager' => $pager,
        ]);
    }

    public function exportCsv(): void
    {
        $this->requirePortal('student');
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="my-quiz-history.csv"');
        $out = fopen('php://output', 'w');
        fputcsv($out, ['Attempt', 'Date', 'Subject', 'Score', 'Total', 'Percent']);
        foreach (Quiz::allAttempts(Auth::id()) as $a) {
            $pct = $a['total'] > 0 ? round($a['score'] / $a['total'] * 100) : 0;
            fputcsv($out, [$a['id'], $a['created_at'], $a['subject_name'], $a['score'], $a['total'], $pct . '%']);
        }
        fclose($out);
        exit;
    }

    /** JSON API used by the dashboard chart. */
    public function progressApi(): void
    {
        $this->requirePortal('student');
        $this->json(['ok' => true, 'data' => Quiz::progress(Auth::id())]);
    }
}
