<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFeaturedAdPurchasesTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('featured_ad_purchases')) {
            return;
        }

        Schema::create('featured_ad_purchases', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('package_id');
            $table->unsignedBigInteger('listing_id');
            $table->decimal('amount_paid', 10, 2)->default(0.00);
            $table->string('payment_method', 100)->default('wallet');
            $table->string('payment_reference', 255)->nullable();
            $table->timestamp('purchased_at')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('listing_id');
            $table->index('package_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('featured_ad_purchases');
    }
}
