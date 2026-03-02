<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('commission_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('scope')->default('global'); // global, category, shop
            $table->unsignedBigInteger('scope_id')->nullable();
            $table->decimal('percentage', 8, 3)->default(0); // percentage taken by platform
            $table->decimal('fixed', 12, 2)->default(0); // fixed fee
            $table->json('conditions')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('commission_rules');
    }
};
