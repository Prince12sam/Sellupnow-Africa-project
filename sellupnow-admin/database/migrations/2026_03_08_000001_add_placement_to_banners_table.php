<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('banners', 'placement')) {
            Schema::table('banners', function (Blueprint $table): void {
                $table->string('placement', 64)
                    ->default('homepage')
                    ->after('banner')
                    ->index();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('banners', 'placement')) {
            Schema::table('banners', function (Blueprint $table): void {
                $table->dropColumn('placement');
            });
        }
    }
};
