<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('featured_ad_packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->double('price')->default(0);
            $table->unsignedInteger('duration_days')->default(7);       // how many days the feature lasts
            $table->unsignedInteger('ads_limit')->default(1);           // how many ads can be featured
            $table->string('badge_color')->nullable();                   // UI badge colour (hex)
            $table->boolean('is_popular')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('featured_ad_packages');
    }
};
