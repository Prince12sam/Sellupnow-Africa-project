<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReelAdPlacementsTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('reel_ad_placements')) {
            return;
        }

        Schema::create('reel_ad_placements', function (Blueprint $table) {
            $table->id();
            // Links to advertisements.id (for banner-style ad units)
            $table->unsignedBigInteger('advertisement_id')->nullable();
            // reel_type: listing | ad_video
            $table->string('reel_type', 50);
            // reel_id: listings.id OR ad_videos.id depending on reel_type
            $table->unsignedBigInteger('reel_id')->nullable();
            // placement: bottom_overlay | bottom_overlay_2
            $table->string('placement', 50);
            // Nth position in the reel feed (e.g. 5 = inject at every 5th reel)
            $table->unsignedInteger('slot_position')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();

            $table->index('is_active');
        });
    }

    public function down()
    {
        Schema::dropIfExists('reel_ad_placements');
    }
}
