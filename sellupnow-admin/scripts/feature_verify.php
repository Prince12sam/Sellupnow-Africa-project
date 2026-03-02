<?php
chdir(__DIR__ . '/..');
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use Illuminate\Support\Facades\DB;

echo "\n=== LISTOCEAN BRANDS (sample 5) ===\n";
$brands = DB::connection('listocean')->table('brands')->take(5)->get();
foreach ($brands as $b) echo "  " . json_encode((array)$b) . "\n";
echo "Total brands: " . DB::connection('listocean')->table('brands')->count() . "\n";

echo "\n=== BRAND USAGE IN LISTINGS ===\n";
$withBrand = DB::connection('listocean')->table('listings')->whereNotNull('brand_id')->where('brand_id','!=',0)->count();
$total = DB::connection('listocean')->table('listings')->count();
echo "Listings with brand_id set: $withBrand / $total\n";

echo "\n=== WALLET — balance check ===\n";
$wallets = DB::connection('listocean')->table('wallets')->get();
foreach ($wallets as $w) {
    $u = DB::connection('listocean')->table('users')->where('id',$w->user_id)->first();
    echo "  User: " . ($u->name ?? 'N/A') . " | balance: {$w->balance} | status: {$w->status}\n";
}

echo "\n=== WALLET HISTORIES — all (status/type breakdown) ===\n";
$hists = DB::connection('listocean')->table('wallet_histories')->get();
foreach ($hists as $h) {
    echo "  #{$h->id} | type={$h->type} | gateway={$h->payment_gateway} | status={$h->payment_status} | amount={$h->amount} | bal_after={$h->balance_after}\n";
}

echo "\n=== MEMBERSHIP — active user subscriptions ===\n";
$subs = DB::connection('listocean')->table('user_memberships as um')
    ->leftJoin('users as u','u.id','=','um.user_id')
    ->leftJoin('membership_plans as mp','mp.id','=','um.membership_plan_id')
    ->select('um.*','u.name','u.email','mp.name as plan_name')
    ->get();
foreach ($subs as $s) echo "  " . json_encode((array)$s) . "\n";

echo "\n=== FEATURED ADS — purchase details ===\n";
$purch = DB::connection('listocean')->table('featured_ad_purchases as fa')
    ->leftJoin('users as u','u.id','=','fa.user_id')
    ->leftJoin('listings as l','l.id','=','fa.listing_id')
    ->leftJoin('featured_ad_packages as p','p.id','=','fa.package_id')
    ->select('fa.*','u.name as buyer','l.title as listing','p.name as package')
    ->get();
foreach ($purch as $p) echo "  " . json_encode((array)$p) . "\n";

echo "\n=== FEATURED ADS — activations ===\n";
$acts = DB::connection('listocean')->table('featured_ad_activations')->get();
foreach ($acts as $a) echo "  " . json_encode((array)$a) . "\n";

echo "\n=== REVIEWS — both DBs ===\n";
echo "listocean reviews: " . DB::connection('listocean')->table('reviews')->count() . "\n";
echo "admin reviews: " . DB::connection('mysql')->table('reviews')->count() . "\n";
// Check admin Review model — which shop_id does it filter by?
$shop = DB::connection('mysql')->table('shops')->first();
echo "Admin shops table: " . DB::connection('mysql')->table('shops')->count() . " rows\n";

echo "\n=== ESCROW — escrow_enabled listing count ===\n";
$escrowEnabled = DB::connection('listocean')->table('listings')->where('escrow_enabled',1)->count();
echo "Listings with escrow_enabled=1: $escrowEnabled\n";
$sample = DB::connection('listocean')->table('listings')->where('escrow_enabled',1)->take(3)->get(['id','title','price']);
foreach ($sample as $l) echo "  [{$l->id}] {$l->title} - price: {$l->price}\n";

echo "\nDone.\n";
