<?php
/**
 * Database consolidation: listocean → admin
 * Run from: /home/sellupnow/htdocs/www.sellupnow.com/main-file/listocean/core/
 * Usage: php migrate_to_admin.php
 */

// ── Bootstrap Laravel just enough for DB ──────────────────────────────────
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// ── Helpers ──────────────────────────────────────────────────────────────

function ok(string $msg): void   { echo "\033[32m[OK]\033[0m $msg\n"; }
function info(string $msg): void { echo "\033[36m[--]\033[0m $msg\n"; }
function err(string $msg): void  { echo "\033[31m[ERR]\033[0m $msg\n"; }
function skip(string $msg): void { echo "\033[33m[SKIP]\033[0m $msg\n"; }

function adminDb(): \Illuminate\Database\Connection
{
    return DB::connection('admin_db');  // connection defined in database.php
}

function listoceanDb(): \Illuminate\Database\Connection
{
    return DB::connection('mysql');     // listocean is default connection (for now)
}

/**
 * Add column to admin DB table if it doesn't already exist.
 */
function addColumnIfMissing(string $table, string $column, string $definition): void
{
    $exists = DB::connection('admin_db')
        ->selectOne("SELECT COUNT(*) as cnt FROM information_schema.columns
                     WHERE table_schema = DATABASE() AND table_name = ? AND column_name = ?",
                    [$table, $column]);

    if ($exists->cnt > 0) {
        skip("$table.$column already exists");
        return;
    }

    try {
        DB::connection('admin_db')->statement("ALTER TABLE `$table` ADD COLUMN `$column` $definition");
        ok("Added $table.$column");
    } catch (\Exception $e) {
        err("Failed $table.$column: " . $e->getMessage());
    }
}

// ── PHASE A: ALTER shared tables ─────────────────────────────────────────
echo "\n=== PHASE A: ALTER TABLE ===\n\n";

