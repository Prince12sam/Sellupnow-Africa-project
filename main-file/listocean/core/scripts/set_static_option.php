<?php
$db = __DIR__ . '/../database/database.sqlite';
$pdo = new PDO('sqlite:' . $db);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$name = 'site_force_ssl_redirection';
$value = '';
try {
    $pdo->beginTransaction();
    $stmt = $pdo->prepare("SELECT id FROM static_options WHERE option_name = ? LIMIT 1");
    $stmt->execute([$name]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $now = date('Y-m-d H:i:s');
    if ($row) {
        $u = $pdo->prepare("UPDATE static_options SET option_value = ?, updated_at = ? WHERE id = ?");
        $u->execute([$value, $now, $row['id']]);
        echo "updated\n";
    } else {
        $i = $pdo->prepare("INSERT INTO static_options (option_name, option_value, created_at, updated_at) VALUES (?,?,?,?)");
        $i->execute([$name, $value, $now, $now]);
        echo "inserted\n";
    }
    $pdo->commit();
} catch (Exception $e) {
    $pdo->rollBack();
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
