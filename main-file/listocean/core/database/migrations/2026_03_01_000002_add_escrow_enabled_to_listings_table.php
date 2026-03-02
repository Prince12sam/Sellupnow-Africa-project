<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('listings') && !Schema::hasColumn('listings', 'escrow_enabled')) {
            Schema::table('listings', function (Blueprint $table) {
                $table->tinyInteger('escrow_enabled')->default(0)->after('is_featured')
                    ->comment('1 = seller has opted this listing into escrow');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('listings') && Schema::hasColumn('listings', 'escrow_enabled')) {
            Schema::table('listings', function (Blueprint $table) {
                $table->dropColumn('escrow_enabled');
            });
        }
    }
};
