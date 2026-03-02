<?php

namespace App\Console\Commands;

use App\Services\EscrowService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EscrowAutoRelease extends Command
{
    protected $signature   = 'escrow:auto-release';
    protected $description = 'Auto-release escrow orders where buyer confirmation deadline has passed';

    public function handle(): void
    {
        $escrow = new EscrowService();

        // Find seller_delivered transactions past the buyer confirmation deadline
        $overdue = DB::table('escrow_transactions')
            ->where('status', 'seller_delivered')
            ->where('buyer_confirm_deadline_at', '<', now())
            ->get();

        $released = 0;
        $errors   = 0;

        foreach ($overdue as $tx) {
            try {
                $escrow->release($tx, 'auto_released', 0); // actor 0 = system
                $released++;
            } catch (\Throwable $e) {
                $errors++;
                Log::warning("EscrowAutoRelease: failed to release tx #{$tx->id}", [
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Also auto-cancel funded orders where seller acceptance deadline has passed
        $expired = DB::table('escrow_transactions')
            ->where('status', 'funded')
            ->where('seller_accept_deadline_at', '<', now())
            ->get();

        $refunded = 0;

        foreach ($expired as $tx) {
            try {
                // Refund total_amount to buyer
                $escrow->refundTobuyer($tx, 'seller_no_response');
                $refunded++;
            } catch (\Throwable $e) {
                $errors++;
                Log::warning("EscrowAutoRelease: failed to refund tx #{$tx->id}", [
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->info("EscrowAutoRelease: released={$released}, refunded={$refunded}, errors={$errors}");
    }
}
