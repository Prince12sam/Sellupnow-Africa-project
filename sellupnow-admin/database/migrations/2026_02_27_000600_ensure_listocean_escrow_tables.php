<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'listocean';

    public function up(): void
    {
        if (!Schema::connection($this->connection)->hasTable('escrow_transactions')) {
            Schema::connection($this->connection)->create('escrow_transactions', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('listing_id')->nullable();
                $table->unsignedBigInteger('buyer_user_id')->nullable();
                $table->unsignedBigInteger('seller_user_id')->nullable();
                $table->decimal('listing_price', 15, 2)->default(0);
                $table->decimal('admin_fee_amount', 15, 2)->default(0);
                $table->decimal('total_amount', 15, 2)->default(0);
                $table->string('currency', 10)->nullable();
                $table->string('status', 40)->default('payment_pending');
                $table->string('payment_gateway', 60)->nullable();
                $table->string('payment_transaction_id', 191)->nullable();
                $table->timestamp('funded_at')->nullable();
                $table->timestamp('seller_accepted_at')->nullable();
                $table->timestamp('seller_delivered_at')->nullable();
                $table->timestamp('buyer_confirmed_at')->nullable();
                $table->timestamp('released_at')->nullable();
                $table->timestamp('buyer_confirm_deadline_at')->nullable();
                $table->timestamp('seller_accept_deadline_at')->nullable();
                $table->timestamps();

                $table->index(['status']);
                $table->index(['listing_id']);
                $table->index(['buyer_user_id']);
                $table->index(['seller_user_id']);
                $table->index(['payment_transaction_id']);
            });
        }

        if (!Schema::connection($this->connection)->hasTable('escrow_events')) {
            Schema::connection($this->connection)->create('escrow_events', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('escrow_transaction_id');
                $table->unsignedBigInteger('actor_user_id')->nullable();
                $table->string('actor_type', 30)->nullable();
                $table->string('event', 80)->nullable();
                $table->string('from_status', 40)->nullable();
                $table->string('to_status', 40)->nullable();
                $table->text('note')->nullable();
                $table->timestamps();

                $table->index(['escrow_transaction_id']);
                $table->index(['actor_user_id']);
            });
        }
    }

    public function down(): void
    {
        // Intentionally non-destructive hotfix migration.
    }
};
