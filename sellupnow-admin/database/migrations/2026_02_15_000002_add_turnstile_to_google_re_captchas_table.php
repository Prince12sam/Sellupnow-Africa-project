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
        Schema::table('google_re_captchas', function (Blueprint $table) {
            if (! Schema::hasColumn('google_re_captchas', 'provider')) {
                $table->string('provider')->default('google')->after('secret_key');
            }
            if (! Schema::hasColumn('google_re_captchas', 'turnstile_site_key')) {
                $table->text('turnstile_site_key')->nullable()->after('provider');
            }
            if (! Schema::hasColumn('google_re_captchas', 'turnstile_secret_key')) {
                $table->text('turnstile_secret_key')->nullable()->after('turnstile_site_key');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('google_re_captchas', function (Blueprint $table) {
            if (Schema::hasColumn('google_re_captchas', 'turnstile_secret_key')) {
                $table->dropColumn('turnstile_secret_key');
            }
            if (Schema::hasColumn('google_re_captchas', 'turnstile_site_key')) {
                $table->dropColumn('turnstile_site_key');
            }
            if (Schema::hasColumn('google_re_captchas', 'provider')) {
                $table->dropColumn('provider');
            }
        });
    }
};
