<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Security hardening for the featured-ad system:
 *
 *  1. featured_ad_purchases  → add duration_days_at_purchase (snapshot of the
 *     package's duration_days at time of purchase, immune to future admin edits).
 *
 *  2. featured_ad_activations → deactivate any orphan rows where ends_at is NULL.
 *     MySQL treats NULL in "WHERE ends_at >= now()" as false, so those rows would
 *     never appear as featured — but we also flip is_active to 0 so they're
 *     explicitly marked expired and the cron ignores them.
 */
class HardenFeaturedAdTables extends Migration
{
    public function up(): void
    {
        // ── 1. Snapshot column on purchases ───────────────────────────────────
        if (Schema::hasTable('featured_ad_purchases') &&
            !Schema::hasColumn('featured_ad_purchases', 'duration_days_at_purchase')) {
            Schema::table('featured_ad_purchases', function (Blueprint $table) {
                // Nullable so existing rows (without the value) don't break.
                // The application always writes this for NEW purchases.
                $table->unsignedSmallInteger('duration_days_at_purchase')
                      ->nullable()
                      ->after('amount_paid')
                      ->comment('Snapshot of package duration_days at time of purchase');
            });
        }

        // ── 2. Deactivate orphan NULL ends_at rows ─────────────────────────────
        // These would never show up in feeds (MySQL NULL comparison = false) but
        // we mark them inactive explicitly to keep the data clean.
        if (Schema::hasTable('featured_ad_activations')) {
            DB::table('featured_ad_activations')
                ->whereNull('ends_at')
                ->update(['is_active' => 0]);
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('featured_ad_purchases') &&
            Schema::hasColumn('featured_ad_purchases', 'duration_days_at_purchase')) {
            Schema::table('featured_ad_purchases', function (Blueprint $table) {
                $table->dropColumn('duration_days_at_purchase');
            });
        }
    }
}
