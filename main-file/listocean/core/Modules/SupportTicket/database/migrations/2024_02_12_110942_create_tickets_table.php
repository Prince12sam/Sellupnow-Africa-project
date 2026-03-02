<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('department_id');
            $table->bigInteger('admin_id')->nullable();
            $table->bigInteger('user_id')->nullable();
            $table->text('title')->nullable();
            $table->text('subject')->nullable();
            $table->string('priority')->nullable();
            $table->string('status')->default('open');
            $table->text('via')->nullable()->comment('admin, user');
            $table->string('operating_system')->nullable();
            $table->string('user_agent')->nullable();
            $table->longText('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
