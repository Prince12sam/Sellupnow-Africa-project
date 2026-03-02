<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBoostsTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('boosts')) {
            return;
        }

        Schema::create('boosts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('listing_id');
            $table->unsignedBigInteger('user_id');
            $table->decimal('amount_paid', 10, 2)->default(0.00);
            $table->string('payment_method', 100)->default('wallet');
            $table->timestamp('boosted_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            // status: active | expired | cancelled
            $table->string('status', 20)->default('active');
            $table->timestamps();

            $table->index('listing_id');
            $table->index(['status', 'expires_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('boosts');
    }
}
