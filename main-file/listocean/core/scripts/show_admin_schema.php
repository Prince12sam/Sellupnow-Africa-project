<?php
$path = __DIR__ . '/../database/database.sqlite';
if (!file_exists($path)) { echo "NO_DB_FILE\n"; exit(1); }
$pdo = new PDO('sqlite:' . $path);
$stmt = $pdo->query("PRAGMA table_info('admins')");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (!$rows) { echo "NO_TABLE_OR_EMPTY\n"; exit(0); }
foreach ($rows as $r) {
    echo $r['cid'] . "\t" . $r['name'] . "\t" . $r['type'] . "\t" . $r['notnull'] . "\t" . $r['dflt_value'] . PHP_EOL;
}
