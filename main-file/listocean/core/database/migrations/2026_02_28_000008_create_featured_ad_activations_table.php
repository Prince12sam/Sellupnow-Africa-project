<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFeaturedAdActivationsTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('featured_ad_activations')) {
            return;
        }

        Schema::create('featured_ad_activations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purchase_id');
            $table->unsignedBigInteger('listing_id');
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('listing_id');
            $table->index(['is_active', 'ends_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('featured_ad_activations');
    }
}
