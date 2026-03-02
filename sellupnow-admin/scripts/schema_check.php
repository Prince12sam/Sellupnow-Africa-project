<?php
chdir(__DIR__ . '/..');
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use Illuminate\Support\Facades\DB;

echo "\n=== LISTOCEAN USERS TABLE COLUMNS ===\n";
$cols = DB::connection('listocean')->select('DESCRIBE users');
foreach ($cols as $c) echo $c->Field . "\n";

echo "\n=== MEMBERSHIP_PLANS columns ===\n";
$cols = DB::connection('listocean')->select('DESCRIBE membership_plans');
foreach ($cols as $c) echo $c->Field . "\n";

echo "\n=== USER_MEMBERSHIPS columns ===\n";
$cols = DB::connection('listocean')->select('DESCRIBE user_memberships');
foreach ($cols as $c) echo $c->Field . "\n";
