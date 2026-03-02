<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ticket_issue_types', function (Blueprint $table) {
            if (!Schema::hasColumn('ticket_issue_types', 'listocean_department_id')) {
                $table->unsignedBigInteger('listocean_department_id')->nullable()->after('is_active');
                $table->index('listocean_department_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('ticket_issue_types', function (Blueprint $table) {
            if (Schema::hasColumn('ticket_issue_types', 'listocean_department_id')) {
                $table->dropIndex(['listocean_department_id']);
                $table->dropColumn('listocean_department_id');
            }
        });
    }
};
