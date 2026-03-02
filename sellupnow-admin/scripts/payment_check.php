<?php
chdir(__DIR__ . '/..');
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use Illuminate\Support\Facades\DB;

echo "\n=== PAYMENT GATEWAYS in listocean_db ===\n";
$gateways = DB::connection('listocean')->table('payment_gateways')->get();
foreach ($gateways as $g) {
    $creds = json_decode($g->credentials ?? '{}', true);
    // Mask secret keys
    if (isset($creds['secret_key'])) {
        $creds['secret_key'] = substr($creds['secret_key'], 0, 8) . '...MASKED';
    }
    if (isset($creds['public_key'])) {
        $creds['public_key'] = substr($creds['public_key'], 0, 8) . '...MASKED';
    }
    echo "\n  [{$g->id}] name={$g->name} | status={$g->status} | test_mode=" . ($g->test_mode ?? '?') . "\n";
    echo "      credentials=" . json_encode($creds) . "\n";
}

echo "\n=== MEMBERSHIP DETAILS ===\n";
$subs = DB::connection('listocean')->table('user_memberships')->get();
foreach ($subs as $s) {
    $plans = DB::connection('listocean')->table('membership_plans')->where('id', $s->membership_id)->first();
    $user = DB::connection('listocean')->table('users')->where('id', $s->user_id)->first();
    echo "  sub #{$s->id} | user=" . ($user->username ?? $user->email ?? 'N/A') . " | plan=" . ($plans->name ?? 'N/A') . " | status={$s->status} | expires={$s->expire_date}\n";
    echo "  payment_status={$s->payment_status} | payment_gateway={$s->payment_gateway}\n";
}

echo "\n=== ESCROW ENABLED LISTINGS ===\n";
$listings = DB::connection('listocean')->table('listings')->where('escrow_enabled', 1)->get(['id','title','price','status']);
if ($listings->count() === 0) {
    echo "  None — no listings have escrow_enabled=1\n";
} else {
    foreach ($listings as $l) echo "  [{$l->id}] {$l->title} | price={$l->price} | status={$l->status}\n";
}

echo "\n=== FEATURED AD PACKAGES (listocean_db) ===\n";
$pkgs = DB::connection('listocean')->table('featured_ad_packages')->get();
foreach ($pkgs as $p) {
    echo "  [{$p->id}] {$p->name} | price={$p->price} | duration={$p->duration_days}d | active=" . ($p->is_active ?? '?') . "\n";
}

echo "\n=== FEATURED AD PURCHASES + ACTIVATIONS ===\n";
$purch = DB::connection('listocean')->table('featured_ad_purchases')->get();
foreach ($purch as $p) {
    $u = DB::connection('listocean')->table('users')->where('id', $p->user_id)->first();
    echo "  purchase #{$p->id} | user=" . ($u->username ?? 'N/A') . " | payment_status={$p->payment_status} | amount=" . ($p->amount ?? $p->price ?? '?') . "\n";
}

echo "\n=== ADMIN SHOPS TABLE (for Review fix) ===\n";
echo "Admin reviews table rows: " . DB::connection('mysql')->table('reviews')->count() . "\n";
echo "Admin shops table rows: " . DB::connection('mysql')->table('shops')->count() . "\n";
$adminShop = DB::connection('mysql')->table('shops')->first();
if ($adminShop) echo "  First shop: " . json_encode((array)$adminShop) . "\n";

echo "\nDone.\n";
