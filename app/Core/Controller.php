<?php
abstract class Controller
{
    protected function isAjax(): bool
    {
        return ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'fetch';
    }

    protected function view(string $name, array $data = []): void
    {
        $data['title'] = $data['title'] ?? config('app_name');
        extract($data);
        ob_start();
        require APP_ROOT . "/views/$name.php";
        $content = ob_get_clean();
        require APP_ROOT . '/views/layout.php';
        unset($_SESSION['old'], $_SESSION['errors']);
    }

    protected function redirect(string $path): never
    {
        header('Location: ' . url($path));
        exit;
    }

    protected function json(array $data, int $code = 200): never
    {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    protected function abort(int $code, string $message = ''): never
    {
        if ($this->isAjax()) {
            $this->json(['ok' => false, 'message' => $message ?: 'Something went wrong, try again.'], $code);
        }
        http_response_code($code);
        $page = in_array($code, [403, 404, 419, 500], true) ? $code : 500;
        $this->view("errors/$page", ['title' => "Error $code"]);
        exit;
    }

    protected function requireLogin(): void
    {
        if (!Auth::check()) {
            if ($this->isAjax()) $this->json(['ok' => false, 'message' => 'Please log in first.'], 401);
            flash('warning', 'Please log in to continue.');
            $this->redirect('/login');
        }
    }

    protected function requireRole(string ...$roles): void
    {
        $this->requireLogin();
        if (!Auth::hasRole(...$roles)) $this->abort(403, 'You do not have permission to do that.');
    }

    /** Only this portal's role (or an admin) may continue. */
    protected function requirePortal(string $portal): void
    {
        $this->requireLogin();
        if (!Auth::canUsePortal($portal)) $this->abort(403, "This page is part of the $portal portal.");
    }

    protected function withErrors(array $errors, array $old, string $back): never
    {
        $_SESSION['errors'] = $errors;
        $_SESSION['old'] = $old;
        $this->redirect($back);
    }

    protected function errors(): array { return $_SESSION['errors'] ?? []; }
}
