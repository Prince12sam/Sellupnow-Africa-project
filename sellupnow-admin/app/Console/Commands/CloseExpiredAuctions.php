<?php

namespace App\Console\Commands;

use App\Models\AuctionBid;
use App\Models\Listing;
use App\Services\PushNotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CloseExpiredAuctions extends Command
{
    protected $signature = 'auctions:close-expired';
    protected $description = 'Close expired auctions, pick winners, and send notifications';

    public function handle(): int
    {
        $expiredAuctions = Listing::query()
            ->where('sale_type', 2)
            ->where('auction_end_date', '<=', now())
            ->whereDoesntHave('auctionBids', fn ($q) => $q->where('status', 'won'))
            ->isActive()
            ->get();

        $this->info("Found {$expiredAuctions->count()} expired auction(s) to close.");

        foreach ($expiredAuctions as $listing) {
            $winningBid = AuctionBid::where('listing_id', $listing->id)
                ->where('status', 'active')
                ->orderByDesc('amount')
                ->first();

            if ($winningBid) {
                // Check reserve price
                $meetsReserve = ! $listing->is_reserve_price_enabled
                    || $winningBid->amount >= ($listing->reserve_price_amount ?? 0);

                if ($meetsReserve) {
                    // Mark as winner
                    $winningBid->update(['status' => 'won']);

                    // Mark remaining active bids as outbid
                    AuctionBid::where('listing_id', $listing->id)
                        ->where('status', 'active')
                        ->where('id', '!=', $winningBid->id)
                        ->update(['status' => 'outbid']);

                    // Notify winner
                    try {
                        PushNotificationService::sendToUsers(
                            $winningBid->user_id,
                            'You won the auction!',
                            "Congratulations! You won \"{$listing->title}\" with a bid of " . number_format($winningBid->amount, 2),
                            ['type' => 'auction_won', 'listing_id' => (string) $listing->id]
                        );
                    } catch (\Throwable $e) {
                        report($e);
                    }

                    // Notify seller
                    try {
                        PushNotificationService::sendToUsers(
                            $listing->user_id,
                            'Auction ended — Item sold!',
                            "\"{$listing->title}\" sold for " . number_format($winningBid->amount, 2),
                            ['type' => 'auction_sold', 'listing_id' => (string) $listing->id]
                        );
                    } catch (\Throwable $e) {
                        report($e);
                    }

                    $this->info("Listing #{$listing->id}: Winner bid #{$winningBid->id} ({$winningBid->amount})");
                } else {
                    // Reserve not met — cancel all bids
                    AuctionBid::where('listing_id', $listing->id)
                        ->where('status', 'active')
                        ->update(['status' => 'cancelled']);

                    // Notify seller
                    try {
                        PushNotificationService::sendToUsers(
                            $listing->user_id,
                            'Auction ended — Reserve not met',
                            "The auction for \"{$listing->title}\" ended but the reserve price was not met.",
                            ['type' => 'auction_reserve_not_met', 'listing_id' => (string) $listing->id]
                        );
                    } catch (\Throwable $e) {
                        report($e);
                    }

                    $this->info("Listing #{$listing->id}: Reserve not met. Bids cancelled.");
                }
            } else {
                // No bids — notify seller
                try {
                    PushNotificationService::sendToUsers(
                        $listing->user_id,
                        'Auction ended — No bids',
                        "The auction for \"{$listing->title}\" ended with no bids.",
                        ['type' => 'auction_no_bids', 'listing_id' => (string) $listing->id]
                    );
                } catch (\Throwable $e) {
                    report($e);
                }

                $this->info("Listing #{$listing->id}: No bids.");
            }
        }

        $this->info('Done.');
        return self::SUCCESS;
    }
}
