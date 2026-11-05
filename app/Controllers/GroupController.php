<?php
class GroupController extends Controller
{
    public function index(): void
    {
        $this->requireLogin();
        $q = trim($_GET['q'] ?? '');
        $subjectId = (int)($_GET['subject_id'] ?? 0);
        $pager = paginate(Group::count($q, $subjectId), 9);
        $this->view('groups/index', [
            'title' => 'Study groups',
            'groups' => Group::search($q, $subjectId, $pager['perPage'], $pager['offset']),
            'pager' => $pager,
            'subjects' => Catalog::subjects(),
            'q' => $q,
            'subjectId' => $subjectId,
        ]);
    }

    public function create(): void
    {
        $this->requireLogin();
        $this->view('groups/form', ['title' => 'Create study group', 'subjects' => Catalog::subjects(), 'errors' => $this->errors()]);
    }

    public function store(): void
    {
        $this->requireLogin();
        $d = $_POST;
        $v = new Validator($d);
        $v->required('name', 'Group name')->max('name', 150, 'Group name')->max('description', 500, 'Description')->max('meeting_time', 100, 'Meeting time');
        $errors = $v->errors;
        $max = (int)($d['max_members'] ?? 0);
        if ($max < 2 || $max > 50) $errors['max_members'] = 'Group size must be between 2 and 50.';
        if (!Catalog::subject((int)($d['subject_id'] ?? 0))) $errors['subject_id'] = 'Please choose a subject.';
        if ($errors) $this->withErrors($errors, $d, '/groups/create');

        $id = Group::create(Auth::id(), (int)$d['subject_id'], trim($d['name']), trim($d['description'] ?? ''), trim($d['meeting_time'] ?? ''), $max);
        flash('success', 'Study group created.');
        $this->redirect('/groups/' . $id);
    }

    public function show(int $id): void
    {
        $this->requireLogin();
        $g = Group::find($id) ?? $this->abort(404);
        $m = Group::membership($id, Auth::id());
        $isOwner = (int)$g['owner_id'] === Auth::id();
        $isMember = $m && $m['status'] === 'approved';
        $this->view('groups/show', [
            'title' => $g['name'],
            'g' => $g,
            'membership' => $m,
            'isOwner' => $isOwner,
            'isMember' => $isMember,
            'members' => Group::members($id, 'approved'),
            'pending' => $isOwner ? Group::members($id, 'pending') : [],
            'messages' => $isMember ? Group::lastMessages($id) : [],
        ]);
    }

    public function join(int $id): void
    {
        $this->requireLogin();
        $g = Group::find($id) ?? $this->abort(404);
        if ((int)$g['member_count'] >= (int)$g['max_members']) {
            flash('warning', 'This group is full.');
        } elseif (Group::requestJoin($id, Auth::id())) {
            flash('success', 'Join request sent. The group owner will review it.');
        } else {
            flash('info', 'You already asked to join this group.');
        }
        $this->redirect("/groups/$id");
    }

    public function approve(int $id, int $mid): void
    {
        $this->requireLogin();
        $g = Group::find($id) ?? $this->abort(404);
        if ((int)$g['owner_id'] !== Auth::id()) $this->abort(403);
        if (Group::approve($mid, $g)) flash('success', 'Member approved.');
        else flash('warning', 'The group is full (' . $g['max_members'] . ' members).');
        $this->redirect("/groups/$id");
    }

    public function reject(int $id, int $mid): void
    {
        $this->requireLogin();
        $g = Group::find($id) ?? $this->abort(404);
        if ((int)$g['owner_id'] !== Auth::id()) $this->abort(403);
        Group::reject($mid, $id);
        flash('info', 'Request declined.');
        $this->redirect("/groups/$id");
    }

    public function delete(int $id): void
    {
        $this->requireLogin();
        $g = Group::find($id) ?? $this->abort(404);
        if ((int)$g['owner_id'] !== Auth::id() && !Auth::hasRole('admin')) $this->abort(403);
        Database::execute('DELETE FROM study_groups WHERE id = ?', [$id]);
        flash('success', 'Group deleted.');
        $this->redirect('/groups');
    }

    // ---- chat (JSON API used by Fetch) ----
    private function memberOrFail(int $id): void
    {
        $this->requireLogin();
        Group::find($id) ?? $this->abort(404, 'Group not found.');
        $m = Group::membership($id, Auth::id());
        if (!$m || $m['status'] !== 'approved') $this->abort(403, 'Only group members can use the chat.');
    }

    public function messages(int $id): void
    {
        $this->memberOrFail($id);
        $this->json(['ok' => true, 'messages' => Group::messagesAfter($id, (int)($_GET['after'] ?? 0)), 'me' => Auth::id()]);
    }

    public function postMessage(int $id): void
    {
        $this->memberOrFail($id);
        $body = trim((string)($_POST['body'] ?? ''));
        if ($body === '') $this->json(['ok' => false, 'message' => "Message can't be empty."], 422);
        if (mb_strlen($body) > 500) $this->json(['ok' => false, 'message' => 'Message is too long (max 500 characters).'], 422);
        Group::addMessage($id, Auth::id(), $body);
        $this->json(['ok' => true]);
    }
}
