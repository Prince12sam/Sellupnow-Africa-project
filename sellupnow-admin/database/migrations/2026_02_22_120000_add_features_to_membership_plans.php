<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFeaturesToMembershipPlans extends Migration
{
    /**
     * Run the migrations.
     * This migration targets the `listocean` database connection explicitly.
     * It will add a nullable JSON `features` column to `membership_plans` if missing.
     *
     * @return void
     */
    public function up()
    {
        try {
            if (Schema::connection('listocean')->hasTable('membership_plans')) {
                if (! Schema::connection('listocean')->hasColumn('membership_plans', 'features')) {
                    Schema::connection('listocean')->table('membership_plans', function (Blueprint $table) {
                        $table->json('features')->nullable()->after('is_active');
                    });
                }
            }
        } catch (\Throwable $e) {
            // Defensive: if the connection/table is not available in this environment,
            // don't crash the migration runner; surface reason in the logs if needed.
            logger()->warning('AddFeaturesToMembershipPlans migration skipped: '.$e->getMessage());
        }
    }

    /**
     * Reverse the migrations.
     * Drops the `features` column if it exists.
     *
     * @return void
     */
    public function down()
    {
        try {
            if (Schema::connection('listocean')->hasTable('membership_plans') && Schema::connection('listocean')->hasColumn('membership_plans', 'features')) {
                Schema::connection('listocean')->table('membership_plans', function (Blueprint $table) {
                    $table->dropColumn('features');
                });
            }
        } catch (\Throwable $e) {
            logger()->warning('Rollback AddFeaturesToMembershipPlans skipped: '.$e->getMessage());
        }
    }
}
