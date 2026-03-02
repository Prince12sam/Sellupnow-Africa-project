<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ad_video_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('ad_video_id')->constrained('ad_videos')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'ad_video_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ad_video_likes');
    }
};
