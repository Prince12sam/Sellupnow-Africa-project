<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CommissionRulesSeeder extends Seeder
{
    public function run()
    {
        if (DB::table('commission_rules')->count() === 0) {
            DB::table('commission_rules')->insert([
                'name' => 'Global default',
                'scope' => 'global',
                'scope_id' => null,
                'percentage' => 5.0,
                'fixed' => 0.00,
                'conditions' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
