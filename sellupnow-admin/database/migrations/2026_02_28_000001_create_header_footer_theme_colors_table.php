<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('header_footer_theme_colors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('header_hex');
            $table->string('footer_hex');
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('header_footer_theme_colors');
    }
};