$alterations = [
    'blogs' => [
        ['admin_id',      'bigint unsigned NULL'],
        ['blog_content',  'longtext NULL'],
        ['author',        'varchar(191) NULL'],
        ['excerpt',       'longtext NULL'],
        ['views',         'varchar(191) NULL'],
        ['visibility',    'varchar(191) NULL'],
        ['featured',      'varchar(191) NULL'],
        ['schedule_date', 'varchar(191) NULL'],
        ['tag_name',      'varchar(191) NULL'],
        ['created_by',    'varchar(191) NULL'],
        ['status',        "enum('publish','draft','archive','schedule') NULL"],
    ],
    'boosts' => [
        ['user_id',        'bigint unsigned NULL'],
        ['amount_paid',    'decimal(10,2) NOT NULL DEFAULT 0.00'],
        ['payment_method', "varchar(100) NOT NULL DEFAULT 'wallet'"],
        ['boosted_at',     'timestamp NULL'],
        ['expires_at',     'timestamp NULL'],
        ['status',         "varchar(20) NOT NULL DEFAULT 'active'"],
    ],
    'brands' => [
        ['title',  'varchar(191) NULL'],
        ['url',    'varchar(191) NULL'],
        ['image',  'varchar(191) NULL'],
        ['status', 'tinyint NOT NULL DEFAULT 1'],
    ],
    'menus' => [
        ['content', 'longtext NULL'],
        ['status',  'varchar(191) NULL'],
    ],
    'membership_features' => [
        ['plan_id',       'bigint unsigned NULL'],
        ['feature_key',   'varchar(100) NULL'],
        ['feature_label', 'varchar(255) NULL'],
        ['value',         "varchar(255) NOT NULL DEFAULT 'true'"],
    ],
    'featured_ad_packages' => [
        ['currency',           'varchar(10) NULL'],
        ['advertisement_limit','int unsigned NOT NULL DEFAULT 1'],
        ['max_listings',       'int unsigned NOT NULL DEFAULT 1'],
        ['position',           "varchar(100) NOT NULL DEFAULT 'homepage_featured'"],
        ['sort_order',         'int unsigned NOT NULL DEFAULT 0'],
    ],
    'notifications' => [
        ['notifiable_id',   'bigint unsigned NULL'],
        ['notifiable_type', 'varchar(191) NULL'],
        ['message',         'text NULL'],
        ['read_at',         'timestamp NULL'],
    ],
    'ad_videos' => [
        ['thumbnail_url', 'varchar(2000) NULL'],
        ['caption',       'text NULL'],
        ['cta_text',      'varchar(191) NULL'],
        ['cta_url',       'varchar(2000) NULL'],
        ['is_sponsored',  'tinyint(1) NOT NULL DEFAULT 0'],
        ['is_approved',   'tinyint(1) NOT NULL DEFAULT 0'],
        ['approved_at',   'datetime NULL'],
        ['is_rejected',   'tinyint(1) NOT NULL DEFAULT 0'],
        ['reject_reason', 'text NULL'],
        ['rejected_at',   'timestamp NULL'],
        ['start_at',      'datetime NULL'],
        ['end_at',        'datetime NULL'],
        ['view_count',    'int unsigned NOT NULL DEFAULT 0'],
    ],
    'countries' => [
        ['country',      'varchar(191) NULL'],
        ['country_code', 'varchar(191) NULL'],
        ['dial_code',    'varchar(191) NULL'],
        ['status',       'tinyint NOT NULL DEFAULT 1'],
    ],
    'listings' => [
        ['admin_id',            'bigint unsigned NULL'],
        ['brand_id',            'bigint unsigned NULL'],
        ['state_id',            'bigint unsigned NULL'],
        ['city_id',             'bigint unsigned NULL'],
        ['video_url',           'text NULL'],
        ['video_is_approved',   'tinyint NOT NULL DEFAULT 0'],
        ['video_reject_reason', 'text NULL'],
        ['condition',           'varchar(191) NULL'],
        ['authenticity',        'varchar(191) NULL'],
        ['phone_hidden',        'tinyint(1) NOT NULL DEFAULT 0'],
        ['escrow_enabled',      'tinyint NOT NULL DEFAULT 0'],
    ],
    'pages' => [
        ['page_content',       'longtext NULL'],
        ['page_class',         'varchar(191) NULL'],
        ['layout',             'varchar(191) NULL'],
        ['sidebar_layout',     'varchar(191) NULL'],
        ['breadcrumb_status',  'varchar(191) NULL'],
        ['navbar_variant',     'varchar(191) NULL'],
        ['footer_variant',     'varchar(191) NULL'],
        ['widget_style',       'varchar(191) NULL'],
        ['left_column',        'varchar(191) NULL'],
        ['right_column',       'varchar(191) NULL'],
        ['visibility',         'varchar(191) NULL'],
        ['back_to_top',        'varchar(191) NULL'],
        ['page_builder_status','varchar(191) NULL'],
        ['status',             'varchar(191) NULL'],
    ],
    'wallets' => [
        ['currency', "varchar(10) NOT NULL DEFAULT 'GHS'"],
    ],
    'payment_gateways' => [
        ['slug',        'varchar(191) NULL'],
        ['image',       'varchar(191) NULL'],
        ['description', 'text NULL'],
        ['credentials', 'longtext NULL'],
        ['test_mode',   'tinyint(1) NOT NULL DEFAULT 0'],
        ['status',      'tinyint(1) NOT NULL DEFAULT 1'],
    ],
    'users' => [
        ['first_name',              'varchar(191) NULL'],
        ['username',                'varchar(191) NULL'],
        ['image',                   'varchar(191) NULL'],
        ['profile_background',      'varchar(191) NULL'],
        ['country_id',              'bigint NULL'],
        ['state_id',                'bigint NULL'],
        ['city_id',                 'bigint NULL'],
        ['post_code',               'varchar(191) NULL'],
        ['latitude',                'varchar(191) NULL'],
        ['longitude',               'varchar(191) NULL'],
        ['address',                 'text NULL'],
        ['about',                   'longtext NULL'],
        ['is_notifications_allowed','tinyint(1) NOT NULL DEFAULT 1'],
        ['is_contact_info_visible', 'tinyint(1) NOT NULL DEFAULT 0'],
        ['terms_condition',         'tinyint(1) NOT NULL DEFAULT 1'],
        ['google_id',               'varchar(191) NULL'],
        ['facebook_id',             'varchar(191) NULL'],
        ['apple_id',                'varchar(191) NULL'],
        ['email_verified',          'tinyint(1) NOT NULL DEFAULT 0'],
        ['email_verify_token',      'text NULL'],
        ['password_changed_at',     'timestamp NULL'],
        ['verified_status',         'tinyint(1) NOT NULL DEFAULT 0'],
        ['check_online_status',     'timestamp NULL'],
        ['is_suspend',              'tinyint(1) NOT NULL DEFAULT 0'],
        ['status',                  'tinyint(1) NOT NULL DEFAULT 1'],
    ],
];

