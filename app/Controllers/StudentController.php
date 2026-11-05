<?php
/** Student portal home. Quizzes live in QuizController under /student/quiz. */
class StudentController extends Controller
{
    public function dashboard(): void
    {
        $this->requirePortal('student');
        $uid = Auth::id();
        $weak = Quiz::weakTopics($uid);
        $subjectIds = array_values(array_unique(array_column($weak, 'subject_id')));
        $this->view('student/dashboard', [
            'title' => 'Student dashboard',
            'useChart' => true,
            'weak' => $weak,
            'recommended' => Resource::forSubjects($subjectIds),
            'recent' => Quiz::attempts($uid, 5, 0),
            'groups' => Group::mine($uid),
            'progress' => Quiz::progress($uid),
        ]);
    }
}
