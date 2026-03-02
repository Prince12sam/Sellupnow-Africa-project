<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ExpireMemberships extends Command
{
    protected $signature   = 'memberships:expire';
    protected $description = 'Set status = 0 on user_memberships whose expire_date has passed.';

    public function handle(): int
    {
        $expired = DB::table('user_memberships')
            ->where('status', 1)
            ->whereNotNull('expire_date')
            ->where('expire_date', '<', now())
            ->get(['id', 'user_id', 'expire_date']);

        if ($expired->isEmpty()) {
            $this->info('No expired memberships found.');
            return self::SUCCESS;
        }

        $ids = $expired->pluck('id')->all();

        DB::table('user_memberships')
            ->whereIn('id', $ids)
            ->update([
                'status'     => 0,
                'updated_at' => now(),
            ]);

        $this->info("Expired {$expired->count()} membership(s).");

        return self::SUCCESS;
    }
}
