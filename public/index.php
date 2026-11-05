<?php
require __DIR__ . '/../app/bootstrap.php';

$router = new Router();
require APP_ROOT . '/app/routes.php';

try {
    $router->dispatch();
} catch (Throwable $e) {
    @file_put_contents(
        APP_ROOT . '/storage/error.log',
        '[' . date('Y-m-d H:i:s') . '] ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine() . PHP_EOL,
        FILE_APPEND
    );
    if (ob_get_level() > 0) ob_end_clean();
    (new ErrorController())->show(500);
}
