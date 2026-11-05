<?php
class QuestionController extends Controller
{
    public function index(): void
    {
        $this->requirePortal('teacher');
        $topicId = (int)($_GET['topic_id'] ?? 0);
        $pager = paginate(Quiz::countQuestions($topicId));
        $this->view('questions/index', [
            'title' => 'Question bank',
            'questions' => Quiz::listQuestions($topicId, $pager['perPage'], $pager['offset']),
            'pager' => $pager,
            'topics' => Catalog::topics(),
            'topicId' => $topicId,
            'errors' => $this->errors(),
        ]);
    }

    public function store(): void
    {
        $this->requirePortal('teacher');
        $d = $_POST;
        $v = new Validator($d);
        $v->required('question_text', 'Question')->max('question_text', 500, 'Question')
          ->required('option_a', 'Option A')->required('option_b', 'Option B')
          ->required('option_c', 'Option C')->required('option_d', 'Option D')
          ->in('correct_option', ['A', 'B', 'C', 'D'], 'Correct answer')->max('explanation', 500, 'Explanation');
        $errors = $v->errors;
        if (!Database::one('SELECT id FROM topics WHERE id = ?', [(int)($d['topic_id'] ?? 0)])) $errors['topic_id'] = 'Please choose a topic.';
        if ($errors) $this->withErrors($errors, $d, '/teacher/questions');
        Quiz::createQuestion(Auth::id(), $d);
        flash('success', 'Question added.');
        $this->redirect('/teacher/questions');
    }

    public function delete(int $id): void
    {
        $this->requirePortal('teacher');
        $q = Quiz::findQuestion($id) ?? $this->abort(404, 'Question not found.');
        if (!Auth::hasRole('admin') && (int)$q['created_by'] !== Auth::id()) {
            $this->abort(403, 'Only the author or an admin can delete this question.');
        }
        Quiz::deleteQuestion($id);
        if ($this->isAjax()) $this->json(['ok' => true, 'message' => 'Question deleted.']);
        $this->redirect('/teacher/questions');
    }
}
