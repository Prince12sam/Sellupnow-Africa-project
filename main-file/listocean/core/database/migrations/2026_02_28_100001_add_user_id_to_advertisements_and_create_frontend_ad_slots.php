<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add user_id to existing advertisements table (for user-submitted banner ad requests)
        if (Schema::hasTable('advertisements') && !Schema::hasColumn('advertisements', 'user_id')) {
            Schema::table('advertisements', function (Blueprint $table) {
                $table->unsignedBigInteger('user_id')->nullable()->after('id');
                $table->string('description', 500)->nullable()->after('title');
                $table->string('requested_slot', 100)->nullable()->after('slot')
                    ->comment('Desired slot key submitted by the user');
                $table->index('user_id');
            });
        }

        // frontend_ad_slots: admin-managed mapping of slot_key → advertisement
        if (!Schema::hasTable('frontend_ad_slots')) {
            Schema::create('frontend_ad_slots', function (Blueprint $table) {
                $table->id();
                $table->string('slot_key', 100)->unique();
                $table->unsignedBigInteger('advertisement_id');
                $table->tinyInteger('status')->default(1);
                $table->timestamp('start_at')->nullable();
                $table->timestamp('end_at')->nullable();
                $table->timestamps();

                $table->index('advertisement_id');
                $table->index(['slot_key', 'status']);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('frontend_ad_slots')) {
            Schema::dropIfExists('frontend_ad_slots');
        }
        if (Schema::hasTable('advertisements')) {
            Schema::table('advertisements', function (Blueprint $table) {
                $table->dropColumn(['user_id', 'description', 'requested_slot']);
            });
        }
    }
};
