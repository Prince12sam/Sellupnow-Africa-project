<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Stub tables — all three are now created in full by dedicated migrations
        // (2026_02_25_000001, 2026_02_28_000003, 2026_02_28_000005).
        // Guards here prevent conflicts if this migration runs after those.

        if (! Schema::hasTable('ad_videos')) {
            Schema::create('ad_videos', function (Blueprint $table) {
                $table->id();
                $table->string('video_url')->nullable();
                $table->string('thumbnail_url')->nullable();
                $table->text('caption')->nullable();
                $table->boolean('is_approved')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('reel_ad_placements')) {
            Schema::create('reel_ad_placements', function (Blueprint $table) {
                $table->id();
                $table->string('placement_name')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('escrow_transactions')) {
            Schema::create('escrow_transactions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->decimal('amount', 16, 2)->default(0);
                $table->string('status')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ad_videos');
        Schema::dropIfExists('reel_ad_placements');
        Schema::dropIfExists('escrow_transactions');
    }
};
