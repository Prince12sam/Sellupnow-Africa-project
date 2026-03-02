<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('frontend_ad_slots') && !Schema::hasColumn('frontend_ad_slots', 'listing_id')) {
            Schema::table('frontend_ad_slots', function (Blueprint $table) {
                $table->unsignedBigInteger('listing_id')->nullable()->after('slot_key')->index();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('frontend_ad_slots') && Schema::hasColumn('frontend_ad_slots', 'listing_id')) {
            Schema::table('frontend_ad_slots', function (Blueprint $table) {
                $table->dropColumn('listing_id');
            });
        }
    }
};
