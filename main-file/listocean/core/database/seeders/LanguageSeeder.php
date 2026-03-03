<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LanguageSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('languages')->insertOrIgnore([
            [
                'id'        => 1,
                'name'      => 'English (UK)',
                'slug'      => 'en_GB',
                'direction' => 'ltr',
                'status'    => 'publish',
                'default'   => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
