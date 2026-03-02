<?php
chdir(__DIR__ . '/..');
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use Illuminate\Support\Facades\DB;

echo "\n=== featured_ad_purchases columns ===\n";
$cols = DB::connection('listocean')->select('DESCRIBE featured_ad_purchases');
foreach ($cols as $c) echo $c->Field . " | " . $c->Type . "\n";

echo "\n=== featured_ad_purchases data ===\n";
$rows = DB::connection('listocean')->table('featured_ad_purchases')->get();
foreach ($rows as $r) echo json_encode((array)$r) . "\n";

echo "\n=== featured_ad_activations columns ===\n";
$cols = DB::connection('listocean')->select('DESCRIBE featured_ad_activations');
foreach ($cols as $c) echo $c->Field . " | " . $c->Type . "\n";

echo "\n=== listocean reviews columns ===\n";
$cols = DB::connection('listocean')->select('DESCRIBE reviews');
foreach ($cols as $c) echo $c->Field . " | " . $c->Type . "\n";

echo "\n=== admin shops table columns ===\n";
$cols = DB::connection('mysql')->select('DESCRIBE shops');
foreach ($cols as $c) echo $c->Field . " | " . $c->Type . "\n";

echo "\nDone.\n";
