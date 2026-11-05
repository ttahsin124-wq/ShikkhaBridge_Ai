<?php
// Default configuration. Override anything in config/config.local.php (not committed to Git).
return [
    'app_name' => 'ShikkhaBridge AI',
    'db' => [
        'host' => '127.0.0.1',
        'name' => 'shikkhabridge',
        'user' => 'root',
        'pass' => '',
        'charset' => 'utf8mb4',
    ],
    'gemini' => [
        'api_key' => getenv('GEMINI_API_KEY') ?: '',   // leave empty to use mock AI answers
        'model' => 'gemini-3.5-flash',   // 2.0 / 2.5 are closed to new API keys; 3.8 was overloaded when tested
    ],
    'upload' => [
        'max_size' => 2 * 1024 * 1024,
        'types' => ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'],
    ],
    'contact_email' => 'hello@example.com',   // shown in the Contact section of the landing page
    'per_page' => 10,
    'max_login_attempts' => 5,
    'lockout_minutes' => 15,
    'ai_requests_per_hour' => 30,
    // No mail server in class projects: show the reset link on screen. Set false in production.
    'show_reset_link' => true,
];
