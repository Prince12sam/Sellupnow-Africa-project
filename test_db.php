<?php
if (!in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '76.13.211.92'])) {
    http_response_code(403); die('Access denied');
}
define('LARAVEL_START', microtime(true));
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Http\Kernel::class)->bootstrap();
echo '<pre>';
$env_val = env('LISTOCEAN_DB_CONNECTION');
echo 'LISTOCEAN_DB_CONNECTION env: ' . var_export($env_val, true) . "\n";
$cfg = config('database.connections.listocean');
echo 'Listocean driver: ' . ($cfg['driver'] ?? 'NULL - using sqlite!') . "\n";
echo 'Listocean database: ' . ($cfg['database'] ?? 'null') . "\n";

try {
    $db = \Illuminate\Support\Facades\DB::connection('listocean');
    $count = $db->table('countries')->count();
    echo "Countries count: $count\n";
    $id = $db->table('countries')->insertGetId([
        'name'         => 'TestCountryA',
        'country'      => 'TestCountryA',
        'country_code' => 'TC',
        'dial_code'    => '999',
        'status'       => 1,
        'created_at'   => now(),
        'updated_at'   => now(),
    ]);
    echo "Inserted ID: $id\n";
    $row = $db->table('countries')->where('id', $id)->first();
    echo "Row in DB: " . json_encode((array)$row) . "\n";
    $db->table('countries')->where('id', $id)->delete();
    echo "Cleaned up. DONE!\n";
} catch (\Throwable $e) {
    echo 'ERROR: ' . $e->getMessage() . "\n";
    echo 'Class: ' . get_class($e) . "\n";
    echo 'File: ' . $e->getFile() . ':' . $e->getLine() . "\n";
}
echo '</pre>';
