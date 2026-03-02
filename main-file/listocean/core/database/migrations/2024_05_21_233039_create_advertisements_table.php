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
        Schema::create('advertisements', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('type')->nullable();
            $table->string('size')->nullable();
            $table->string('image')->nullable();
            $table->string('slot')->nullable();
            $table->longText('embed_code')->nullable();
            $table->longText('redirect_url')->nullable();
            $table->unsignedBigInteger('click')->default(0);
            $table->unsignedBigInteger('impression')->default(0);
            $table->unsignedBigInteger('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('advertisements');
    }
};
