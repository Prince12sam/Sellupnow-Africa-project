<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('purchase_type');                   // subscription | featured_ad | other
            $table->unsignedBigInteger('package_id')->nullable(); // references featured_ad_packages.id or subscription_plans.id
            $table->double('amount')->default(0);
            $table->string('currency', 10)->default('USD');
            $table->string('payment_method')->nullable();      // paystack | paypal | stripe | etc.
            $table->string('transaction_reference')->nullable();
            $table->string('status')->default('pending');      // pending | completed | failed | refunded
            $table->json('meta')->nullable();                  // store raw gateway response if needed
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('transaction_reference');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_histories');
    }
};
