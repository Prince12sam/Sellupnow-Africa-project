<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Aligns the membership_plans and featured_ad_packages tables with
 * the actual column names the admin controllers insert/update.
 *
 * membership_plans:
 *   - adds duration_days (controller uses this instead of billing_period)
 *   - adds currency
 *   - adds features (JSON — admin stores feature list here directly)
 *
 * featured_ad_packages:
 *   - adds advertisement_limit (controller uses this instead of max_listings)
 *   - adds currency
 */
class AlignControllerColumnNames extends Migration
{
    public function up(): void
    {
        // ── membership_plans ────────────────────────────────────────────────
        if (Schema::hasTable('membership_plans')) {
            Schema::table('membership_plans', function (Blueprint $table) {
                if (! Schema::hasColumn('membership_plans', 'duration_days')) {
                    $table->unsignedInteger('duration_days')->default(30)->after('price');
                }
                if (! Schema::hasColumn('membership_plans', 'currency')) {
                    $table->string('currency', 10)->nullable()->after('duration_days');
                }
                if (! Schema::hasColumn('membership_plans', 'features')) {
                    // Stores feature list as JSON (e.g. ["video_reels","verified_badge"])
                    $table->json('features')->nullable()->after('currency');
                }
            });
        }

        // ── featured_ad_packages ─────────────────────────────────────────────
        if (Schema::hasTable('featured_ad_packages')) {
            Schema::table('featured_ad_packages', function (Blueprint $table) {
                if (! Schema::hasColumn('featured_ad_packages', 'advertisement_limit')) {
                    // Max number of listings the buyer can feature under this package at once
                    $table->unsignedInteger('advertisement_limit')->default(1)->after('duration_days');
                }
                if (! Schema::hasColumn('featured_ad_packages', 'currency')) {
                    $table->string('currency', 10)->nullable()->after('price');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('membership_plans')) {
            Schema::table('membership_plans', function (Blueprint $table) {
                $table->dropColumn(['duration_days', 'currency', 'features']);
            });
        }
        if (Schema::hasTable('featured_ad_packages')) {
            Schema::table('featured_ad_packages', function (Blueprint $table) {
                $table->dropColumn(['advertisement_limit', 'currency']);
            });
        }
    }
}
