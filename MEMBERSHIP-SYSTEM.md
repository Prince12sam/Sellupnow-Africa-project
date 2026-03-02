# SellUpNow Membership System — Product Design

> **Authored by:** Product Engineering  
> **Date:** 2026-02-28  
> **Status:** Ready to implement — admin panel fully supports this schema  
> **Currency:** GHS (₵) — Ghana Cedis (admin panel supports any currency per plan)

---

## Table of Contents

1. [Philosophy & Goals](#1-philosophy--goals)
2. [The Four Tiers](#2-the-four-tiers)
3. [Feature Matrix](#3-feature-matrix)
4. [Pricing Strategy](#4-pricing-strategy)
5. [Business Rules](#5-business-rules)
6. [Admin Panel Setup — Step by Step](#6-admin-panel-setup--step-by-step)
7. [Frontend Implementation](#7-frontend-implementation)
8. [DB Schema Reference](#8-db-schema-reference)
9. [Quota Enforcement Logic](#9-quota-enforcement-logic)
10. [Upgrade / Downgrade / Expiry Rules](#10-upgrade--downgrade--expiry-rules)
11. [Scheduled Jobs Required](#11-scheduled-jobs-required)

---

## 1. Philosophy & Goals

The membership system transforms SellUpNow from a free listing board into a **tiered seller ecosystem**. The design principles are:

- **Free tier stays generous** — never punish casual sellers. The platform grows when everyone can list.
- **Paid tiers unlock visibility, not access** — any seller can list; paid sellers get seen more.
- **Each tier tells a story** — name, badge, and description should convey aspiration, not just features.
- **Annual discount drives commitment** — 15–17% off annual plans reduces churn.
- **Non-destructive downgrade** — when a plan expires, listings stay live, they just lose boost priority and badge. Sellers can always re-subscribe to restore full power.

---

## 2. The Four Tiers

### Tier 0 — Free (Seeker)

> *"Start selling today. No credit card needed."*

The default tier for all registered users. Generous enough to be genuinely useful but limited enough to make the paid upgrade compelling.

- No payment required
- Suitable for: occasional sellers, private individuals, first-time listers
- Badge: none

---

### Tier 1 — Starter (₵49/month · ₵499/year)

> *"Your first step to a serious seller profile."*

For sellers who are consistent but don't yet need full business tools. The goal of this tier is reducing friction between "casual seller" and "regular seller."

- Small monthly cost — psychologically approachable
- 2 free boosts per month (₵25 value if bought individually)
- Starter badge adds social proof to profile and listings
- Suitable for: students, side-hustlers, part-time traders

---

### Tier 2 — Pro (₵149/month · ₵1,499/year)

> *"Stand out. Sell faster. Build your brand."*

The primary revenue-driving tier. Most serious individual sellers and small businesses land here. The jump from Starter to Pro is about **visibility at scale** — more listings featured, reels, and analytics.

- 1 auto-featured listing per month included (₵80 value)
- 5 boosts per month (₵125 value)
- Video reel uploads — visual storytelling for listings
- Verified Pro badge — builds buyer trust
- Listing analytics — see which listings get the most attention
- Suitable for: active traders, small shop owners, freelancers

---

### Tier 3 — Business (₵399/month · ₵3,999/year)

> *"Your complete business presence on SellUpNow."*

For businesses, agencies, and high-volume sellers who need everything. The Business tier is positioned as a **complete marketing platform**, not just a listing quota upgrade.

- 5 auto-featured listings per month (₵400 value)
- 15 boosts per month (₵375 value) 
- Unlimited video reel uploads
- 1 banner ad request per quarter included (₵500+ value)
- Business Verified gold badge — highest trust signal
- Full analytics dashboard with conversion tracking
- Priority in search results (above Starter and Free)
- Priority support — all tickets answered within 4 hours
- Suitable for: car dealerships, real estate agencies, electronics stores, supermarkets

---

## 3. Feature Matrix

| Feature | Free | Starter | Pro | Business |
|---------|------|---------|-----|----------|
| **Active listing limit** | 5 | 20 | Unlimited | Unlimited |
| **Price** | ₵0 | ₵49/mo | ₵149/mo | ₵399/mo |
| **Annual price** | ₵0 | ₵499/yr | ₵1,499/yr | ₵3,999/yr |
| **Boosts per month** | 0 | 2 | 5 | 15 |
| **Auto-featured listings/month** | 0 | 0 | 1 | 5 |
| **Video reel uploads** | ❌ | ❌ | ✅ (5 active) | ✅ Unlimited |
| **Seller badge** | None | Starter | Pro Verified | Business Gold |
| **Banner ad request** | ❌ | ❌ | ❌ | 1/quarter included |
| **Listing analytics** | ❌ | View count only | Full analytics | Full + conversion |
| **Search priority** | Standard | Standard | Elevated | Top |
| **Support** | Community | Email (48h) | Email (24h) | Priority (4h) |
| **Profile highlight** | Standard | Highlighted | Featured profile | Premium profile |
| **Promotional reel eligibility** | ❌ | ❌ | ✅ | ✅ |

---

## 4. Pricing Strategy

### Monthly vs Annual

| Plan | Monthly | Annual | Monthly equiv. | Saving |
|------|---------|--------|---------------|--------|
| Starter | ₵49 | ₵499 | ₵41.58 | ₵89 (15%) |
| Pro | ₵149 | ₵1,499 | ₵124.92 | ₵289 (16%) |
| Business | ₵399 | ₵3,999 | ₵333.25 | ₵789 (17%) |

Annual discount increases slightly per tier — rewarding the highest-spend commitment most.

### Value anchoring

Always show the "equivalent value" of boosts + featured ads included in each plan on the pricing page. For example:

> **Pro at ₵149/month includes:** 5 boosts (₵125 value) + 1 auto-featured listing (₵80 value) = **₵205 in standalone purchases for ₵149**

This makes the plan feel like a net positive before a seller even lists.

### Future: Add-on marketplace

Once membership is live, sell standalone add-ons:
- Extra boosts: ₵25 each
- Extra featured listing slots: ₵80 for 7 days
- Banner ad request (non-Business): ₵300 per slot per week

These are not in scope for the initial implementation but the DB schema supports them through `wallet_histories.reference_type`.

---

## 5. Business Rules

### What membership controls

| Rule | Detail |
|------|--------|
| Listing count cap | On listing submit, check `user_memberships` active plan's quota vs listings currently active. Reject if over quota. Free = 5 cap. |
| Boost entitlement | Deduct from `boosts_remaining` (tracked in `user_memberships`). Resets on billing anniversary. |
| Auto-featured | On membership activation, create `featured_ad_activations` records equal to `auto_feature_count`. Seller assigns which listings to feature. |
| Video reel access | `membership_features` record `feature_key = video_reels` must exist for the user's active plan. Check before allowing upload. |
| Badge display | Read `membership_plans.badge_label` and `badge_color` from the user's active `user_memberships.plan_id`. Display on profile and listing cards. |
| Banner ad request | `membership_features` record `feature_key = banner_ad_request` with `value = quarterly`. Check on submission form. |

### Plan stacking

A user can only have **one active membership plan** at a time. The most recently activated paid plan wins. You cannot stack multiple plans.

### Free users who exceed the legacy limit

If an existing seller has more than 5 listings and downgrades/expires to Free, their **existing listings are preserved but set to inactive** until they either delete listings to stay under quota or re-subscribe. They are notified before this happens (7 days notice before expiry + on expiry day).

---

## 6. Admin Panel Setup — Step by Step

The admin panel at `http://127.0.0.1:8091/admin/membership-plans` is fully ready. Follow this setup sequence exactly:

### Step 1 — Create the four plans

Navigate to **Admin → Membership Plans → Create** and create four plans in this order (so IDs are predictable: 1=Free, 2=Starter, 3=Pro, 4=Business):

#### Plan 1 — Free
| Field | Value |
|-------|-------|
| Name | Free |
| Description | Perfect for occasional sellers. List up to 5 items, connect with buyers, no credit card needed. |
| Duration Days | 36500 (100 years = forever) |
| Price | 0 |
| Currency | GHS |
| Active | ✅ Yes |
| Features (JSON tags) | `browse_listings`, `contact_sellers`, `basic_listing` |

#### Plan 2 — Starter
| Field | Value |
|-------|-------|
| Name | Starter |
| Description | Your first step to a serious seller profile. Get 2 free boosts per month and your Starter badge. |
| Duration Days | 30 |
| Price | 49.00 |
| Currency | GHS |
| Active | ✅ Yes |
| Features | `2_boosts_monthly`, `starter_badge`, `email_support_48h`, `view_count_analytics` |

#### Plan 3 — Pro
| Field | Value |
|-------|-------|
| Name | Pro |
| Description | Stand out and sell faster. 5 monthly boosts, 1 auto-featured listing, video reels, and your Pro Verified badge. |
| Duration Days | 30 |
| Price | 149.00 |
| Currency | GHS |
| Active | ✅ Yes |
| Features | `5_boosts_monthly`, `1_auto_featured_monthly`, `video_reels`, `pro_badge`, `listing_analytics`, `email_support_24h`, `elevated_search` |

#### Plan 4 — Business
| Field | Value |
|-------|-------|
| Name | Business |
| Description | Your complete business presence on SellUpNow. Unlimited listings, 15 boosts, 5 auto-features, banner ad, Business Gold badge, and priority support. |
| Duration Days | 30 |
| Price | 399.00 |
| Currency | GHS |
| Active | ✅ Yes |
| Features | `15_boosts_monthly`, `5_auto_featured_monthly`, `video_reels_unlimited`, `banner_ad_quarterly`, `business_gold_badge`, `full_analytics`, `priority_support_4h`, `top_search`, `premium_profile` |

> **Tip:** After creating all four plans, copy their IDs. Use them in the frontend constants file so you don't rely on name matching.

### Step 2 — Create membership features per plan (optional but recommended)

Navigate to **Admin → Membership Features** and create explicit feature records for the most important features. These supplement the JSON features field and allow per-feature UI rendering:

For Plan 3 (Pro) create these feature rows:
| Plan ID | Feature Key | Feature Label | Value |
|---------|------------|--------------|-------|
| 3 | listing_quota | Active listings | 50 |
| 3 | boosts_per_month | Monthly boosts | 5 |
| 3 | auto_featured_per_month | Auto-featured listings | 1 |
| 3 | video_reels | Video reel uploads | true |
| 3 | badge_label | Seller badge | Pro Verified |
| 3 | badge_color | Badge colour | #2563EB |

For Plan 4 (Business), mirror the above with Business values and add:
| Plan ID | Feature Key | Feature Label | Value |
|---------|------------|--------------|-------|
| 4 | banner_ad_request | Banner ad request | quarterly |

---

## 7. Frontend Implementation

### Files to create in `main-file/listocean/core/`

```
app/
  Services/
    MembershipService.php          ← all membership business logic
  Http/
    Controllers/
      Frontend/
        MembershipController.php   ← subscribe, cancel, my-membership page
resources/
  views/
    frontend/
      membership/
        plans.blade.php            ← pricing/comparison page
        checkout.blade.php         ← select billing period, confirm wallet balance
        my-membership.blade.php    ← current plan, usage stats, upgrade/cancel
routes/
  web.php                          ← add membership route group
```

### Key routes to add

```php
Route::middleware('auth')->prefix('user/membership')->name('membership.')->group(function () {
    Route::get('/',          [MembershipController::class, 'plans'])->name('plans');
    Route::get('/current',   [MembershipController::class, 'current'])->name('current');
    Route::post('/subscribe/{planId}', [MembershipController::class, 'subscribe'])->name('subscribe');
    Route::post('/cancel',   [MembershipController::class, 'cancel'])->name('cancel');
});
```

### MembershipService — core methods

```php
class MembershipService
{
    public function activePlan(int $userId): ?object
    {
        return DB::table('user_memberships')
            ->where('user_id', $userId)
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->orderByDesc('id')
            ->first();
    }

    public function subscribe(int $userId, int $planId, string $period = 'monthly'): void
    {
        // 1. Fetch plan + calculate price
        $plan = DB::table('membership_plans')->where('id', $planId)->firstOrFail();
        $price = $period === 'yearly'
            ? $this->annualPrice($plan->price)
            : $plan->price;

        // 2. Deduct from wallet (throws InsufficientBalanceException if short)
        WalletService::debit($userId, $price, 'membership_subscription', $planId);

        // 3. Cancel any existing active plan
        DB::table('user_memberships')
            ->where('user_id', $userId)->where('status', 'active')
            ->update(['status' => 'cancelled', 'updated_at' => now()]);

        // 4. Create new membership record
        $durationDays = $period === 'yearly' ? $plan->duration_days * 12 : $plan->duration_days;
        DB::table('user_memberships')->insert([
            'user_id'          => $userId,
            'plan_id'          => $planId,
            'amount_paid'      => $price,
            'payment_method'   => 'wallet',
            'started_at'       => now(),
            'expires_at'       => now()->addDays($durationDays),
            'status'           => 'active',
            'listings_used'    => 0,
            'auto_features_used' => 0,
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);
    }

    public function canPostListing(int $userId): bool
    {
        $plan = $this->activePlan($userId);
        $quota = $plan ? DB::table('membership_plans')->where('id', $plan->plan_id)->value('listing_quota') : 5;
        if ($quota === 0) return true; // 0 = unlimited

        $active = DB::table('listings')
            ->where('user_id', $userId)->where('status', 1)->count();
        return $active < $quota;
    }

    public function hasFeature(int $userId, string $featureKey): bool
    {
        $plan = $this->activePlan($userId);
        if (!$plan) return false;

        // Check JSON features column on the plan
        $features = json_decode(
            DB::table('membership_plans')->where('id', $plan->plan_id)->value('features') ?? '[]', true
        );
        return in_array($featureKey, $features ?? []);
    }
}
```

### plans.blade.php — Pricing Page Layout

Structure the page as a **horizontal 4-column card grid** on desktop, vertical stack on mobile:

```
┌─────────────────────────────────────────────────────────────────┐
│  Choose Your Plan — Sell More. Earn More.                        │
│  [Monthly] [Annual — Save up to 17%]  ← toggle switch          │
├──────────┬──────────┬──────────┬──────────────────────────────┤
│  FREE    │  STARTER │   PRO    │  BUSINESS                    │
│  ₵0      │  ₵49/mo  │  ₵149/mo │  ₵399/mo                    │
│          │          │          │                              │
│ 5 listings│20 list. │ Unlimited│ Unlimited                    │
│ No badge │Starter ✓│Pro Badge ✓│Business Gold ✓               │
│          │2 boosts  │5 boosts  │15 boosts                     │
│          │          │1 featured│5 featured                    │
│          │          │Video ✓   │Video ✓ + Banner ✓            │
│          │          │          │                              │
│[Current] │[Upgrade] │[Upgrade] │[Upgrade]                    │
└──────────┴──────────┴──────────┴──────────────────────────────┘
```

On each card, show the "value" calculation for paid plans:
> *"Includes ₵205 in features for ₵149/month"*

---

## 8. DB Schema Reference

The schema was created by the Feb 28 migrations and the align-columns migration. Actual columns currently in each table:

### `membership_plans`
```
id, name, description, price, billing_period, listing_quota,
auto_feature_count, badge_label, badge_color, is_active, sort_order,
duration_days, currency, features (JSON), created_at, updated_at
```

> **Admin uses:** `name`, `description`, `duration_days`, `price`, `currency`, `is_active`, `features`  
> **Frontend additionally reads:** `badge_label`, `badge_color`, `listing_quota`, `auto_feature_count`

### `user_memberships`
```
id, user_id, plan_id, amount_paid, payment_method, payment_reference,
started_at, expires_at, status (active|cancelled|expired),
listings_used, auto_features_used, created_at, updated_at
```

### `membership_features` (optional, supplementary)
```
id, plan_id, feature_key, feature_label, value, created_at, updated_at
```

---

## 9. Quota Enforcement Logic

Add this check in the listing creation controller, before saving:

```php
// In ListingController@store — before DB insert
$membershipService = new MembershipService();
if (! $membershipService->canPostListing(auth()->id())) {
    return back()->withErrors([
        'quota' => 'You have reached your plan\'s listing limit. 
                    Upgrade to Pro for unlimited listings.',
    ]);
}
// Increment listings_used on successful save
DB::table('user_memberships')
    ->where('user_id', auth()->id())
    ->where('status', 'active')
    ->increment('listings_used');
```

---

## 10. Upgrade / Downgrade / Expiry Rules

| Scenario | What happens |
|----------|-------------|
| User upgrades (Free → Pro) | Old plan cancelled, new plan starts immediately. Remaining days on old plan are forfeited (no proration in v1). |
| User upgrades (Starter → Business) | Same — cancel old, start new immediately. |
| Annual plan subscriber upgrades mid-year | Cancel old, start new annual from today. No refund in v1. |
| Plan expires naturally | `expires_at` passes. Scheduled job sets `status = expired`. User reverts to Free rules. Listings STAY active until admin cleanup job (7 days grace). |
| User has 25 listings, plan expires (Free = 5 limit) | All listings stay active for 7-day grace period. On day 7, excess listings (oldest first) are set to `status = 0` (inactive). User notified D-7 and D-0. |
| User cancels active plan | `status = cancelled`. Features deactivated immediately. Wallet is NOT refunded in v1 (state this clearly on cancel confirmation screen). |
| User re-subscribes after cancellation | New fresh subscription starts. `listings_used` and `auto_features_used` reset to 0. |

---

## 11. Scheduled Jobs Required

Add to `app/Console/Commands/` — register in `Console/Kernel.php`:

| Job | Schedule | What it does |
|-----|----------|-------------|
| `ExpireMemberships` | Daily at 00:05 | Set `user_memberships.status = expired` where `expires_at < NOW()` and `status = active` |
| `DowngradeExpiredUsers` | Daily at 00:10 | For expired users over Free quota, set oldest excess listings to `status = 0`. Send notification email. |
| `GrantMonthlyBoosts` | 1st of month at 00:01 | Reset `boosts_remaining` counter for active subscribers (not in DB yet — track in `user_memberships` or a separate `membership_entitlements` table) |
| `ExpireBoosts` | Hourly | Set `boosts.status = expired` where `expires_at < NOW()` |
| `ExpireFeaturedAds` | Hourly | Set `featured_ad_activations.is_active = 0` where `ends_at < NOW()`. Set `listings.is_featured = 0`. |