adminDb()->statement('SET foreign_key_checks = 0');
foreach ($alterations as $table => $columns) {
    info("Table: $table");
    foreach ($columns as [$col, $def]) {
        addColumnIfMissing($table, $col, $def);
    }
}
adminDb()->statement('SET foreign_key_checks = 1');

// ── PHASE B: Data migration ───────────────────────────────────────────────
echo "\n=== PHASE B: DATA MIGRATION ===\n\n";

$adb = adminDb();
$ldb = listoceanDb();

adminDb()->statement('SET foreign_key_checks = 0');

/**
 * Simple wholesale copy (same columns in both DBs).
 */
function copyTable(string $table): void
{
    global $adb, $ldb;
    $rows = $ldb->table($table)->get()->map(fn($r) => (array)$r)->toArray();
    if (empty($rows)) { skip("$table: empty in listocean"); return; }

    $inserted = 0;
    foreach ($rows as $row) {
        try {
            $adb->table($table)->insertOrIgnore($row);
            $inserted++;
        } catch (\Exception $e) {
            err("$table row {$row['id']}: " . $e->getMessage());
        }
    }
    ok("$table: $inserted/" . count($rows) . " rows copied");
}

// Simple full-copy tables
foreach (['cities', 'states', 'static_options', 'media_uploads', 'membership_plans',
          'page_builders', 'widgets', 'visitors', 'identity_verifications',
          'advertisements', 'frontend_ad_slots', 'wallet_histories', 'admins',
          'meta_data'] as $tbl) {
    copyTable($tbl);
}

// pages: explicit columns (avoids admin's url/is_default columns being required)
info("Table: pages");
$pages = $ldb->table('pages')->get()->map(fn($r) => (array)$r)->toArray();
$pInserted = 0;
foreach ($pages as $page) {
    $row = [
        'id'                 => $page['id'],
        'title'              => $page['title'] ?? null,
        'slug'               => $page['slug']  ?? null,
        'page_content'       => $page['page_content'] ?? null,
        'page_class'         => $page['page_class'] ?? null,
        'layout'             => $page['layout'] ?? null,
        'sidebar_layout'     => $page['sidebar_layout'] ?? null,
        'breadcrumb_status'  => $page['breadcrumb_status'] ?? null,
        'navbar_variant'     => $page['navbar_variant'] ?? null,
        'footer_variant'     => $page['footer_variant'] ?? null,
        'widget_style'       => $page['widget_style'] ?? null,
        'left_column'        => $page['left_column'] ?? null,
        'right_column'       => $page['right_column'] ?? null,
        'visibility'         => $page['visibility'] ?? null,
        'back_to_top'        => $page['back_to_top'] ?? null,
        'page_builder_status'=> $page['page_builder_status'] ?? null,
        'status'             => $page['status'] ?? null,
        'created_at'         => $page['created_at'] ?? null,
        'updated_at'         => $page['updated_at'] ?? null,
    ];
    try {
        $adb->table('pages')->insertOrIgnore($row);
        $pInserted++;
    } catch (\Exception $e) {
        err("pages row {$page['id']}: " . $e->getMessage());
    }
}
ok("pages: $pInserted/" . count($pages) . " rows copied");

