<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ExpireBoosts extends Command
{
    protected $signature   = 'boosts:expire';
    protected $description = 'Mark boosts whose expires_at has passed as expired.';

    public function handle(): int
    {
        $count = DB::table('boosts')
            ->where('status', 'active')
            ->where('expires_at', '<', now())
            ->update(['status' => 'expired']);

        $this->info("Expired {$count} boost(s).");
        return self::SUCCESS;
    }
}
