<?php
try {
    $path = __DIR__ . '/../database/database.sqlite';
    if (!file_exists($path)) {
        echo "NO_DB_FILE\n";
        exit(0);
    }
    $db = new PDO('sqlite:' . $path);
    $rows = $db->query("SELECT name FROM sqlite_master WHERE type='table'")->fetchAll(PDO::FETCH_COLUMN);
    foreach ($rows as $r) {
        echo $r . PHP_EOL;
    }
} catch (Exception $e) {
    echo 'ERROR: ' . $e->getMessage() . PHP_EOL;
}
