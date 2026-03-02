<?php
// Bootstrap Laravel from the admin app root (run as: php scripts/db_audit.php)
chdir(__DIR__ . '/..');
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

function tbl($connection, $table) {
    try {
        $count = DB::connection($connection)->table($table)->count();
        return $count;
    } catch (\Exception $e) {
        return 'ERR: ' . $e->getMessage();
    }
}

function exists($connection, $table) {
    try {
        DB::connection($connection)->table($table)->limit(1)->get();
        return true;
    } catch (\Exception $e) {
        return false;
    }
}

function row($label, $count) {
    $flag = '';
    if (is_int($count)) {
        $flag = $count === 0 ? ' [EMPTY]' : " [$count rows]";
    }
    echo str_pad($label, 45) . $flag . "\n";
}

// ── ADMIN DB ──────────────────────────────────────────────────────────────────
echo "\n=== ADMIN DB (sellupnow) ===\n\n";

$adminTables = [
    // E-commerce
    'orders'             => 'Orders',
    'order_items'        => 'Order Items',
    'riders'             => 'Riders / Delivery',
    'flash_sales'        => 'Flash Sales',
    'flash_sale_products'=> 'Flash Sale Products',
    'vat_taxes'          => 'VAT / Tax Rules',
    'coupons'            => 'Coupons',
    'brands'             => 'Brands',
    'currencies'         => 'Currencies',
    'delivery_charges'   => 'Delivery Charges',
    'products'           => 'Products',
    'commission_rules'   => 'Commission Rules',
    'membership_features'=> 'Membership Features',
    // Listings (core)
    'listings'           => 'Listings',
    'listing_reports'    => 'Listing Reports',
    // Users
    'customers'          => 'Customers (Flutter)',
    'shop_users'         => 'Site Users (web)',
    'employees'          => 'Employees',
    // Ads
    'advertisements'     => 'Advertisements',
    'banner_ads'         => 'Banner Ads',
    'banner_ad_requests' => 'Banner Ad Requests',
    'promo_video_ads'    => 'Promo Video Ads',
    'featured_ad_packages'  => 'Featured Ad Packages',
    'featured_ad_purchases' => 'Featured Ad Purchases',
    'ad_videos'          => 'Ad Videos (reels)',
    'reel_ad_placements' => 'Reel Ad Placements',
    // Finance
    'escrows'            => 'Escrows',
    'escrow_transactions'=> 'Escrow Transactions',
    'wallets'            => 'Wallets',
    'wallet_transactions'=> 'Wallet Transactions',
    'withdrawals'        => 'Withdrawals',
    'subscription_plans' => 'Subscription Plans',
    'shop_subscriptions' => 'Shop Subscriptions',
    // Content
    'blogs'              => 'Blogs',
    'faqs'               => 'FAQs',
    'support_tickets'    => 'Support Tickets',
    'ticket_issue_types' => 'Ticket Issue Types',
    'reviews'            => 'Reviews',
    'report_reasons'     => 'Report Reasons',
    'boosts'             => 'Boosts',
    'boost_purchases'    => 'Boost Purchases',
    // Config
    'roles'              => 'Roles',
    'permissions'        => 'Permissions',
    'languages'          => 'Languages',
    'social_auths'       => 'Social Auth configs',
    'payment_gateways'   => 'Payment Gateways',
    'banners'            => 'Banners',
    'categories'         => 'Categories',
    'category_attributes'=> 'Category Attributes',
    'menus'              => 'Menus',
    'notifications'      => 'Notifications',
];

foreach ($adminTables as $table => $label) {
    row("  $label ($table)", tbl('mysql', $table));
}

// ── LISTOCEAN DB ──────────────────────────────────────────────────────────────
echo "\n=== LISTOCEAN DB (frontend) ===\n\n";

$listoceanTables = [
    'listings'              => 'Listings',
    'users'                 => 'Users',
    'pages'                 => 'Pages (About/Terms/etc)',
    'faqs'                  => 'FAQs',
    'static_options'        => 'Static Options',
    'categories'            => 'Categories',
    'countries'             => 'Countries',
    'states'                => 'States',
    'cities'                => 'Cities',
    'banners'               => 'Banners',
    'blogs'                 => 'Blogs',
    'contact_us'            => 'Contact Us submissions',
    'escrows'               => 'Escrows',
    'wallet_transactions'   => 'Wallet Transactions',
    'withdrawals'           => 'Withdrawals',
    'ad_videos'             => 'Ad Videos (reels)',
    'ad_views'              => 'Ad Views',
    'ad_likes'              => 'Ad Likes',
    'listing_reports'       => 'Listing Reports',
    'notifications'         => 'Notifications',
    'chats'                 => 'Chat messages',
    'follows'               => 'Follows',
    'blocks'                => 'Blocks',
    'reviews'               => 'Reviews',
    'support_tickets'       => 'Support Tickets',
    'identity_verifications'=> 'Identity Verifications',
    'featured_ad_packages'  => 'Featured Ad Packages',
    'featured_ad_purchases' => 'Featured Ad Purchases',
    'memberships'           => 'Memberships',
    'membership_plans'      => 'Membership Plans',
    'site_advertisements'   => 'Site Advertisements',
    'coupon_usages'         => 'Coupon Usages',
    'boosts'                => 'Boosts',
    'boost_plans'           => 'Boost Plans',
    'purchase_histories'    => 'Purchase Histories',
    'social_logins'         => 'Social Logins',
    'reel_ad_placements'    => 'Reel Ad Placements',
    'promo_video_ads'       => 'Promo Video Ads',
    'commission_rules'      => 'Commission Rules',
    'orders'                => 'Orders',
    'riders'                => 'Riders',
    'flash_sales'           => 'Flash Sales',
];

foreach ($listoceanTables as $table => $label) {
    row("  $label ($table)", tbl('listocean', $table));
}

// ── LISTOCEAN: show all tables ────────────────────────────────────────────────
echo "\n=== ALL TABLES IN LISTOCEAN DB ===\n\n";
try {
    $tables = DB::connection('listocean')->select('SHOW TABLES');
    foreach ($tables as $t) {
        $vals = (array)$t;
        echo '  ' . reset($vals) . "\n";
    }
} catch(\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== ALL TABLES IN ADMIN DB ===\n\n";
try {
    $tables = DB::connection('mysql')->select('SHOW TABLES');
    foreach ($tables as $t) {
        $vals = (array)$t;
        echo '  ' . reset($vals) . "\n";
    }
} catch(\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\nDone.\n";
