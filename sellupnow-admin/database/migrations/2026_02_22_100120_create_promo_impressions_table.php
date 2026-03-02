<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('promo_impressions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ad_video_id');
            $table->unsignedBigInteger('advertiser_id')->nullable();
            $table->dateTime('period_start')->nullable();
            $table->integer('impressions')->default(0);
            $table->integer('clicks')->default(0);
            $table->decimal('billed_amount', 12, 2)->default(0);
            $table->timestamp('billed_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('promo_impressions');
    }
};
