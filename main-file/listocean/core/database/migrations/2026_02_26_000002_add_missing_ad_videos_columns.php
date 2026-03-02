<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMissingAdVideosColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * This migration is intentionally defensive: it only adds columns if they
     * do not already exist to avoid destructive changes on an existing DB.
     */
    public function up()
    {
        if (! Schema::hasTable('ad_videos')) {
            return;
        }

        Schema::table('ad_videos', function (Blueprint $table) {
            if (! Schema::hasColumn('ad_videos', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('id');
            }
            if (! Schema::hasColumn('ad_videos', 'cta_text')) {
                $table->string('cta_text')->nullable()->after('caption');
            }
            if (! Schema::hasColumn('ad_videos', 'cta_url')) {
                $table->string('cta_url')->nullable()->after('cta_text');
            }
            if (! Schema::hasColumn('ad_videos', 'start_at')) {
                $table->dateTime('start_at')->nullable()->after('cta_url');
            }
            if (! Schema::hasColumn('ad_videos', 'end_at')) {
                $table->dateTime('end_at')->nullable()->after('start_at');
            }
            if (! Schema::hasColumn('ad_videos', 'is_sponsored')) {
                $table->boolean('is_sponsored')->default(false)->after('end_at');
            }
            if (! Schema::hasColumn('ad_videos', 'approved_at')) {
                $table->dateTime('approved_at')->nullable()->after('is_approved');
            }
            if (! Schema::hasColumn('ad_videos', 'is_rejected')) {
                $table->boolean('is_rejected')->default(false)->after('approved_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * We avoid dropping columns on rollback to be safe in this development
     * environment — leaving columns in place is non-destructive.
     */
    public function down()
    {
        // Intentionally empty to avoid destructive rollbacks.
    }
}
