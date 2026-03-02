<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserMembershipsTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('user_memberships')) {
            return;
        }

        Schema::create('user_memberships', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('plan_id');
            $table->decimal('amount_paid', 10, 2)->default(0.00);
            $table->string('payment_method', 100)->default('wallet');
            $table->string('payment_reference', 255)->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            // status: active | cancelled | expired
            $table->string('status', 20)->default('active');
            // How many listings posted in the current billing period
            $table->unsignedInteger('listings_used')->default(0);
            // How many auto-features used in the current billing period
            $table->unsignedInteger('auto_features_used')->default(0);
            $table->timestamps();

            $table->index('user_id');
            $table->index(['status', 'expires_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_memberships');
    }
}
