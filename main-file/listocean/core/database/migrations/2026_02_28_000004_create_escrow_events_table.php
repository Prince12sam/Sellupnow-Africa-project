<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEscrowEventsTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('escrow_events')) {
            return;
        }

        Schema::create('escrow_events', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('escrow_transaction_id');
            // event: funded | seller_accepted | seller_delivered | buyer_confirmed
            //        released | refunded | disputed | admin_released | admin_refunded | admin_note
            $table->string('event', 100);
            // actor_type: buyer | seller | admin | system
            $table->string('actor_type', 50);
            $table->unsignedBigInteger('actor_user_id')->nullable();
            $table->string('from_status', 50)->nullable();
            $table->string('to_status', 50)->nullable();
            $table->text('note')->nullable();
            $table->timestamp('created_at')->nullable();

            $table->index('escrow_transaction_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('escrow_events');
    }
}
