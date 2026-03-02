<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up()
    {
        if (DB::table('commission_rules')->count() === 0) {
            DB::table('commission_rules')->insert([
                'name' => 'Global default',
                'scope' => 'global',
                'scope_id' => null,
                'percentage' => 5.0,
                'fixed' => 0.00,
                'conditions' => null,
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down()
    {
        DB::table('commission_rules')->where('scope', 'global')->where('name', 'Global default')->delete();
    }
};