// menus: pull available columns dynamically
info("Table: menus");
$menuCols = array_column($ldb->select("SHOW COLUMNS FROM menus"), 'Field');
$menus = $ldb->table('menus')->get()->map(fn($r) => (array)$r)->toArray();
$mInserted = 0;
foreach ($menus as $menu) {
    $adminCols = array_column($adb->select("SHOW COLUMNS FROM menus"), 'Field');
    $row = array_intersect_key($menu, array_flip($adminCols));
    try {
        $adb->table('menus')->insertOrIgnore($row);
        $mInserted++;
    } catch (\Exception $e) {
        err("menus row {$menu['id']}: " . $e->getMessage());
    }
}
ok("menus: $mInserted/" . count($menus) . " rows copied");

// listings: use intersection of src/dest columns plus newly added ones
info("Table: listings");
$listings = $ldb->table('listings')->get()->map(fn($r) => (array)$r)->toArray();
$lInserted = 0;
if (!empty($listings)) {
    $adminListingCols = array_column($adb->select("SHOW COLUMNS FROM listings"), 'Field');
    foreach ($listings as $listing) {
        $row = array_intersect_key($listing, array_flip($adminListingCols));
        try {
            $adb->table('listings')->insertOrIgnore($row);
            $lInserted++;
        } catch (\Exception $e) {
            err("listings row {$listing['id']}: " . $e->getMessage());
        }
    }
}
ok("listings: $lInserted/" . count($listings) . " rows copied");

// users: map first_name+last_name → name; merge all available columns
info("Table: users");
$lusers = $ldb->table('users')->get()->map(fn($r) => (array)$r)->toArray();
$uInserted = 0;
if (!empty($lusers)) {
    $adminUserCols = array_column($adb->select("SHOW COLUMNS FROM users"), 'Field');
    foreach ($lusers as $lu) {
        // Check if this user's email already exists in admin (avoid duplicate)
        $adminExists = $adb->table('users')->where('email', $lu['email'])->exists();
        if ($adminExists) {
            skip("users: email {$lu['email']} already in admin, updating missing fields only");
            $row = array_intersect_key($lu, array_flip($adminUserCols));
            unset($row['id'], $row['email']); // don't overwrite PK or email
            // Compose name from first+last if missing
            if (empty($row['name']) && isset($lu['first_name'])) {
                $row['name'] = trim(($lu['first_name'] ?? '') . ' ' . ($lu['last_name'] ?? ''));
            }
            $adb->table('users')->where('email', $lu['email'])->update($row);
            continue;
        }

        $row = array_intersect_key($lu, array_flip($adminUserCols));
        // Compose name from listocean's first_name + last_name
        if (empty($row['name']) || trim($row['name']) === '') {
            $row['name'] = trim(($lu['first_name'] ?? '') . ' ' . ($lu['last_name'] ?? ''));
        }
        try {
            $adb->table('users')->insertOrIgnore($row);
            $uInserted++;
        } catch (\Exception $e) {
            err("users row {$lu['id']}: " . $e->getMessage());
        }
    }
}
ok("users: $uInserted new rows inserted");

adminDb()->statement('SET foreign_key_checks = 1');

// ── PHASE C: Register listocean migrations in admin ───────────────────────
echo "\n=== PHASE C: REGISTER MIGRATIONS ===\n\n";

$lMigrations = $ldb->table('migrations')->pluck('migration')->toArray();
$aMigrations = $adb->table('migrations')->pluck('migration')->toArray();
$missing = array_diff($lMigrations, $aMigrations);

if (empty($missing)) {
    skip("All listocean migrations already registered in admin");
} else {
    $nextBatch = ($adb->table('migrations')->max('batch') ?? 0) + 1;
    $rows = array_map(fn($m) => ['migration' => $m, 'batch' => $nextBatch], $missing);
    $adb->table('migrations')->insert($rows);
    ok("Registered " . count($missing) . " listocean migrations in admin (batch $nextBatch)");
}

echo "\n=== DONE ===\n";
echo "Next step: edit listocean/core/.env — change DB_DATABASE=admin, DB_USERNAME=admin, DB_PASSWORD=nU7Ak80lRYUO4QkqPfxw\n\n";
