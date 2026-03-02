<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ExpireFeaturedAds extends Command
{
    protected $signature   = 'featuredads:expire';
    protected $description = 'Deactivate featured_ad_activations whose ends_at has passed, and clear listings.is_featured.';

    public function handle(): int
    {
        // 1. Collect listing IDs that are about to lose their last active activation
        $expiredListingIds = DB::table('featured_ad_activations')
            ->where('is_active', 1)
            ->where('ends_at', '<', now())
            ->pluck('listing_id')
            ->unique()
            ->values();

        // 2. Deactivate expired activations
        $count = DB::table('featured_ad_activations')
            ->where('is_active', 1)
            ->where('ends_at', '<', now())
            ->update(['is_active' => 0]);

        // 3. For listings that had expired activations, check if any OTHER active activation remains.
        //    Only those with NO remaining active activation should have is_featured cleared.
        foreach ($expiredListingIds as $listingId) {
            $stillActive = DB::table('featured_ad_activations')
                ->where('listing_id', $listingId)
                ->where('is_active', 1)
                ->where('ends_at', '>=', now())
                ->exists();

            if (!$stillActive) {
                DB::table('listings')->where('id', $listingId)->update(['is_featured' => 0]);
            }
        }

        $this->info("Expired {$count} featured ad activation(s).");
        return self::SUCCESS;
    }
}
