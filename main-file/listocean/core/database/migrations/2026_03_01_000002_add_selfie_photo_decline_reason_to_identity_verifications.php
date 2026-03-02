<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('identity_verifications', function (Blueprint $table) {
            if (!Schema::hasColumn('identity_verifications', 'selfie_photo')) {
                $table->string('selfie_photo')->nullable()->after('back_document');
            }
            if (!Schema::hasColumn('identity_verifications', 'decline_reason')) {
                $table->text('decline_reason')->nullable()->after('verify_by');
            }
        });
    }

    public function down(): void
    {
        Schema::table('identity_verifications', function (Blueprint $table) {
            $table->dropColumn(array_filter([
                Schema::hasColumn('identity_verifications', 'selfie_photo') ? 'selfie_photo' : null,
                Schema::hasColumn('identity_verifications', 'decline_reason') ? 'decline_reason' : null,
            ]));
        });
    }
};
