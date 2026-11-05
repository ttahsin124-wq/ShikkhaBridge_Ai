<?php
class HomeController extends Controller
{
    public function index(): void
    {
        // real numbers from the database, chosen so a fresh install never shows "0"
        $stats = [
            'questions' => (int)Database::value('SELECT COUNT(*) FROM questions'),
            'topics' => (int)Database::value('SELECT COUNT(*) FROM topics'),
            'subjects' => (int)Database::value('SELECT COUNT(*) FROM subjects'),
        ];
        $this->view('home', ['title' => 'AI-powered personalised learning', 'shell' => 'landing', 'stats' => $stats]);
    }

    /** Old /dashboard link: send each user to their own portal. */
    public function dashboard(): void
    {
        $this->requireLogin();
        $this->redirect(portal_home(Auth::user()));
    }
}
