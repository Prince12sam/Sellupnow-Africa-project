<?php
chdir(__DIR__ . '/..');
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use Illuminate\Support\Facades\DB;

function c($conn, $table) {
    try { return DB::connection($conn)->table($table)->count(); }
    catch (\Exception $e) { return 'MISSING'; }
}

echo "\n== LISTOCEAN DB — resolved table names ==\n\n";
$lo = 'listocean';
$checks = [
    // core features
    'listings'              => 'Listings',
    'users'                 => 'Users (registered)',
    'guest_listings'        => 'Guest Listings (no-login posts)',
    'ad_videos'             => 'Ad Videos (reels/video uploads)',
    'reel_comments'         => 'Reel Comments',
    'reel_ad_placements'    => 'Reel Ad Placements',
    'advertisements'        => 'Site Advertisements',
    'frontend_ad_slots'     => 'Frontend Ad Slots (defined)',
    'marketplace_banners'   => 'Marketplace Banners',
    // escrow
    'escrow_transactions'   => 'Escrow Transactions',
    'escrow_events'         => 'Escrow Events',
    // wallet
    'wallets'               => 'Wallets',
    'wallet_histories'      => 'Wallet Histories (top-ups/transactions)',
    // memberships
    'membership_types'      => 'Membership Types',
    'membership_plans'      => 'Membership Plans',
    'user_memberships'      => 'User Memberships (active)',
    'membership_features'   => 'Membership Features',
    'membership_histories'  => 'Membership Histories',
    // featured ads
    'featured_ad_packages'  => 'Featured Ad Packages',
    'featured_ad_purchases' => 'Featured Ad Purchases',
    'featured_ad_activations'=> 'Featured Ad Activations',
    // identity
    'identity_verifications'=> 'Identity Verifications',
    'identity_verification_audits' => 'Identity Verification Audits',
    // social / chats
    'chat_messages'         => 'Chat Messages',
    'live_chats'            => 'Live Chats (chat threads)',
    'live_chat_messages'    => 'Live Chat Messages',
    'blocked_users'         => 'Blocked Users',
    'social_icons'          => 'Social Icons',
    // content
    'pages'                 => 'Pages (About/Terms/Privacy etc)',
    'blogs'                 => 'Blogs',
    'blog_comments'         => 'Blog Comments',
    'notices'               => 'Notices',
    'enquiries'             => 'Enquiries (contact form)',
    // categories
    'categories'            => 'Categories',
    'sub_categories'        => 'Sub-Categories',
    'child_categories'      => 'Child Categories',
    'listing_attributes'    => 'Listing Attributes',
    // location
    'countries'             => 'Countries',
    'states'                => 'States',
    'cities'                => 'Cities',
    // other
    'reviews'               => 'Reviews',
    'listing_reports'       => 'Listing Reports',
    'report_reasons'        => 'Report Reasons',
    'listing_favorites'     => 'Listing Favourites',
    'listing_tags'          => 'Listing Tags',
    'tags'                  => 'Tags',
    'tickets'               => 'Support Tickets',
    'departments'           => 'Support Departments',
    'payment_gateways'      => 'Payment Gateways',
    'permissions'           => 'Permissions',
    'roles'                 => 'Roles',
    'languages'             => 'Languages',
    'menus'                 => 'Menus',
    'page_builders'         => 'Page Builders',
    'form_builders'         => 'Form Builders',
    'static_options'        => 'Static Options (key-value settings)',
    'visitors'              => 'Visitors',
    'news_letters'          => 'Newsletter Subscribers',
    'boosts'                => 'Boosts',
    'xg_payment_meta'       => 'Payment Meta (xg_payment_meta)',
    'xg_ftp_infos'          => 'FTP Info (xg_ftp_infos)',
];

foreach ($checks as $tbl => $label) {
    $count = c($lo, $tbl);
    $flag = is_int($count)
        ? ($count === 0 ? ' [EMPTY]' : " [$count rows]")
        : " [$count]";
    echo str_pad("  $label", 48) . str_pad("($tbl)", 35) . $flag . "\n";
}

