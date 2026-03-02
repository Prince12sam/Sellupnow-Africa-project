<?php
chdir(__DIR__ . '/..');
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use Illuminate\Support\Facades\DB;

function cols($conn, $table) {
    try {
        $rows = DB::connection($conn)->select("SHOW COLUMNS FROM `$table`");
        return array_map(fn($r) => $r->Field . ' (' . $r->Type . ')', $rows);
    } catch (\Exception $e) { return ['ERROR: ' . $e->getMessage()]; }
}
function q($conn, $sql) {
    try { return DB::connection($conn)->select($sql); }
    catch (\Exception $e) { return []; }
}

// ── ESCROW ─────────────────────────────────────────────────────────────────
echo "\n=== ESCROW ===\n";
echo "\nlistocean.listings columns (escrow-related):\n";
foreach (cols('listocean','listings') as $c) {
    if (str_contains(strtolower($c), 'escrow') || str_contains(strtolower($c),'price')) echo "  $c\n";
}
echo "\nlistocean.escrow_transactions columns:\n";
foreach (cols('listocean','escrow_transactions') as $c) echo "  $c\n";
echo "\nescrow_transactions count: " . DB::connection('listocean')->table('escrow_transactions')->count() . "\n";
echo "escrow_events count: " . DB::connection('listocean')->table('escrow_events')->count() . "\n";

// Check escrow_enabled on actual listings
$listings = q('listocean', "SELECT id, title, price, is_published, status FROM listings LIMIT 5");
echo "\nSample listings:\n";
foreach ($listings as $l) {
    echo "  [{$l->id}] {$l->title} | price={$l->price} | published={$l->is_published} | status={$l->status}\n";
}
// Does escrow_enabled column exist?
$listingCols = DB::connection('listocean')->select("SHOW COLUMNS FROM listings");
$hasEscrow = false;
foreach ($listingCols as $c) { if ($c->Field === 'escrow_enabled') $hasEscrow = true; }
echo "escrow_enabled column exists: " . ($hasEscrow ? 'YES' : 'NO - MISSING') . "\n";

// ── REVIEWS ────────────────────────────────────────────────────────────────
echo "\n=== REVIEWS ===\n";
$reviewCols = cols('listocean', 'reviews');
echo "listocean.reviews columns:\n";
foreach ($reviewCols as $c) echo "  $c\n";
echo "review count: " . DB::connection('listocean')->table('reviews')->count() . "\n";

// Check if admin review controller uses listocean DB
echo "\nAdmin review check (admin DB reviews table): ";
try { echo DB::connection('mysql')->table('reviews')->count() . " rows\n"; }
catch (\Exception $e) { echo "ERR: " . $e->getMessage() . "\n"; }

// ── WALLET ─────────────────────────────────────────────────────────────────
echo "\n=== WALLET ===\n";
echo "listocean.wallets: " . DB::connection('listocean')->table('wallets')->count() . " rows\n";
echo "listocean.wallet_histories: " . DB::connection('listocean')->table('wallet_histories')->count() . " rows\n";
$walletCols = cols('listocean', 'wallets');
echo "wallets columns:\n";
foreach ($walletCols as $c) echo "  $c\n";
$walletHistCols = cols('listocean', 'wallet_histories');
echo "wallet_histories columns:\n";
foreach ($walletHistCols as $c) echo "  $c\n";

// Sample wallet data
$wallets = q('listocean', "SELECT w.*, u.name, u.email FROM wallets w LEFT JOIN users u ON u.id = w.user_id LIMIT 5");
echo "\nSample wallets:\n";
foreach ($wallets as $w) {
    $name = property_exists($w, 'name') ? $w->name : 'N/A';
    $bal = property_exists($w, 'balance') ? $w->balance : (property_exists($w,'amount') ? $w->amount : '?');
    echo "  User: $name | balance: $bal\n";
}
$wh = q('listocean', "SELECT * FROM wallet_histories ORDER BY id DESC LIMIT 5");
echo "Recent wallet histories:\n";
foreach ($wh as $r) {
    echo "  " . json_encode((array)$r) . "\n";
}

