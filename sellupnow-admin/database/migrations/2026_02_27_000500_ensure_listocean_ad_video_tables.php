<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'listocean';

    public function up(): void
    {
        if (!Schema::connection($this->connection)->hasTable('ad_videos')) {
            Schema::connection($this->connection)->create('ad_videos', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('user_id')->nullable();
                $table->unsignedBigInteger('listing_id')->nullable();
                $table->text('video_url')->nullable();
                $table->text('thumbnail_url')->nullable();
                $table->string('caption', 300)->nullable();
                $table->string('cta_text', 60)->nullable();
                $table->string('cta_url', 2000)->nullable();
                $table->boolean('is_sponsored')->default(0);
                $table->boolean('is_approved')->default(0);
                $table->timestamp('approved_at')->nullable();
                $table->boolean('is_rejected')->default(0);
                $table->string('reject_reason', 300)->nullable();
                $table->timestamp('rejected_at')->nullable();
                $table->timestamp('start_at')->nullable();
                $table->timestamp('end_at')->nullable();
                $table->unsignedBigInteger('views')->default(0);
                $table->unsignedBigInteger('likes_count')->default(0);
                $table->timestamps();

                $table->index(['user_id']);
                $table->index(['listing_id']);
                $table->index(['is_approved']);
                $table->index(['is_rejected']);
                $table->index(['start_at', 'end_at']);
            });
        } else {
            Schema::connection($this->connection)->table('ad_videos', function (Blueprint $table) {
                $this->addColumnIfMissing('ad_videos', 'user_id', fn () => $table->unsignedBigInteger('user_id')->nullable());
                $this->addColumnIfMissing('ad_videos', 'listing_id', fn () => $table->unsignedBigInteger('listing_id')->nullable());
                $this->addColumnIfMissing('ad_videos', 'video_url', fn () => $table->text('video_url')->nullable());
                $this->addColumnIfMissing('ad_videos', 'thumbnail_url', fn () => $table->text('thumbnail_url')->nullable());
                $this->addColumnIfMissing('ad_videos', 'caption', fn () => $table->string('caption', 300)->nullable());
                $this->addColumnIfMissing('ad_videos', 'cta_text', fn () => $table->string('cta_text', 60)->nullable());
                $this->addColumnIfMissing('ad_videos', 'cta_url', fn () => $table->string('cta_url', 2000)->nullable());
                $this->addColumnIfMissing('ad_videos', 'is_sponsored', fn () => $table->boolean('is_sponsored')->default(0));
                $this->addColumnIfMissing('ad_videos', 'is_approved', fn () => $table->boolean('is_approved')->default(0));
                $this->addColumnIfMissing('ad_videos', 'approved_at', fn () => $table->timestamp('approved_at')->nullable());
                $this->addColumnIfMissing('ad_videos', 'is_rejected', fn () => $table->boolean('is_rejected')->default(0));
                $this->addColumnIfMissing('ad_videos', 'reject_reason', fn () => $table->string('reject_reason', 300)->nullable());
                $this->addColumnIfMissing('ad_videos', 'rejected_at', fn () => $table->timestamp('rejected_at')->nullable());
                $this->addColumnIfMissing('ad_videos', 'start_at', fn () => $table->timestamp('start_at')->nullable());
                $this->addColumnIfMissing('ad_videos', 'end_at', fn () => $table->timestamp('end_at')->nullable());
                $this->addColumnIfMissing('ad_videos', 'views', fn () => $table->unsignedBigInteger('views')->default(0));
                $this->addColumnIfMissing('ad_videos', 'likes_count', fn () => $table->unsignedBigInteger('likes_count')->default(0));
                if (!Schema::connection($this->connection)->hasColumn('ad_videos', 'created_at') || !Schema::connection($this->connection)->hasColumn('ad_videos', 'updated_at')) {
                    $table->timestamps();
                }
            });
        }

        if (!Schema::connection($this->connection)->hasTable('reel_ad_placements')) {
            Schema::connection($this->connection)->create('reel_ad_placements', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('reel_type', 20);
                $table->unsignedBigInteger('reel_id');
                $table->unsignedBigInteger('advertisement_id');
                $table->string('placement', 40);
                $table->boolean('status')->default(1);
                $table->timestamp('start_at')->nullable();
                $table->timestamp('end_at')->nullable();
                $table->timestamps();

                $table->unique(['reel_type', 'reel_id', 'placement'], 'reel_ad_placements_unique');
                $table->index(['advertisement_id']);
                $table->index(['status']);
            });
        }
    }

    private function addColumnIfMissing(string $table, string $column, \Closure $addColumn): void
    {
        if (!Schema::connection($this->connection)->hasColumn($table, $column)) {
            $addColumn();
        }
    }

    public function down(): void
    {
        // Intentionally non-destructive for hotfix compatibility migration.
    }
};
