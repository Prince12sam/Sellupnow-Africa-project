<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVideoIsApprovedToListings extends Migration
{
    public function up()
    {
        Schema::table('listings', function (Blueprint $table) {
            if (! Schema::hasColumn('listings', 'video_is_approved')) {
                $table->tinyInteger('video_is_approved')->default(0)->after('video_url')
                    ->comment('0=pending, 1=approved, 2=rejected');
            }
            if (! Schema::hasColumn('listings', 'video_reject_reason')) {
                $table->text('video_reject_reason')->nullable()->after('video_is_approved');
            }
        });
    }

    public function down()
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->dropColumn(['video_is_approved', 'video_reject_reason']);
        });
    }
}
