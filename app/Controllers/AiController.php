<?php
class AiController extends Controller
{
    private GeminiService $ai;

    public function __construct() { $this->ai = new GeminiService(); }

    public function index(): void
    {
        $this->requireLogin();
        $this->view('ai/index', ['title' => 'AI Tutor', 'topics' => Catalog::topics()]);
    }

    public function explain(): void
    {
        $this->requireLogin();
        $topic = $this->text('topic');
        $this->respond($this->ai->explainPrompt($topic, $this->lang()));
    }

    public function practice(): void
    {
        $this->requireLogin();
        $topic = $this->text('topic');
        $this->respond($this->ai->practicePrompt($topic, $this->lang()));
    }

    public function summarize(): void
    {
        $this->requireLogin();
        $r = Resource::find((int)($_POST['resource_id'] ?? 0));
        if (!$r) $this->json(['ok' => false, 'message' => 'Resource not found.'], 404);
        $this->respond($this->ai->summarizePrompt($r['title'] . "\n" . $r['content'], $this->lang()));
    }

    private function text(string $key): string
    {
        $t = trim((string)($_POST[$key] ?? ''));
        if ($t === '') $this->json(['ok' => false, 'message' => 'Please type a topic or question first.'], 422);
        if (mb_strlen($t) > 1000) $this->json(['ok' => false, 'message' => 'Please keep it under 1000 characters.'], 422);
        return $t;
    }

    private function lang(): string
    {
        $l = $_POST['lang'] ?? Auth::user()['language_pref'];
        return in_array($l, ['bn', 'en'], true) ? $l : 'bn';
    }

    private function respond(string $prompt): never
    {
        // simple per-session rate limit so one student cannot burn the whole API quota
        $now = time();
        $log = array_filter($_SESSION['ai_log'] ?? [], fn($t) => $t > $now - 3600);
        if (count($log) >= (int)config('ai_requests_per_hour', 30)) {
            $this->json(['ok' => false, 'message' => 'You have used many AI requests this hour. Please try again later.'], 429);
        }
        $log[] = $now;
        $_SESSION['ai_log'] = $log;

        try {
            $answer = $this->ai->generate($prompt);
        } catch (Throwable $e) {
            error_log('[AI] ' . $e->getMessage());
            $this->json(['ok' => false, 'message' => 'The AI tutor is not available right now. Please try again in a moment.'], 502);
        }
        $this->json(['ok' => true, 'answer' => $answer]);
    }
}
