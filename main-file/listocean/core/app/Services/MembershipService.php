<?php

namespace App\Services;

use App\Models\Frontend\MembershipPlan;
use App\Models\Frontend\UserMembership;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class MembershipService
{
    public function __construct(protected WalletService $walletService) {}

    /**
     * Get the active membership for a user (or null).
     */
    public function activeMembership(int $userId): ?UserMembership
    {
        return UserMembership::with('plan')
            ->where('user_id', $userId)
            ->where('status', 1)                       // tinyint: 1 = active
            ->where(function ($q) {
                $q->whereNull('expire_date')
                  ->orWhere('expire_date', '>', now());
            })
            ->latest()
            ->first();
    }

    /**
     * Get the active plan (or null if on free / no membership).
     */
    public function activePlan(int $userId): ?MembershipPlan
    {
        return optional($this->activeMembership($userId))->plan;
    }

    /**
     * Check whether the user can post a new listing.
     */
    public function canPostListing(int $userId): bool
    {
        $membership = $this->activeMembership($userId);

        if (! $membership) {
            // If no paid plan needed (system allows all users), return true
            return get_static_option('listing_requires_membership') != '1';
        }

        return $membership->canPostListing();
    }

    /**
     * Check if user's active plan has a specific feature.
     * $key examples: 'video_upload', 'reel_promotion', 'priority_support'
     */
    public function hasFeature(int $userId, string $key): bool
    {
        try {
            $plan = $this->activePlan($userId);
            return $plan ? $plan->hasFeature($key) : false;
        } catch (\Throwable $e) {
            report($e);
            return false;
        }
    }

    /**
     * Get the video quota for a user's active plan.
     * Returns: 0 = no access, -1 = unlimited, N > 0 = max N listings with video.
     */
    public function getVideoQuota(int $userId): int
    {
        try {
            $plan = $this->activePlan($userId);
            if (! $plan) {
                return 0;
            }
            // If the plan explicitly has a video_quota column, use it.
            $quota = (int) ($plan->video_quota ?? 0);
            // Backwards-compat: if video_reels feature flag is set but quota is 0, treat as -1 (unlimited).
            if ($quota === 0 && $plan->hasFeature('video_reels')) {
                return -1;
            }
            return $quota;
        } catch (\Throwable $e) {
            report($e);
            return 0;
        }
    }

    /**
     * Get the banner ad quota for a user's active plan.
     * Returns: 0 = no access, -1 = unlimited, N > 0 = max N banner ad requests.
     */
    public function getBannerAdQuota(int $userId): int
    {
        try {
            $plan = $this->activePlan($userId);
            if (! $plan) {
                return 0;
            }
            return (int) ($plan->banner_ad_quota ?? 0);
        } catch (\Throwable $e) {
            report($e);
            return 0;
        }
    }

    /**
     * Return the badge label for the user's current plan.
     */
    public function getBadge(int $userId): string
    {
        $plan = $this->activePlan($userId);

        if (! $plan) {
            return 'Free';
        }

        return match (strtolower($plan->name)) {
            'starter'  => 'Starter',
            'pro'      => 'Pro',
            'business' => 'Business',
            default    => $plan->name,
        };
    }

    /**
     * Subscribe a user to a plan using their wallet balance.
     *
     * @throws RuntimeException
     */
    public function subscribe(int $userId, int $planId): UserMembership
    {
        $plan = MembershipPlan::where('id', $planId)->where('is_active', true)->firstOrFail();

        return DB::transaction(function () use ($userId, $plan) {
            // Debit wallet (free plans skip this)
            if ((float) $plan->price > 0) {
                $this->walletService->debit(
                    $userId,
                    (float) $plan->price,
                    "Membership subscription: {$plan->name}",
                    'membership',
                    $plan->id
                );
            }

            // Expire any existing active membership
            UserMembership::where('user_id', $userId)
                ->where('status', 1)
                ->update(['status' => 0]);

            // Calculate expiry
            $expiresAt = $plan->duration_days > 0
                ? now()->addDays($plan->duration_days)
                : null; // null = never expires (lifetime / free)

            $featuredCount = (int) ($plan->auto_feature_count ?? 0);
            $galleryCount  = (int) ($plan->gallery_image_limit ?? 0);

            return UserMembership::create([
                'user_id'                    => $userId,
                'membership_id'              => $plan->id,
                'price'                      => $plan->price,
                'payment_gateway'            => 'wallet',
                'payment_status'             => 'completed',
                'expire_date'                => $expiresAt,
                'status'                     => 1,
                'listing_limit'              => $plan->listing_quota ?? 0,
                'gallery_images'             => $galleryCount,
                'initial_gallery_images'     => $galleryCount,
                'featured_listing'           => $featuredCount,
                'initial_featured_listing'   => $featuredCount,
                'enquiry_form'               => data_get($plan->features, 'enquiry_form', 0),
                'business_hour'              => data_get($plan->features, 'business_hour', 0),
                'membership_badge'           => data_get($plan->features, 'membership_badge', 0),
            ]);
        });
    }

    /**
     * Cancel a user's active membership.
     */
    public function cancel(int $userId): bool
    {
        return (bool) UserMembership::where('user_id', $userId)
            ->where('status', 1)
            ->update(['status' => 0]);
    }

    /**
     * Increment the listing usage counter when a user posts a listing.
     */
    public function incrementListingUsage(int $userId): void
    {
        // listing_limit tracks the remaining count; decrement it if > 0
        UserMembership::where('user_id', $userId)
            ->where('status', 1)
            ->where('listing_limit', '>', 0)
            ->decrement('listing_limit');
    }

    /**
     * Get all active (visible) plans ordered for display.
     */
    public function getPlans()
    {
        return MembershipPlan::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('price')
            ->get();
    }
}