echo "\n== ADMIN DB — resolved table names ==\n\n";
$adm = 'mysql';
$adminChecks = [
    'orders'             => 'Orders',
    'order_products'     => 'Order Products (line items)',
    'carts'              => 'Carts',
    'drivers'            => 'Drivers / Riders (actual table)',
    'driver_orders'      => 'Driver Order Assignments',
    'flash_sales'        => 'Flash Sales',
    'flash_sale_products'=> 'Flash Sale Products',
    'vat_taxes'          => 'VAT / Tax rules',
    'coupons'            => 'Coupons',
    'admin_coupons'      => 'Admin Coupons (coupon-specific table)',
    'brands'             => 'Brands',
    'currencies'         => 'Currencies',
    'delivery_charges'   => 'Delivery Charges',
    'products'           => 'Products',
    'pos_carts'          => 'POS Carts',
    'pos_cart_products'  => 'POS Cart Products',
    'commission_rules'   => 'Commission Rules',
    'membership_features'=> 'Membership Features',
    'offer_banners'      => 'Offer Banners',
    'listings'           => 'Listings (admin)',
    'customers'          => 'Customers (Flutter app users)',
    'shop_user'          => 'Shop Users (web users) — actual table name',
    'shops'              => 'Shops',
    'employees'          => 'Employees (staff accounts)',
    'ad_videos'          => 'Ad Videos (admin DB)',
    'ads'                => 'Ads (promoted listings)',
    'banners'            => 'Banners',
    'promo_impressions'  => 'Promo Impressions',
    'wallets'            => 'Wallets (admin)',
    'withdraws'          => 'Withdrawals (admin — actual table)',
    'transactions'       => 'Transactions (admin)',
    'subscription_plans' => 'Subscription Plans (Flutter)',
    'shop_subscriptions' => 'Shop Subscriptions',
    'blogs'              => 'Blogs (admin)',
    'faqs'               => 'FAQs (admin)',
    'support_tickets'    => 'Support Tickets (admin)',
    'supports'           => 'Support messages (admin)',
    'ticket_issue_types' => 'Ticket Issue Types',
    'tips'               => 'Tips / Helpful Hints (API)',
    'roles'              => 'Roles',
    'permissions'        => 'Permissions',
    'legal_pages'        => 'Legal Pages (admin legal_pages table)',
    'pages'              => 'Pages (admin pages table)',
    'menus'              => 'Menus',
    'purchase_histories' => 'Purchase Histories',
    'financial_audits'   => 'Financial Audits',
    'whats_app_contacts' => 'WhatsApp Contacts',
    'whats_app_messages' => 'WhatsApp Messages',
    'shop_user_chats'    => 'Shop User Chats (in-app chat)',
    'payment_gateways'   => 'Payment Gateways (admin)',
    'social_auths'       => 'Social Auth configs',
    'social_links'       => 'Social Links (footer icons)',
    'categories'         => 'Categories (admin — for ecom products)',
    'home_themes'        => 'Home Themes',
    'generate_settings'  => 'General Settings',
    'module_settings'    => 'Module Settings',
    'verify_manages'     => 'Verification Settings',
    'user_verifications' => 'User Verifications',
    'auction_bids'       => 'Auction Bids',
    'favorites'          => 'Favorites (admin)',
    'follows'            => 'Follows',
    'blocks'             => 'Blocks',
    'device_keys'        => 'Device Keys (push notifications)',
    'id_proof_types'     => 'ID Proof Types',
];

foreach ($adminChecks as $tbl => $label) {
    $count = c($adm, $tbl);
    $flag = is_int($count)
        ? ($count === 0 ? ' [EMPTY]' : " [$count rows]")
        : " [$count]";
    echo str_pad("  $label", 48) . str_pad("($tbl)", 35) . $flag . "\n";
}

echo "\nDone.\n";
