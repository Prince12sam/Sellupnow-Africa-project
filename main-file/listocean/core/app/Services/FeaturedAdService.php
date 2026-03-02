<?php

namespace App\Services;

use App\Models\Frontend\FeaturedAdPackage;
use App\Models\Frontend\FeaturedAdPurchase;
use App\Models\Frontend\FeaturedAdActivation;
use App\Models\Backend\Listing;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class FeaturedAdService
{
    public function __construct(private WalletService $walletService) {}

    // ─── Package helpers ───────────────────────────────────────────────

    public function getActivePackages(): Collection
    {
        return FeaturedAdPackage::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('price')
            ->get();
    }

    public function findPackage(int $id): ?FeaturedAdPackage
    {
        return FeaturedAdPackage::where('id', $id)->where('is_active', true)->first();
    }

    // ─── User history ──────────────────────────────────────────────────

    public function getUserPurchases(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return FeaturedAdPurchase::with(['package', 'activeActivation'])
            ->where('user_id', $userId)
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    // ─── Check if a specific listing already has a live featured slot ──

    public function isListingFeatured(int $listingId): bool
    {
        return FeaturedAdActivation::where('listing_id', $listingId)
            ->where('is_active', true)
            ->where('ends_at', '>=', now())
            ->exists();
    }

    // ─── Check if user can afford a package ───────────────────────────

    public function canAfford(int $userId, FeaturedAdPackage $package): bool
    {
        if ($package->isFree()) {
            return true;
        }
        return $this->walletService->balance($userId) >= (float) $package->price;
    }

    // ─── Purchase flow ─────────────────────────────────────────────────

    /**
     * Debit wallet and create a FeaturedAdPurchase + FeaturedAdActivation.
     *
     * @throws \RuntimeException on insufficient funds or already-featured listing
     */
    public function purchase(int $userId, int $packageId, int $listingId): FeaturedAdPurchase
    {
        $package = $this->findPackage($packageId);

        if (!$package) {
            throw new \RuntimeException('Featured ad package not found or inactive.');
        }

        if ($this->isListingFeatured($listingId)) {
            throw new \RuntimeException('This listing already has an active featured slot.');
        }

        // ── Enforce advertisement_limit (global purchase cap for this package) ──
        if ((int) $package->advertisement_limit > 0) {
            $usedSlots = FeaturedAdPurchase::where('package_id', $packageId)->count();
            if ($usedSlots >= (int) $package->advertisement_limit) {
                throw new \RuntimeException('This featured package is sold out. Please choose another.');
            }
        }

        if (!$package->isFree()) {
            $balance = $this->walletService->balance($userId);
            if ($balance < (float) $package->price) {
                throw new \RuntimeException('Insufficient wallet balance. Please top up your wallet.');
            }

            $this->walletService->debit(
                userId: $userId,
                amount: (float) $package->price,
                note: "Featured Ad: {$package->name}",
                referenceType: 'featured_ad_package',
                referenceId: $packageId,
            );
        }

        $purchase = FeaturedAdPurchase::create([
            'user_id'                  => $userId,
            'package_id'               => $packageId,
            'listing_id'               => $listingId,
            'amount_paid'              => $package->price,
            'duration_days_at_purchase' => (int) $package->duration_days,
            'payment_method'           => 'wallet',
            'payment_reference'        => 'WALLET-' . strtoupper(uniqid()),
            'purchased_at'             => now(),
        ]);

        $startsAt = now();
        $endsAt   = $startsAt->copy()->addDays($package->duration_days);

        FeaturedAdActivation::create([
            'purchase_id' => $purchase->id,
            'listing_id'  => $listingId,
            'starts_at'   => $startsAt,
            'ends_at'     => $endsAt,
            'is_active'   => true,
        ]);

        // Sync listings.is_featured so existing page-builder widgets pick it up
        Listing::where('id', $listingId)->update(['is_featured' => 1]);

        return $purchase->load(['package', 'activations']);
    }

    // ─── Expire stale activations (call from scheduled command if desired) ──

    public function expireStaleActivations(): int
    {
        // Get the listing IDs whose activations are expiring
        $expiredListingIds = FeaturedAdActivation::where('is_active', true)
            ->where('ends_at', '<', now())
            ->pluck('listing_id')
            ->unique()
            ->values();

        $count = FeaturedAdActivation::where('is_active', true)
            ->where('ends_at', '<', now())
            ->update(['is_active' => false]);

        // For each affected listing, clear is_featured only if no other live activation remains
        foreach ($expiredListingIds as $listingId) {
            $hasLiveActivation = FeaturedAdActivation::where('listing_id', $listingId)
                ->where('is_active', true)
                ->where('ends_at', '>=', now())
                ->exists();

            if (!$hasLiveActivation) {
                DB::table('listings')->where('id', $listingId)->update(['is_featured' => 0]);
            }
        }

        return $count;
    }
}
