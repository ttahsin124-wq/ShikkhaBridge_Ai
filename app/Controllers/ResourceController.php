<?php
class ResourceController extends Controller
{
    public function index(): void
    {
        $this->requireLogin();
        $q = trim($_GET['q'] ?? '');
        $subjectId = (int)($_GET['subject_id'] ?? 0);
        $pager = paginate(Resource::count($q, $subjectId));
        $this->view('resources/index', [
            'title' => 'Learning resources',
            'items' => Resource::search($q, $subjectId, $pager['perPage'], $pager['offset']),
            'pager' => $pager,
            'subjects' => Catalog::subjects(),
            'q' => $q,
            'subjectId' => $subjectId,
        ]);
    }

    public function show(int $id): void
    {
        $this->requireLogin();
        $r = Resource::find($id) ?? $this->abort(404);
        $this->view('resources/show', ['title' => $r['title'], 'r' => $r, 'canManage' => self::canManage($r)]);
    }

    public function create(): void
    {
        $this->requirePortal('teacher');
        $this->view('resources/form', ['title' => 'Add resource', 'r' => null, 'subjects' => Catalog::subjects(), 'errors' => $this->errors()]);
    }

    public function store(): void
    {
        $this->requirePortal('teacher');
        $errors = $this->validate($_POST);
        if ($errors) $this->withErrors($errors, $_POST, '/teacher/resources/create');
        $id = Resource::create(Auth::id(), (int)$_POST['subject_id'], trim($_POST['title']), trim($_POST['description'] ?? ''), trim($_POST['content']), trim($_POST['url'] ?? ''));
        flash('success', 'Resource published.');
        $this->redirect('/resources/' . $id);
    }

    public function edit(int $id): void
    {
        $this->requirePortal('teacher');
        $r = Resource::find($id) ?? $this->abort(404);
        if (!self::canManage($r)) $this->abort(403);
        $this->view('resources/form', ['title' => 'Edit resource', 'r' => $r, 'subjects' => Catalog::subjects(), 'errors' => $this->errors()]);
    }

    public function update(int $id): void
    {
        $this->requirePortal('teacher');
        $r = Resource::find($id) ?? $this->abort(404);
        if (!self::canManage($r)) $this->abort(403);
        $errors = $this->validate($_POST);
        if ($errors) $this->withErrors($errors, $_POST, "/teacher/resources/$id/edit");
        Resource::update($id, (int)$_POST['subject_id'], trim($_POST['title']), trim($_POST['description'] ?? ''), trim($_POST['content']), trim($_POST['url'] ?? ''));
        flash('success', 'Resource updated.');
        $this->redirect("/resources/$id");
    }

    /** Fetch action (with loading indicator in the browser). */
    public function delete(int $id): void
    {
        $this->requirePortal('teacher');
        $r = Resource::find($id) ?? $this->abort(404, 'Resource not found.');
        if (!self::canManage($r)) $this->abort(403, 'Only the author or an admin can delete this resource.');
        Resource::delete($id);
        if ($this->isAjax()) $this->json(['ok' => true, 'message' => 'Resource deleted.']);
        flash('success', 'Resource deleted.');
        $this->redirect('/teacher/resources');
    }

    /** Public so views can ask the same question the controller does. */
    public static function canManage(array $r): bool
    {
        return Auth::hasRole('admin') || (Auth::hasRole('teacher') && (int)$r['user_id'] === Auth::id());
    }

    private function validate(array $d): array
    {
        $v = new Validator($d);
        $v->required('title', 'Title')->max('title', 200, 'Title')->max('description', 500, 'Description')
          ->required('content', 'Content')->max('url', 255, 'Link');
        $errors = $v->errors;
        if (!Catalog::subject((int)($d['subject_id'] ?? 0))) $errors['subject_id'] = 'Please choose a subject.';
        $url = trim($d['url'] ?? '');
        if ($url !== '' && (!filter_var($url, FILTER_VALIDATE_URL) || !preg_match('#^https?://#i', $url))) {
            $errors['url'] = 'Link must start with http:// or https://';
        }
        return $errors;
    }
}
