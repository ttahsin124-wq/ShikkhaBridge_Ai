<?php
// Usage (from the project folder):  php scripts/create_admin.php "Admin Name" admin@example.com "StrongPassword123"
if (PHP_SAPI !== 'cli') { exit('Run this from the command line.'); }
require __DIR__ . '/../app/bootstrap.php';

[$script, $name, $email, $password] = array_pad($argv, 4, null);
if (!$name || !$email || !$password) {
    exit("Usage: php scripts/create_admin.php \"Name\" email@example.com \"Password\"\n");
}
if (strlen($password) < 8 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    exit("Use a valid email and a password of at least 8 characters.\n");
}
if (User::findByEmail($email)) {
    exit("That email already exists.\n");
}
$id = User::create($name, $email, password_hash($password, PASSWORD_DEFAULT), 'admin', 'active');
echo "Admin created with id $id.\n";
