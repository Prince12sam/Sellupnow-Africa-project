<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DemoMembershipPlanSeeder extends Seeder
{
    public function run()
    {
        try {
            if (Schema::connection('listocean')->hasTable('membership_plans')) {
                // create a starter and gold plan if missing
                $exists = DB::connection('listocean')->table('membership_plans')->where('name', 'Starter')->exists();
                if (! $exists) {
                    DB::connection('listocean')->table('membership_plans')->insert([
                        [
                            'name' => 'Starter',
                            'description' => 'Basic free starter plan',
                            'duration_days' => 30,
                            'price' => 0.00,
                            'currency' => 'USD',
                            'is_active' => 1,
                            'features' => json_encode(['unlimited_images' => false, 'priority_listing' => false]),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ],
                        [
                            'name' => 'Gold',
                            'description' => 'Premium plan with extra features',
                            'duration_days' => 30,
                            'price' => 29.99,
                            'currency' => 'USD',
                            'is_active' => 1,
                            'features' => json_encode(['unlimited_images' => true, 'priority_listing' => true, 'featured_badge' => true, 'discount_commission' => true]),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ],
                    ]);
                }
            }
        } catch (\Throwable $e) {
            logger()->warning('DemoMembershipPlanSeeder skipped: '.$e->getMessage());
        }
    }
}
