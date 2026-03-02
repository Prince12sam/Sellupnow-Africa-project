<?php
$path = __DIR__ . '/../database/database.sqlite';
if (!file_exists($path)) { echo "NO_DB_FILE\n"; exit(1); }
$pdo = new PDO('sqlite:' . $path);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$email = $argv[1] ?? 'admin@example.com';
$username = $argv[2] ?? 'admin';
$password = $argv[3] ?? 'Passw0rd!';
$name = $argv[4] ?? 'Local Admin';
// check exists
$stmt = $pdo->prepare('SELECT id FROM admins WHERE email = ? OR username = ? LIMIT 1');
$stmt->execute([$email, $username]);
if ($stmt->fetch()) {
    echo "ALREADY_EXISTS\n";
    exit(0);
}
$hash = password_hash($password, PASSWORD_BCRYPT);
$now = date('Y-m-d H:i:s');
$insert = $pdo->prepare('INSERT INTO admins (name, username, phone, email, email_verified, image, password, about, role, status, remember_token, created_at, updated_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)');
$insert->execute([$name, $username, '', $email, 1, '', $hash, '', 'admin', 1, '', $now, $now]);
if ($insert->rowCount() > 0) echo "CREATED\n"; else echo "FAILED\n";
