<?php
define('APP_ROOT', dirname(__DIR__));

// ---- configuration ----
$GLOBALS['__config'] = require APP_ROOT . '/config/config.php';
if (is_file(APP_ROOT . '/config/config.local.php')) {
    $GLOBALS['__config'] = array_replace_recursive($GLOBALS['__config'], require APP_ROOT . '/config/config.local.php');
}

// ---- autoload ----
spl_autoload_register(function (string $class): void {
    foreach (['Core', 'Models', 'Controllers', 'Services'] as $dir) {
        $file = APP_ROOT . "/app/$dir/$class.php";
        if (is_file($file)) { require $file; return; }
    }
});

require APP_ROOT . '/app/helpers.php';

// ---- security headers ----
if (PHP_SAPI !== 'cli') {
    header('X-Frame-Options: SAMEORIGIN');
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: strict-origin-when-cross-origin');
}

// ---- session ----
$https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
ini_set('session.use_strict_mode', '1');     // refuse session ids the server did not create
ini_set('session.use_only_cookies', '1');
session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax', 'secure' => $https]);
session_start();

date_default_timezone_set('Asia/Dhaka');
