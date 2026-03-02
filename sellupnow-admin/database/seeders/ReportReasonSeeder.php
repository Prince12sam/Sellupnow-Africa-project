<?php

namespace Database\Seeders;

use App\Models\ReportReason;
use Illuminate\Database\Seeder;

class ReportReasonSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Spam', 'Duplicate', 'Fraud', 'Incorrect Information'] as $name) {
            ReportReason::query()->firstOrCreate(
                ['name' => $name],
                ['is_active' => true]
            );
        }
    }
}
