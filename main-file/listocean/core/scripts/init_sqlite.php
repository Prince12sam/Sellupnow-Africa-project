<?php
$dbPath = __DIR__ . '/../database/database.sqlite';
if (!file_exists(dirname($dbPath))) {
    mkdir(dirname($dbPath), 0777, true);
}
if (!file_exists($dbPath)) {
    touch($dbPath);
}
try{
    $pdo = new PDO('sqlite:' . $dbPath);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create minimal languages table
    $pdo->exec("CREATE TABLE IF NOT EXISTS languages (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT,
        slug TEXT,
        direction TEXT DEFAULT 'ltr',
        `default` INTEGER DEFAULT 0
    );");

    // Create minimal media_uploads table
    $pdo->exec("CREATE TABLE IF NOT EXISTS media_uploads (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        path TEXT,
        alt TEXT
    );");

    // Create minimal static_options table
    $pdo->exec("CREATE TABLE IF NOT EXISTS static_options (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        option_name TEXT UNIQUE,
        option_value TEXT
    );");

    // Create minimal custom_fonts table (frontend expects this)
    $pdo->exec("CREATE TABLE IF NOT EXISTS custom_fonts (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT,
        status INTEGER DEFAULT 0,
        file_path TEXT,
        created_at TEXT,
        updated_at TEXT
    );");

    // Ensure there is at least one active custom font to avoid view errors
    $stmt = $pdo->prepare("SELECT COUNT(*) as cnt FROM custom_fonts WHERE status = 1");
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (empty($row) || intval($row['cnt']) === 0) {
        $pdo->exec("INSERT INTO custom_fonts (name, status, file_path, created_at, updated_at) VALUES ('Default', 1, '', datetime('now'), datetime('now'));");
    }

    echo "sqlite-init: OK\n";
}catch (Exception $e){
    echo "sqlite-init: ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
