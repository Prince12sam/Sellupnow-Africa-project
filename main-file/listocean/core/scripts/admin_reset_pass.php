<?php
// Usage: php admin_reset_pass.php [email] [new_password]
$path = __DIR__ . '/../database/database.sqlite';
if (!file_exists($path)) {
    echo "NO_DB_FILE\n";
    exit(1);
}
$pdo = new PDO('sqlite:' . $path);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
if ($argc === 1) {
    // list admins
    $stmt = $pdo->query('SELECT id, name, email FROM admins');
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (!$rows) {
        echo "NO_ADMINS\n";
        exit(0);
    }
    foreach ($rows as $r) {
        echo $r['id'] . "\t" . ($r['email'] ?? $r['name']) . "\t" . $r['name'] . PHP_EOL;
    }
    exit(0);
}
if ($argc < 3) {
    echo "Usage: php admin_reset_pass.php email new_password\n";
    exit(1);
}
$email = $argv[1];
$new = $argv[2];
// generate bcrypt hash compatible with Laravel
$options = ['cost' => 10];
$hash = password_hash($new, PASSWORD_BCRYPT, $options);
// Update admins table 'password' column
$update = $pdo->prepare('UPDATE admins SET password = ? WHERE email = ?');
$update->execute([$hash, $email]);
if ($update->rowCount() > 0) {
    echo "UPDATED\n";
} else {
    echo "NOT_FOUND\n";
}
