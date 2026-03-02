<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('id_proof_type');                                      // e.g. "National ID Card"
            $table->string('id_proof_number')->nullable();
            $table->string('front_image')->nullable();                            // stored file path
            $table->string('back_image')->nullable();
            $table->string('selfie_image')->nullable();
            $table->string('status')->default('pending');                         // pending | approved | rejected
            $table->text('rejection_reason')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_verifications');
    }
};
