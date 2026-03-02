<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('membership_plans') && !Schema::hasColumn('membership_plans', 'banner_ad_quota')) {
            Schema::table('membership_plans', function (Blueprint $table) {
                $table->smallInteger('banner_ad_quota')->default(0)->after('video_quota')
                    ->comment('0 = none, -1 = unlimited, N = max N banner ad requests');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('membership_plans') && Schema::hasColumn('membership_plans', 'banner_ad_quota')) {
            Schema::table('membership_plans', function (Blueprint $table) {
                $table->dropColumn('banner_ad_quota');
            });
        }
    }
};
