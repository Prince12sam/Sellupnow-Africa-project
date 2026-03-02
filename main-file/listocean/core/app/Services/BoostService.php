<?php

namespace App\Services;

use App\Models\Frontend\Boost;
use App\Models\Frontend\UserMembership;

class BoostService
{
    // Boost durations in hours by membership tier
    private const BOOST_HOURS = 48;

    public function __construct(private WalletService $walletService) {}

    /**
     * Default boost price — falls back to static option, then ₵25.
     */
    public function boostPrice(): float
    {
        $price = (float) get_static_option('boost_price_per_listing', 25);
        return $price > 0 ? $price : 25.00;
    }

    /**
     * Check whether the user has free boost entitlements from their membership.
     * Entitlement limits (per calendar month):
     *   Starter: 0, Pro: 3, Business: 10
     */
    public function freeBoostsRemaining(int $userId): int
    {
        $membership = UserMembership::where('user_id', $userId)
            ->where('status', 1)
            ->where(function ($q) {
                $q->whereNull('expire_date')
                  ->orWhere('expire_date', '>=', now());
            })
            ->first();

        if (!$membership) {
            return 0;
        }

        // Entitlement by plan id — look up from membership_plans if desired.
        // Simple approach: store free_boost_limit in plan features JSON.
        $plan = $membership->plan ?? null;
        $limit = 0;
        if ($plan && is_array($plan->features) && isset($plan->features['free_boosts_per_month'])) {
            $limit = (int) $plan->features['free_boosts_per_month'];
        }

        if ($limit <= 0) {
            return 0;
        }

        // Count boosts this calendar month that were free
        $usedThisMonth = Boost::where('user_id', $userId)
            ->where('payment_method', 'membership_entitlement')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        return max(0, $limit - $usedThisMonth);
    }

    /**
     * Boost a listing. Uses free entitlement if available, otherwise debits wallet.
     *
     * @throws \RuntimeException
     */
    public function boost(int $userId, int $listingId): Boost
    {
        // Verify listing belongs to user
        $listing = \App\Models\Backend\Listing::where('id', $listingId)
            ->where('user_id', $userId)
            ->first();

        if (!$listing) {
            throw new \RuntimeException('Listing not found or does not belong to you.');
        }

        // Check if already boosted
        $existing = Boost::where('listing_id', $listingId)
            ->where('status', 'active')
            ->where('expires_at', '>=', now())
            ->first();

        if ($existing) {
            throw new \RuntimeException(
                'This listing is already boosted until ' . $existing->expires_at->format('d M Y H:i') . '.'
            );
        }

        $freeRemaining = $this->freeBoostsRemaining($userId);
        $paymentMethod = 'wallet';
        $amountPaid    = $this->boostPrice();

        if ($freeRemaining > 0) {
            $paymentMethod = 'membership_entitlement';
            $amountPaid    = 0;
        } else {
            // Debit wallet
            $this->walletService->debit(
                userId: $userId,
                amount: $amountPaid,
                note: "Boost listing #{$listingId}",
                referenceType: 'listing_boost',
                referenceId: $listingId,
            );
        }

        $now = now();
        return Boost::create([
            'listing_id'     => $listingId,
            'user_id'        => $userId,
            'amount_paid'    => $amountPaid,
            'payment_method' => $paymentMethod,
            'boosted_at'     => $now,
            'expires_at'     => $now->copy()->addHours(self::BOOST_HOURS),
            'status'         => 'active',
        ]);
    }
}
