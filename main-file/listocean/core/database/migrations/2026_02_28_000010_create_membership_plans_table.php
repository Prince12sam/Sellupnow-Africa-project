<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMembershipPlansTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('membership_plans')) {
            return;
        }

        Schema::create('membership_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2)->default(0.00);
            // billing_period: monthly | yearly | lifetime
            $table->string('billing_period', 20)->default('monthly');
            // How many listings this plan allows per billing period (0 = unlimited)
            $table->unsignedInteger('listing_quota')->default(0);
            // How many listings are auto-featured per billing period
            $table->unsignedInteger('auto_feature_count')->default(0);
            $table->string('badge_label', 100)->nullable()->comment('e.g. Pro, Gold, Verified Seller');
            $table->string('badge_color', 20)->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('membership_plans');
    }
}
