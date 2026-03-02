<?php
$db = __DIR__ . '/../database/database.sqlite';
$pdo = new PDO('sqlite:' . $db);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$stmt = $pdo->prepare("SELECT option_value FROM static_options WHERE option_name = 'site_force_ssl_redirection' LIMIT 1");
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ($row) {
    echo "site_force_ssl_redirection=" . $row['option_value'] . "\n";
} else {
    echo "site_force_ssl_redirection not set\n";
}
