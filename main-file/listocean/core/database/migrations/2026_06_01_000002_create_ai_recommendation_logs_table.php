<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('ai_recommendation_logs')) {
            Schema::create('ai_recommendation_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('listing_id')->nullable()->index();
                $table->string('model', 100)->default('gpt-4o-mini');
                $table->text('prompt')->nullable();
                $table->text('response')->nullable();
                $table->unsignedInteger('prompt_tokens')->default(0);
                $table->unsignedInteger('completion_tokens')->default(0);
                $table->boolean('success')->default(true);
                $table->text('error_message')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_recommendation_logs');
    }
};