// ── MEMBERSHIPS ────────────────────────────────────────────────────────────
echo "\n=== MEMBERSHIPS ===\n";
echo "membership_plans: " . DB::connection('listocean')->table('membership_plans')->count() . " rows\n";
echo "membership_types: " . DB::connection('listocean')->table('membership_types')->count() . " rows\n";
echo "user_memberships: " . DB::connection('listocean')->table('user_memberships')->count() . " rows\n";
echo "membership_features: " . DB::connection('listocean')->table('membership_features')->count() . " rows\n";
$plans = q('listocean', "SELECT * FROM membership_plans LIMIT 10");
echo "\nMembership plans:\n";
foreach ($plans as $p) echo "  " . json_encode((array)$p) . "\n";
$types = q('listocean', "SELECT * FROM membership_types LIMIT 10");
echo "\nMembership types:\n";
foreach ($types as $t) echo "  " . json_encode((array)$t) . "\n";
$activeSubs = q('listocean', "SELECT um.*, u.name, u.email, mp.name as plan FROM user_memberships um LEFT JOIN users u ON u.id=um.user_id LEFT JOIN membership_plans mp ON mp.id=um.membership_plan_id LIMIT 5");
echo "\nActive memberships:\n";
foreach ($activeSubs as $s) echo "  " . json_encode((array)$s) . "\n";
// Check admin admin_membership_plan route
$adminPlans = [];
try { $adminPlans = DB::connection('mysql')->table('subscription_plans')->get()->toArray(); }
catch (\Exception $e) {}
echo "Admin DB subscription_plans: " . count($adminPlans) . " rows\n";

// ── FEATURED ADS ───────────────────────────────────────────────────────────
echo "\n=== FEATURED ADS ===\n";
echo "listocean.featured_ad_packages: " . DB::connection('listocean')->table('featured_ad_packages')->count() . " rows\n";
echo "listocean.featured_ad_purchases: " . DB::connection('listocean')->table('featured_ad_purchases')->count() . " rows\n";
echo "listocean.featured_ad_activations: " . DB::connection('listocean')->table('featured_ad_activations')->count() . " rows\n";
$pkgs = q('listocean', "SELECT * FROM featured_ad_packages LIMIT 5");
echo "\nFeatured ad packages:\n";
foreach ($pkgs as $p) echo "  " . json_encode((array)$p) . "\n";
$purch = q('listocean', "SELECT fa.*, u.name, u.email, l.title as listing_title FROM featured_ad_purchases fa LEFT JOIN users u ON u.id=fa.user_id LEFT JOIN listings l ON l.id=fa.listing_id LIMIT 5");
echo "\nFeatured ad purchases:\n";
foreach ($purch as $p) echo "  " . json_encode((array)$p) . "\n";
// Admin side
echo "\nAdmin DB featured_ad_packages: ";
try { echo DB::connection('mysql')->table('featured_ad_packages')->count() . " rows\n"; }
catch (\Exception $e) { echo "ERR: " . $e->getMessage() . "\n"; }

// ── BRAND TABLE ────────────────────────────────────────────────────────────
echo "\n=== BRAND TABLE ===\n";
echo "Admin DB brands: ";
try { echo DB::connection('mysql')->table('brands')->count() . " rows\n"; }
catch (\Exception $e) { echo "ERR: " . $e->getMessage() . "\n"; }
echo "Listocean brands: ";
try { echo DB::connection('listocean')->table('brands')->count() . " rows\n"; }
catch (\Exception $e) { echo "TABLE DOES NOT EXIST\n"; }

// Check if listings have a brand column
$hasBrand = false;
foreach ($listingCols as $c) { if ($c->Field === 'brand_id' || $c->Field === 'brand') $hasBrand = true; }
echo "Listings has brand column: " . ($hasBrand ? 'YES' : 'NO') . "\n";

// Is brand referenced anywhere in frontend add-listing?
echo "\n=== COMMISSION RULES ===\n";
echo "Admin DB commission_rules: " . DB::connection('mysql')->table('commission_rules')->count() . " rows\n";
$comRules = q('mysql', "SELECT * FROM commission_rules LIMIT 5");
foreach ($comRules as $r) echo "  " . json_encode((array)$r) . "\n";
echo "Listocean commission_rules (table exists?): ";
try { echo DB::connection('listocean')->table('commission_rules')->count() . " rows\n"; }
catch (\Exception $e) { echo "NO TABLE\n"; }

echo "\nDone.\n";
