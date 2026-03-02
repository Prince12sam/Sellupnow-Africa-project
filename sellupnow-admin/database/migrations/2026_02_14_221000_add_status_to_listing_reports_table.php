<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('listing_reports', function (Blueprint $table) {
            $table->string('status', 20)->default('pending')->after('description');
            $table->timestamp('resolved_at')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('listing_reports', function (Blueprint $table) {
            $table->dropColumn(['status', 'resolved_at']);
        });
    }
};
