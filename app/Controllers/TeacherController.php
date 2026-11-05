<?php
/** Teacher portal home and "my resources". Question bank: QuestionController, resource editing: ResourceController. */
class TeacherController extends Controller
{
    /** A teacher sees stats for their own content; an admin viewing the portal sees everyone's. */
    private function scope(): ?int
    {
        return Auth::hasRole('admin') ? null : Auth::id();
    }

    public function dashboard(): void
    {
        $this->requirePortal('teacher');
        $scope = $this->scope();
        $uid = Auth::id();
        $this->view('teacher/dashboard', [
            'title' => 'Teacher dashboard',
            'counts' => [
                'resources' => Resource::count('', 0, $scope ?? 0),
                'questions' => (int)($scope === null
                    ? Database::value('SELECT COUNT(*) FROM questions')
                    : Database::value('SELECT COUNT(*) FROM questions WHERE created_by = ?', [$scope])),
            ],
            'summary' => Quiz::teacherSummary($scope),
            'topics' => Quiz::teacherTopicStats($scope),
            'hardest' => Quiz::hardestQuestions($scope),
            'recentResources' => Resource::search('', 0, 5, 0, $scope ?? 0),
            'groups' => Group::mine($uid),
            'isAdminView' => $scope === null,
        ]);
    }

    public function resources(): void
    {
        $this->requirePortal('teacher');
        $q = trim($_GET['q'] ?? '');
        $scope = $this->scope() ?? 0;
        $pager = paginate(Resource::count($q, 0, $scope));
        $this->view('teacher/resources', [
            'title' => 'My resources',
            'items' => Resource::search($q, 0, $pager['perPage'], $pager['offset'], $scope),
            'pager' => $pager,
            'q' => $q,
        ]);
    }
}
