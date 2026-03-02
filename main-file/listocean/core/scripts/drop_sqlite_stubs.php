<?php
$dbPath = __DIR__ . '/../database/database.sqlite';
if (!file_exists($dbPath)) {
    echo "DB file not found: $dbPath\n";
    exit(1);
}
try {
    $pdo = new PDO('sqlite:' . $dbPath);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $tables = ['languages','media_uploads','static_options','custom_fonts'];
    foreach ($tables as $t) {
        $pdo->exec("DROP TABLE IF EXISTS $t;");
        echo "dropped: $t\n";
    }
    echo "drop-stubs: OK\n";
} catch (Exception $e) {
    echo "drop-stubs: ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
