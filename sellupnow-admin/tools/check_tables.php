<?php
define('LARAVEL_START', microtime(true));
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Admin currencies table
echo "=== ADMIN currencies table ===\n";
foreach (DB::select("DESCRIBE currencies") as $col) {
    echo "  {$col->Field} ({$col->Type})\n";
}
echo "\n--- admin currencies rows ---\n";
foreach (DB::table('currencies')->get() as $row) {
    $arr = (array)$row;
    echo "  " . json_encode($arr) . "\n";
}

// Admin generate_settings currency columns
echo "\n=== ADMIN generate_settings currency columns ===\n";
foreach (DB::table('generate_settings')->get() as $row) {
    if (isset($row->currency_name)) {
        echo "  currency_name: {$row->currency_name}\n";
        echo "  currency_code: {$row->currency_code}\n";
        echo "  currency_symbol: {$row->currency_symbol}\n";
    }
}

// Listocean static_options for currency
echo "\n=== LISTOCEAN static_options (currency keys) ===\n";
$db = DB::connection('listocean');
foreach ($db->table('static_options')->where('option_name', 'like', '%currency%')->get() as $row) {
    echo "  {$row->option_name} = {$row->option_value}\n";
}
