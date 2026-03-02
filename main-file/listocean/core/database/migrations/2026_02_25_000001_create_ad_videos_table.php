<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdVideosTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('ad_videos')) {
            return;
        }

        Schema::create('ad_videos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->comment('NULL = admin-posted');
            $table->text('video_url');
            $table->string('thumbnail_url', 2000)->nullable();
            $table->text('caption')->nullable();
            $table->string('cta_text')->nullable()->comment('e.g. Shop Now');
            $table->string('cta_url', 2000)->nullable();
            $table->boolean('is_sponsored')->default(false);
            $table->boolean('is_approved')->default(false);
            $table->dateTime('approved_at')->nullable();
            $table->boolean('is_rejected')->default(false);
            $table->text('reject_reason')->nullable();
            $table->dateTime('start_at')->nullable();
            $table->dateTime('end_at')->nullable();
            $table->unsignedInteger('view_count')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ad_videos');
    }
}
