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
        Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->default(0);
            $table->unsignedBigInteger('admin_id');
            $table->unsignedBigInteger('category_id')->nullable();
            $table->string('title');
            $table->text('slug')->nullable();
            $table->longText('blog_content')->nullable();
            $table->string('image')->nullable();
            $table->string('author')->nullable();
            $table->longText('excerpt')->nullable();
            $table->string('views')->nullable();
            $table->string('visibility')->nullable();
            $table->string('featured')->nullable();
            $table->string('schedule_date')->nullable();
            $table->string('tag_name')->nullable();
            $table->string('created_by')->nullable();
            $table->enum('status', ['publish', 'draft','archive','schedule']);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blogs');
    }
};
