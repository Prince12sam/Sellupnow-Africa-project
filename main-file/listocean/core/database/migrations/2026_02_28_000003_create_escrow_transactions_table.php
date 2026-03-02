<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEscrowTransactionsTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('escrow_transactions')) {
            return;
        }

        Schema::create('escrow_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('listing_id');
            $table->unsignedBigInteger('buyer_user_id');
            $table->unsignedBigInteger('seller_user_id');
            $table->decimal('listing_price', 15, 2);
            $table->decimal('admin_fee_amount', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2);
            $table->string('currency', 10)->default('GHS');
            $table->enum('status', [
                'payment_pending',
                'funded',
                'seller_confirmed',
                'seller_delivered',
                'released',
                'refunded',
                'disputed',
            ])->default('payment_pending');
            $table->string('payment_gateway', 100)->nullable();
            $table->string('payment_transaction_id', 255)->nullable();
            $table->timestamp('funded_at')->nullable();
            $table->timestamp('seller_accepted_at')->nullable();
            $table->timestamp('seller_delivered_at')->nullable();
            $table->timestamp('buyer_confirmed_at')->nullable();
            $table->timestamp('released_at')->nullable();
            $table->timestamp('buyer_confirm_deadline_at')->nullable();
            $table->timestamp('seller_accept_deadline_at')->nullable();
            $table->text('admin_note')->nullable();
            $table->timestamps();

            $table->index('listing_id');
            $table->index('buyer_user_id');
            $table->index('seller_user_id');
            $table->index('status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('escrow_transactions');
    }
}
