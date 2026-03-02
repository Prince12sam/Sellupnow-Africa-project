<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFeaturedAdPackagesTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('featured_ad_packages')) {
            return;
        }

        Schema::create('featured_ad_packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2)->default(0.00);
            $table->unsignedInteger('duration_days')->default(7);
            // position: homepage_featured | category_top | search_top
            $table->string('position', 100)->default('homepage_featured');
            // max concurrent listings a user can feature under this package at once
            $table->unsignedInteger('max_listings')->default(1);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('featured_ad_packages');
    }
}
