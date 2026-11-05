<?php
class ErrorController extends Controller
{
    public function show(int $code): void
    {
        $code = in_array($code, [403, 404, 419, 500], true) ? $code : 500;
        $this->abort($code, match ($code) {
            403 => 'You do not have permission to do that.',
            404 => 'Page not found.',
            419 => 'Your session expired. Please reload the page and try again.',
            default => 'Something went wrong, try again.',
        });
    }
}
