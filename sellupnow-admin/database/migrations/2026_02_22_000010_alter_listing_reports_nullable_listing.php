<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Make listing_reports.listing_id nullable so we can store user/video reports
 * that have no associated listing, and add report_type + reported_user_id.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('listing_reports', function (Blueprint $table) {
            $table->unsignedBigInteger('listing_id')->nullable()->change();
            $table->string('report_type')->default('listing')->after('listing_id'); // listing | user | video
            $table->unsignedBigInteger('reported_user_id')->nullable()->after('report_type');
        });
    }

    public function down(): void
    {
        Schema::table('listing_reports', function (Blueprint $table) {
            $table->dropColumn(['report_type', 'reported_user_id']);
            $table->unsignedBigInteger('listing_id')->nullable(false)->change();
        });
    }
};
