<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'is_notifications_allowed')) {
                $table->boolean('is_notifications_allowed')->default(1)->after('about')->comment('1=enabled, 0=disabled');
            }
            if (!Schema::hasColumn('users', 'is_contact_info_visible')) {
                $table->boolean('is_contact_info_visible')->default(0)->after('is_notifications_allowed')->comment('1=visible, 0=hidden');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_notifications_allowed', 'is_contact_info_visible']);
        });
    }
};
