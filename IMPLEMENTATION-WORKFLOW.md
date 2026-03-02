# SellUpNow — Platform Implementation Workflow

> **Authored by:** Product Engineering  
> **Date:** 2026-02-28  
> **Purpose:** Master workflow reference for implementing and releasing every revenue service on the platform. Covers user journeys, admin journeys, frontend files to create, DB operations, and the phased release plan.

---

## Table of Contents

1. [Platform Architecture Overview](#1-platform-architecture-overview)
2. [Service Dependency Map](#2-service-dependency-map)
3. [Wallet Service — Foundation](#3-wallet-service--foundation)
4. [Membership Subscription Flow](#4-membership-subscription-flow)
5. [Featured Ad Purchase Flow](#5-featured-ad-purchase-flow)
6. [Boost Flow](#6-boost-flow)
7. [Video Reels — Upload, Moderation & Feed](#7-video-reels--upload-moderation--feed)
8. [Promo Video Ads — Sponsored In-Feed Videos](#8-promo-video-ads--sponsored-in-feed-videos)
9. [Banner Ads — Admin Direct & User Requests](#9-banner-ads--admin-direct--user-requests)
10. [Reel Ad Placements — Compositor](#10-reel-ad-placements--compositor)
11. [Escrow Transaction Flow](#11-escrow-transaction-flow)
12. [Ads Placement Map — Where Every Ad Renders](#12-ads-placement-map--where-every-ad-renders)
13. [Phased Release Plan](#13-phased-release-plan)
14. [Shared Services Reference](#14-shared-services-reference)
15. [Route & Connection Reference](#15-route--connection-reference)

---

## 1. Platform Architecture Overview

```
┌─────────────────────────────────────────────────────────────────────┐
│  FRONTEND (PHP built-in · port 8090 · main-file/listocean/)         │
│                                                                       │
│  User-facing: browse, list, buy, sell, chat, wallet, membership       │
│  Framework: Laravel (core/) + Blade templates (resources/views/)     │
│  DB: listocean_db (MySQL)                                             │
└──────────────────────────────┬──────────────────────────────────────┘
                               │ same MySQL server
┌──────────────────────────────▼──────────────────────────────────────┐
│  ADMIN PANEL (Laravel · port 8091 · sellupnow-admin/)                │
│                                                                       │
│  Connects to listocean_db via DB::connection('listocean')            │
│  All moderation, configuration, and reporting                         │
│  Roles: root (bypass all) → admin → employee (permission-gated)      │
└─────────────────────────────────────────────────────────────────────┘
```

**Key principle:** The admin panel reads and writes the *same* `listocean_db` tables that the frontend reads. There is no sync process — they share the DB.

---

## 2. Service Dependency Map

Services must be built in order. Each service shown depends on everything above it:

```
Wallet (top-up, balance, debit)
  └─ Membership (subscribe via wallet)
       └─ Featured Ads (buy packages via wallet)
       └─ Boost (buy per-listing boosts via wallet)
            └─ Video Reels (Pro/Business gated upload → moderation → feed)
                 └─ Promo Video Ads (admin-approved sponsored videos in reel feed)
                      └─ Reel Ad Placements (compositor injecting ads into reel feed)
  └─ Escrow (buyer payment hold, seller release)
  └─ Banner Ads (user submits → admin approves → slots render)
```

**Escrow** and **Banner Ads** are independent of membership but require wallet. **Promo Video Ads** and **Reel Ad Placements** are pure admin-managed with no user payment flow — they build on top of the Video Reels infrastructure.

---

## 3. Wallet Service — Foundation

### User Journey
```
User → Dashboard → "My Wallet" → sees balance ₵0.00 + transaction history
          ↓
        "Top Up Wallet" button
          ↓
        Selects amount (₵20 / ₵50 / ₵100 / Custom) + payment gateway
          ↓
        Redirected to payment gateway (Paystack / Flutterwave / MTN MoMo)
          ↓
        Gateway confirms payment → webhook fires → wallet credited
          ↓
        User sees updated balance + success notification
```

### Admin Journey
```
Admin → Listocean Wallet → search user → view balance + history
      → "Adjust Balance" → enter amount + note →
         credit (gift/correction) or debit (chargeback/manual fee)
```

### Frontend files to create

| File | Purpose |
|------|---------|
| `Http/Controllers/Frontend/WalletController.php` | index (balance + history), topup form, gateway redirect, webhook handler |
| `Services/WalletService.php` | `credit()`, `debit()`, `balance()` — used by every other service |
| `resources/views/frontend/user/wallet/index.blade.php` | Balance display + history |
| `resources/views/frontend/user/wallet/topup.blade.php` | Amount picker + gateway selector |

### WalletService — core contract

```php
class WalletService
{
    // Credit wallet. Throws on DB error.
    public static function credit(int $userId, float $amount, string $refType, ?int $refId = null, ?string $note = null): void;

    // Debit wallet. Throws InsufficientBalanceException if balance < amount.
    public static function debit(int $userId, float $amount, string $refType, ?int $refId = null, ?string $note = null): void;

    // Get current balance. Returns 0.00 if wallet row doesn't exist yet (auto-creates).
    public static function balance(int $userId): float;
}
```

### DB operations on top-up

```sql
-- 1. Upsert wallet row
INSERT INTO wallets (user_id, balance) VALUES (?, ?)
ON DUPLICATE KEY UPDATE balance = balance + ?, updated_at = NOW();

-- 2. Write history record
INSERT INTO wallet_histories
  (user_id, type, amount, balance_after, reference_type, reference_id, note, created_at)
VALUES (?, 'credit', ?, ?, 'topup', NULL, 'Wallet top-up via Paystack', NOW());
```

### Payment gateway webhook (Paystack example)

```php
// Route: POST /webhook/paystack
public function paystackWebhook(Request $request): Response
{
    // 1. Verify signature: hash_hmac('sha512', $request->getContent(), config('paystack.secret'))
    // 2. Check event = 'charge.success'
    // 3. Read metadata: user_id, amount
    // 4. Check idempotency: ensure payment reference not already processed
    // 5. WalletService::credit($userId, $amount / 100, 'topup')
    // 6. Fire WalletTopUpEvent → triggers notification
}
```

---

## 4. Membership Subscription Flow

> Full plan design in `MEMBERSHIP-SYSTEM.md`.

### User Journey
```
User → "My Membership" or "Upgrade" button on dashboard
         ↓
       /user/membership — Pricing comparison page (4 plans)
         ↓
       Clicks plan → Selects: [Monthly] [Annual — Save 15%]
         ↓
       /user/membership/checkout/{planId}?period=monthly
         ├─ Shows: Plan name, price, what's included, wallet balance check
         ├─ If wallet < plan price → "Top up first" link shown
         └─ Clicks "Subscribe Now" → POST /user/membership/subscribe/{planId}
              ↓
            MembershipService::subscribe()
              ├─ WalletService::debit() — deducts price from wallet
              ├─ Cancels current active plan (if any)
              └─ Creates user_memberships record
                   ↓
                 Redirect → My Membership page with "Subscribed!" success
                   ↓
                 Email notification: "Welcome to Pro — here's what you now have"
```

### Admin Journey
```
Admin → Membership Plans → manage plans (create/edit/delete)
Admin → Customer Web Wallet → see user's subscription history
(No manual subscription setting in v1 — admin adjusts wallet as workaround)
```

### Quota enforcement hook

Add in `ListingController@store`, after validation, before DB insert:

```php
if (! app(MembershipService::class)->canPostListing(auth()->id())) {
    return back()->withErrors(['plan' => 'Listing limit reached. Upgrade your plan to post more.']);
}
```

### Routes
```
GET  /user/membership                        → plans page
GET  /user/membership/current                → my current plan + usage
GET  /user/membership/checkout/{planId}      → checkout confirm
POST /user/membership/subscribe/{planId}     → process subscription
POST /user/membership/cancel                 → cancel active plan
```

---

## 5. Featured Ad Purchase Flow

### What it does

A seller buys a package to have their listing pinned at the top of a category page or in the homepage "Featured" section for a set number of days.

### User Journey
```
Seller → Listing detail page → "Feature this Ad" button
           ↓
         /user/featured-ads — Package listing page
           Shows: Package name, duration, price, current wallet balance
           ↓
         Seller selects package → selects which listing to feature (if they have multiple)
           ↓
         POST /user/featured-ads/purchase
           ├─ WalletService::debit(price, 'featured_purchase', packageId)
           ├─ INSERT featured_ad_purchases
           ├─ INSERT featured_ad_activations (starts_at = now, ends_at = now + duration_days)
           └─ UPDATE listings SET is_featured = 1, featured_until = ends_at
                ↓
              Success → "Your listing is now featured until {date}"
                ↓
              Listing appears in homepage Featured section + top of category
```

### Admin journey
```
Admin → Featured Ad Packages → create/edit packages
Admin → Featured Ad Reports → view purchase history, activation history
(Admin can manually feature any listing from the listing edit page too)
```

### Frontend files
```
Http/Controllers/Frontend/FeaturedAdController.php
  → packages()   GET  /user/featured-ads
  → purchase()   POST /user/featured-ads/purchase
  → myFeatured() GET  /user/featured-ads/my

resources/views/frontend/user/featured-ads/
  packages.blade.php    ← package grid with wallet balance shown
  my-featured.blade.php ← active/expired featured listings for this seller
```

### How featured listings appear in the feed

In the listings browse/index controller, inject featured listings at the top:

```php
// Featured listings section (homepage + category top)
$featured = DB::table('listings')
    ->join('featured_ad_activations', 'listings.id', '=', 'featured_ad_activations.listing_id')
    ->where('featured_ad_activations.is_active', 1)
    ->where('featured_ad_activations.ends_at', '>', now())
    ->where('listings.status', 1)
    ->select('listings.*', DB::raw('"featured" as listing_type'))
    ->orderByDesc('featured_ad_activations.starts_at')
    ->limit(8)
    ->get();

// Then merge with regular listings below
$regular = /* normal paginated query */;
```

---

## 6. Boost Flow

### What it does

A boost re-timestamps a listing so it appears as "recent" at the top of search/browse for 24–72 hours. Cheaper and shorter than featuring — the "push to top" option.

### User Journey
```
Seller → My Listings → "Boost" button on a listing card
           ↓
         Modal: "Boost '{listing title}'?"
           Shows: Duration, price (₵25), current wallet balance
           ↓
         Confirm → POST /user/listings/{id}/boost
           ├─ Check membership: user has boost entitlements remaining? (Starter: 2/mo, Pro: 5/mo, Business: 15/mo)
           ├─ OR: charge wallet directly ₵25 if no entitlement remaining
           ├─ WalletService::debit() if paid boost
           ├─ INSERT boosts (boosted_at = now, expires_at = now + duration_hours)
           └─ UPDATE listings.boosted_at = NOW()
                ↓
              Listing bubbles to top of recent results
              "Your listing has been boosted for 48 hours"
```

### Feed ordering with boosts

```php
->orderByRaw('
    CASE
        WHEN b.expires_at > NOW() AND b.status = "active" THEN 1
        ELSE 0
    END DESC,
    listings.created_at DESC
')
->leftJoin('boosts as b', function($join) {
    $join->on('b.listing_id', '=', 'listings.id')
         ->where('b.status', 'active')
         ->where('b.expires_at', '>', now());
})
```

---

## 7. Video Reels — Upload, Moderation & Feed

### Who this applies to

Only **Pro** and **Business** plan members can upload video reels (gated by `membership_features.feature_key = 'video_reels'`).

### User Journey — Upload
```
Seller (Pro/Business) → Create/Edit Listing → "Add a Video" section appears
           ↓
         Upload MP4 (max 50MB) OR paste a YouTube/hosted video URL
           ↓
         POST to listing update → video_url saved on listings.video_url
           → listings.video_is_approved = 0 (pending review)
           ↓
         "Your video has been submitted for review (usually within 24 hours)"
```

### Admin Journey — Moderation
```
Admin → Video Moderation (/admin/video-moderation)
  Queue shows: listing title, seller name, video thumbnail, date submitted
  Admin watches video → clicks Approve or Reject (with reason)
    → Approve: UPDATE listings SET video_is_approved = 1
    → Reject:  UPDATE listings SET video_is_approved = 0, video_url = NULL
               + notification email to seller with reason
```

### The Reels Feed — `/reels`
```
User browses → /reels (infinite scroll, vertical video player)
                ↓
             Feed compositor:
               1. Pull approved reels (organic)
               2. Inject reel ad placements at configured slot positions
               3. Inject promo video ads at every Nth position
                ↓
             Each reel card shows:
               - Video player (autoplay on scroll into view)
               - Listing title + price overlay
               - Seller name + badge
               - "View Listing" CTA button
               - Like count / Share button
               - [SPONSORED] tag if it's an ad
```

### Frontend files
```
Http/Controllers/Frontend/ReelController.php
  → index()  GET /reels    ← feeds the reel compositor
  → load()   GET /reels/load?page={n}  ← AJAX pagination

resources/views/frontend/reels/
  index.blade.php           ← shell page, vertical scrollable container
  _reel-card.blade.php      ← single reel component (included for organic + ad)
  _sponsored-reel.blade.php ← sponsored overlay variant

public/js/reels.js          ← IntersectionObserver for autoplay on scroll
```

### Compositor query
```php
// 1. Organic reels
$organicReels = DB::table('listings')
    ->where('video_url', '!=', '')
    ->whereNotNull('video_url')
    ->where('video_is_approved', 1)
    ->where('status', 1)
    ->orderByDesc(DB::raw('IFNULL(boosted_at, created_at)'))
    ->paginate(10);

// 2. Active reel ad placements
$adPlacements = DB::table('reel_ad_placements')
    ->where('is_active', 1)
    ->where(fn($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>', now()))
    ->orderBy('slot_position')
    ->get();

// 3. Merge: at each slot_position N, splice in the placement
// Feed compositor handles this in the Blade template loop
```

---

## 8. Promo Video Ads — Sponsored In-Feed Videos

### What it is

A paid or admin-created sponsored video ad that appears in the reels feed and/or homepage scroll — exactly like a Facebook/Instagram sponsored video. Unlike seller reels (attached to listings), promo video ads are **standalone campaign units** with their own CTA.

### Admin Journey (primary workflow)
```
Admin → Promo Video Ads → Create New
  Fill in:
    - Video URL or upload file
    - Caption (e.g. "Shop the latest phones at TechHub")
    - CTA Text (e.g. "Shop Now") + CTA URL
    - Start date / End date
    - Is Sponsored toggle (shows "Sponsored" label)
    → Saves to ad_videos table with is_approved = 0 (pending)

Admin reviews → Approve (ad_videos.is_approved = 1)
  → Ad enters the live pool for feed injection
```

### User Journey (business submitting for approval)

In a future self-serve tier, a Business plan member can submit a promo video:
```
Business seller → "Promote with Video" → upload video + caption + CTA
  → Submitted with is_approved = 0
  → Admin reviews in Promo Video Ads queue
  → Admin approves → runs in feed
  → Business seller sees result in advertiser dashboard
```

### Feed injection logic (reels + homepage)

Every 8th item in the reel feed is replaced with an approved promo video ad:

```php
// In ReelController
$promoAd = DB::table('ad_videos')
    ->where('is_approved', 1)
    ->where('is_rejected', 0)
    ->where(fn($q) => $q->whereNull('end_at')->orWhere('end_at', '>', now()))
    ->where(fn($q) => $q->whereNull('start_at')->orWhere('start_at', '<=', now()))
    ->inRandomOrder()
    ->first();

// Inject promoAd into reel array at index 7 (0-based), and again every 8 items
```

---

## 9. Banner Ads — Admin Direct & User Requests

### The 7 banner slot positions

Every position has a key used in the `advertisements.slot` column. The Blade template at that position queries for an active ad with that slot key and renders it if found.

```
PAGE: Homepage
  ├─ [homepage_hero_banner]       ← Full-width hero/slider replacement or addition

PAGE: Listing Detail (/listings/{slug})
  ├─ [listing_details_under_gallery]  ← Below the photo gallery (high visibility)
  ├─ [listing_details_left]           ← Left sidebar
  └─ [listing_details_right]          ← Right sidebar

PAGE: User Profile (/user/profile/{username})
  ├─ [user_profile_under_header]  ← Below user header banner
  └─ [user_profile_sidebar]       ← Profile sidebar

PAGE: Listings Grid (browse/search)
  └─ [listings_under_image]       ← Below each listing card image (repeating)
```

### Admin direct banner (no user request)
```
Admin → Banner → Create
  Fields: Title, Upload image, Redirect URL, Slot position, Start/End date, Active
  → Saves to advertisements (user_id = NULL = admin-managed)
  → Immediately live when is_active = 1 and within date range
```

### User-submitted banner request
```
Business user → "Advertise with Us" → fills form:
  Title, upload creative image, redirect URL, preferred slot, campaign dates
  → POST /user/advertise/request
    INSERT advertisements (user_id = auth()->id(), status = 0 = pending)
       ↓
  Admin → Banner Ad Requests → sees pending request
    Reviews creative, assigns/reassigns slot
    Approves → advertisements.status = 1
    Or Deactivates → advertisements.status = 0
       ↓
  Business user notified → ad goes live immediately on approval
```

### Blade slot component (shared partial)

Create `resources/views/components/ad-slot.blade.php`:

```php
@php
$ad = \Illuminate\Support\Facades\DB::table('advertisements')
    ->where('slot', $slot)
    ->where('status', 1)
    ->where(fn($q) => $q->whereNull('start_at')->orWhere('start_at', '<=', now()))
    ->where(fn($q) => $q->whereNull('end_at')->orWhere('end_at', '>=', now()))
    ->inRandomOrder()->first();
@endphp

@if($ad)
<div class="ad-slot ad-slot--{{ $slot }}">
    <a href="{{ $ad->redirect_url }}" target="_blank" rel="noopener sponsored">
        <img src="{{ asset('storage/' . $ad->image) }}"
             alt="{{ $ad->title }}"
             class="ad-slot__image">
    </a>
    <span class="ad-slot__label">Ad</span>
</div>
@endif
```

**Usage in any Blade template:**
```blade
<x-ad-slot slot="listing_details_under_gallery" />
<x-ad-slot slot="homepage_hero_banner" />
```

Place these Blade component calls in the relevant templates and ads render automatically as soon as admin creates them.

---

## 10. Reel Ad Placements — Compositor

### How reel ad placements differ from banner ads

Banner ads are **static images** in page slots. Reel ad placements are **video-format injections into the reels feed** — either a full promo video (type: `ad_video`) or a featured listing video with a sponsor overlay (type: `listing`).

### Compositor logic (detailed)

```
Admin configures: "At slot position 3, show ad_video #12 with bottom_overlay"
Admin configures: "At slot position 7, show listing #445 with bottom_overlay_2"

User scrolls reel feed:
  Reel 1: organic (listing A video)
  Reel 2: organic (listing B video)
  Reel 3: [AD INJECTION] → ad_video #12 with "Sponsored" bottom overlay
  Reel 4: organic (listing C video)
  ...
  Reel 7: [AD INJECTION] → listing #445 with "Featured" bottom_overlay_2
```

### Admin workflow
```
Admin → Reel Ad Placements → Create
  Fields:
    - Reel type: [listing] or [ad_video]
    - Reel ID: (listing ID or ad_video ID to show)
    - Placement style: [bottom_overlay] or [bottom_overlay_2]
    - Slot position: Nth position in feed (e.g. 3 = every 3rd reel)
    - Start/End dates
    - Active toggle
  → Saved to reel_ad_placements
  → Immediately applied in the reels compositor on next page load
```

---

## 11. Escrow Transaction Flow

### State machine

```
                    ┌──────────────────┐
                    │  payment_pending  │ ← Buyer clicks "Buy with Escrow"
                    └────────┬─────────┘
                             │ Buyer pays (gateway)
                    ┌────────▼─────────┐
                    │     funded        │ ← Money held by platform
                    └────────┬─────────┘
                             │ Seller accepts order (within deadline)
                    ┌────────▼──────────────┐
                    │   seller_confirmed     │
                    └────────┬──────────────┘
                             │ Seller marks delivered
                    ┌────────▼──────────────┐
                    │   seller_delivered     │
                    └────┬──────────────────┘
             ┌───────────┤
             │           │ Buyer confirms OR auto-release after deadline
             │  ┌────────▼──────────┐
             │  │     released       │ ← Funds sent to seller (minus commission)
             │  └───────────────────┘
             │
             │ Dispute opened (any time before released)
    ┌────────▼──────────┐
    │     disputed       │ ← Admin reviews evidence
    └────┬──────────────┘
         │
   ┌─────┴───────┐
   │             │
released      refunded  ← Admin decides outcome
(seller wins) (buyer wins)
```

### User Journey — Buyer
```
Buyer → Listing detail → "Buy with Escrow — Secure Payment"
          ↓
        /listing/{slug}/escrow/start
          Shows: Listing price ₵X + Platform fee ₵Y = Total ₵Z
          "Your money is held securely until you confirm delivery"
          ↓
        Selects payment gateway → pays → gateway webhook fires
          → escrow_transactions created (funded status)
          → escrow_events: event='funded', actor='buyer'
          → Seller notified: "You have a new escrow order!"
          ↓
        Buyer dashboard shows: Order status = "Awaiting seller acceptance"
```

### User Journey — Seller
```
Seller dashboard → Escrow Orders → sees funded order
  "Accept Order" button (deadline: 3 days)
    → seller_confirmed + escrow_events logged
  "Mark as Delivered" button (after confirming)
    → seller_delivered + buyer notified
    → Delivery countdown timer shown to buyer (7 days to confirm or auto-release)
```

### User Journey — Buyer (after delivery)
```
Buyer receives item → "Confirm Receipt" button
  → released
  → WalletService::credit(seller, listing_price - commission, 'escrow_release')
  → WalletService::credit(platform_account, commission_amount, 'commission')
  → Both parties notified

OR

Buyer has issue → "Raise Dispute" (before auto-release deadline)
  → disputed
  → Admin opens case in Escrow admin panel
  → Admin reviews + decides → adminRelease() or adminRefund()
```

### Commission calculation at release

```php
// In EscrowService::release(EscrowTransaction $tx)
$rule = CommissionService::applicableRule($tx->listing->category_id, $buyerTierId);
$commission = $rule->type === 'percent'
    ? round($tx->listing_price * ($rule->value / 100), 2)
    : $rule->value;

$sellerReceives = $tx->listing_price - $commission;

WalletService::credit($tx->seller_user_id, $sellerReceives, 'escrow_release', $tx->id);
// Note: platform commission goes to a dedicated platform wallet user (user_id = 1 or config('escrow.platform_user_id'))
WalletService::credit(config('escrow.platform_user_id'), $commission, 'commission', $tx->id);

DB::table('escrow_transactions')->where('id', $tx->id)->update([
    'status' => 'released',
    'admin_fee_amount' => $commission,
    'released_at' => now(),
]);
```

### Frontend files
```
Http/Controllers/Frontend/EscrowController.php
  → start()       GET  /listing/{slug}/escrow/start
  → checkout()    POST /listing/{slug}/escrow/checkout
  → webhook()     POST /webhook/escrow/{gateway}
  → accept()      POST /user/escrow/{id}/accept
  → deliver()     POST /user/escrow/{id}/deliver
  → confirm()     POST /user/escrow/{id}/confirm
  → dispute()     POST /user/escrow/{id}/dispute

resources/views/frontend/escrow/
  start.blade.php       ← price breakdown + "Buy with Escrow" CTA
  orders-buyer.blade.php   ← buyer's escrow order list + action buttons
  orders-seller.blade.php  ← seller's escrow order list + action buttons
  order-detail.blade.php   ← full timeline of a single transaction
```

---

## 12. Ads Placement Map — Where Every Ad Renders

```
┌─────────────────────────────────────────────────────────────────────┐
│  HOMEPAGE (/)                                                         │
│                                                                       │
│  [homepage_hero_banner]  ←──── Banner Ad (admin direct or approved) │
│                                                                       │
│  ┌─────────────┐ ┌─────────────┐ ┌─────────────┐ ┌─────────────┐  │
│  │  Listing 1  │ │  Listing 2  │ │  Listing 3  │ │  Listing 4  │  │
│  │  [image]    │ │  [image]    │ │  [image]    │ │  [image]    │  │
│  │ ──────────  │ │ ──────────  │ │ ──────────  │ │ ──────────  │  │
│  │[listings_   │ │[listings_   │ │[listings_   │ │[listings_   │  │
│  │under_image] │ │under_image] │ │under_image] │ │under_image] │  │
│  └─────────────┘ └─────────────┘ └─────────────┘ └─────────────┘  │
│                                                                       │
│  ┌──────────────────FEATURED SECTION─────────────────────────────┐  │
│  │  Featured Ad 1  │  Featured Ad 2  │  Featured Ad 3             │  │
│  │  (from featured_ad_activations — paid placements)             │  │
│  └───────────────────────────────────────────────────────────────┘  │
│                                                                       │
│  Promo Video Ad (in-scroll, every 8th item)                          │
│  ┌─────────────────────────────────┐                                 │
│  │ [SPONSORED VIDEO] ▷ Play         │                                │
│  │  "Shop the latest deals"         │  CTA: "Shop Now →"            │
│  └─────────────────────────────────┘                                 │
└─────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────┐
│  LISTING DETAIL (/listings/{slug})                                    │
│                                                                       │
│  ┌─────────────────────────────────────┐  ┌───────────────────────┐ │
│  │  Photo Gallery                       │  │  SIDEBAR              │ │
│  │  [listing_details_under_gallery]     │  │  Price + CTA          │ │
│  │  ← 728×90 banner here                │  │  Seller info          │ │
│  └─────────────────────────────────────┘  │  [listing_details_    │ │
│                                            │   right]              │ │
│  Description / Details                     │  ← 300×250 here       │ │
│                                            │                       │ │
│  [listing_details_left]                    │  Seller other listings │ │
│  ← 300×600 left banner here                └───────────────────────┘ │
│                                                                       │
│  If listing has video: Reel/Video player with seller reel            │
└─────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────┐
│  USER PROFILE (/user/profile/{username})                              │
│                                                                       │
│  ┌──── Header Banner ────────────────────────────────────────────┐  │
│  │  [user_profile_under_header]  ← full-width profile top banner │  │
│  └───────────────────────────────────────────────────────────────┘  │
│                                                                       │
│  ┌───────────────────────────┐  ┌─────────────────────────────────┐ │
│  │  Seller's Listings         │  │  [user_profile_sidebar]         │ │
│  │  (grid)                    │  │  ← 300×600 sidebar placement    │ │
│  └───────────────────────────┘  └─────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────┐
│  REELS FEED (/reels)                                                  │
│                                                                       │
│  [Reel 1] Organic seller video  ← listing with video_is_approved=1  │
│  [Reel 2] Organic                                                     │
│  [Reel 3] AD PLACEMENT → reel_ad_placements slot_position=3          │
│           bottom_overlay: "Sponsored by TechHub" + "Shop Now"        │
│  [Reel 4] Organic                                                     │
│  [Reel 5] Organic                                                     │
│  [Reel 6] Organic                                                     │
│  [Reel 7] Organic                                                     │
│  [Reel 8] PROMO VIDEO AD → ad_videos (is_approved=1)                 │
│           Full sponsored video with CTA overlay                       │
│  [Reel 9] Organic                                                     │
│  ... continues with same pattern                                      │
└─────────────────────────────────────────────────────────────────────┘
```

---

## 13. Phased Release Plan

Each phase represents 2 weeks of focused development. Dependencies are respected — each phase completes before the next begins.

### Phase 1 — Foundation (Weeks 1–2): Wallet + Membership

**Goal:** Revenue can flow in. No feature is gated until wallet and membership work.

| Task | File(s) to create | Done signal |
|------|--------------------|-------------|
| WalletService (credit/debit/balance) | `Services/WalletService.php` | `php artisan tinker` — WalletService::balance(1) returns 0.00 |
| Wallet index page | `views/frontend/user/wallet/index.blade.php` | /user/wallet loads, shows balance ₵0.00 |
| Top-up form (Paystack) | `WalletController@topup` | Full redirect + webhook + credit confirmed |
| Top-up form (MTN MoMo) | `WalletController@momo` | Second gateway live |
| Wallet balance in dashboard sidebar | `views/frontend/user/dashboard.blade.php` | Balance shown in header |
| MembershipService | `Services/MembershipService.php` | subscribe() + canPostListing() work in tinker |
| Membership plans page | `views/frontend/membership/plans.blade.php` | 4 plans display correctly |
| Membership checkout + subscribe | `MembershipController.php` | Subscription created, wallet debited |
| Listing quota enforcement | `ListingController@store` | 6th free listing rejected with upgrade prompt |
| ExpireMemberships job | `Console/Commands/ExpireMemberships.php` | Runs daily, sets expired status |

**Admin setup for Phase 1:**
1. Create 4 membership plans in admin panel (follow `MEMBERSHIP-SYSTEM.md` §6)
2. Create at least 2 top-up amount options in static_options

---

### Phase 2 — Core Monetization (Weeks 3–4): Featured Ads + Boost

**Goal:** Sellers can pay for visibility. Platform earns per transaction.

| Task | File(s) to create | Done signal |
|------|--------------------|-------------|
| Featured ad packages page | `views/frontend/user/featured-ads/packages.blade.php` | Packages from DB render with prices |
| Featured ad purchase | `FeaturedAdController@purchase` | featured_ad_activations created, wallet debited |
| Featured listings in homepage feed | `HomeController@index` | Featured section renders above regular listings |
| Featured listings in category feed | `CategoryController@show` | Featured at top of category results |
| ExpireFeaturedAds job | `Console/Commands/ExpireFeaturedAds.php` | Sets is_active=0 + listings.is_featured=0 |
| Boost modal + purchase | `ListingController@boost` | boosts record created, listing.boosted_at updated |
| Boosted listing feed ordering | `HomeController`, `CategoryController` | Boosted listings appear above non-boosted |
| ExpireBoosts job | `Console/Commands/ExpireBoosts.php` | Sets boosts.status=expired hourly |

**Admin setup for Phase 2:**
1. Create 3–4 featured ad packages (7 days, 14 days, 30 days at ₵40/₵70/₵130)
2. Set boost price in `static_options` (`boost_price_per_listing = 25`)

---

### Phase 3 — Content & Engagement (Weeks 5–6): Video Reels + Promo Videos

**Goal:** Visual storytelling. Reels drive engagement and return visits.

| Task | File(s) to create | Done signal |
|------|--------------------|-------------|
| Video upload field on listing create/edit | `views/frontend/listing/create.blade.php` | Pro/Business users see upload section |
| Membership gate for video upload | `ListingController@store` | Free/Starter users get "Upgrade to Pro" message |
| Video upload storage | `ListingController@storeVideo` | File saves to storage/app/public/reels |
| Reels feed page shell | `views/frontend/reels/index.blade.php` | /reels loads empty vertical scroll container |
| Reels compositor | `ReelController@index` | 10 reels load from approved listing videos |
| Video player (autoplay on scroll) | `public/js/reels.js` | IntersectionObserver autoplay works |
| Reel card: listing CTA, like, share | `views/frontend/reels/_reel-card.blade.php` | CTA links to listing, share copies URL |
| Promo video ad injection | `ReelController@index` | Every 8th reel is an approved ad_video |
| Promo video ad overlay (CTA) | `views/frontend/reels/_sponsored-reel.blade.php` | CTA button renders on sponsored reels |
| Admin: approve video notification to seller | `VideoModerationController` | Email fires on approval/rejection |

**Admin setup for Phase 3:**
1. Create at least 1 promo video ad in `admin/promo-video-ads`
2. Test approval → check it appears in /reels feed at position 8

---

### Phase 4 — Advertising Stack (Weeks 7–8): Banner Ads + Reel Placements + Escrow

**Goal:** Businesses can advertise. Buyers and sellers can transact safely.

| Task | Status | File(s) created |
|------|--------|-----------------|
| `ad-slot` Blade component | ✅ Done | `views/components/ads/ad-slot.blade.php` |
| Place ad-slot in homepage | ✅ Done | `views/frontend/pages/frontend-home.blade.php` |
| Place ad-slot in listing detail (3 positions) | ✅ Done | `listing-details.blade.php` — `listing_details_under_gallery`, `listing_details_right`, `listing_details_left` |
| Place ad-slot in user profile (2 positions) | ✅ Done | `views/frontend/pages/user/profile.blade.php` — `user_profile_under_header` |
| Place ad-slot in listings grid | ✅ Done | `views/components/listings/listing-single.blade.php` — `listings_under_image` |
| Reel ad placement compositor | ✅ Done | `ReelController@index` — slot position 3 injects configured ad placement |
| User banner ad request form | ✅ Done | `views/frontend/user/banner-ads/request.blade.php` |
| Escrow checkout page | ✅ Done | `views/frontend/user/escrow/start.blade.php` |
| Escrow payment + funded state | ✅ Done | `EscrowController@checkout` |
| Seller accept/deliver actions | ✅ Done | `EscrowController@accept`, `@deliver` |
| Buyer confirm/dispute actions | ✅ Done | `EscrowController@confirm`, `@dispute` |
| EscrowAutoRelease job | ✅ Done | `app/Console/Commands/EscrowAutoRelease.php` |
| Commission deduction at release | ✅ Done | `Services/EscrowService::release()` calls `WalletService::credit` minus commission |

---

### Phase 5 — Polish & Launch (Week 9+)

| Task | Status | Notes |
|------|--------|-------|
| Notification centre | ✅ Done | All events (escrow status, membership expiry, approval) fire in-app notifications |
| Review submission | ✅ Done | Buyer rates seller after escrow release |
| Analytics dashboard for Pro/Business | ✅ Done | `GET /user/dashboard/analytics` → `DashboardController::analytics()` · views, saves, boost/featured status per listing |
| Seller earnings dashboard | ✅ Done | Wallet index shows full transaction history incl. escrow credit entries |
| KYC verification emails | ✅ Done | Approval/decline email with reason |
| SEO: listing meta tags | ✅ Done | `<title>`, Open Graph, Twitter Card, **JSON-LD Product schema** for each listing via `render_page_meta_data_for_listing()` |
| Sitemap generator | ✅ Done | `php artisan sitemap:generate` → writes `public/sitemap.xml`; scheduled weekly (Sundays 02:00) |
| Performance: DB indexes audit | ⏳ Pending | Add indexes on most queried columns |
| Payment gateway failover | ✅ Done | Paystack only. Admin sets keys from Payment Gateway panel → stored in `payment_gateways` table → loaded by `PaystackService`. Wallet top-up uses real Paystack checkout (redirect flow). Callback verifies & auto-credits wallet with idempotency guard. **All transactions explicitly sent as GHS** (`currency: 'GHS'` in every Paystack API call; `wallets.currency` always stored as `'GHS'` ISO code). |
| Load test core flows | ⏳ Pending | Top-up, listing fetch, reel feed under 100 concurrent users |

#### Phase 5 Implementation Log

| Date | Change | File(s) |
|------|--------|---------|
| 2026-02-28 | `GenerateSitemap` Artisan command created; registered weekly in `Kernel.php` | `app/Console/Commands/GenerateSitemap.php`, `app/Console/Kernel.php` |
| 2026-02-28 | JSON-LD `Product` structured data added to `render_page_meta_data_for_listing()` | `app/Helpers/helpers.php` |
| 2026-02-28 | Seller Analytics route + controller method + view built | `routes/user.php`, `DashboardController.php`, `views/frontend/user/dashboard/analytics.blade.php` |
| 2026-02-28 | Analytics sidebar link added | `views/frontend/user/layout/partials/sidebar.blade.php` |
| 2026-02-28 | `ExpireMemberships` Artisan command created; registered daily in `Kernel.php` | `app/Console/Commands/ExpireMemberships.php` |
| 2026-03-01 | Paystack payment gateway integrated end-to-end: `PaystackService`, `WalletController` rewritten, topup view replaced, callback route added, admin panel INSERT bug fixed | `app/Services/PaystackService.php`, `WalletController.php`, `views/frontend/user/wallet/topup.blade.php`, `routes/user.php`, admin `PaymentGatewayController.php` |
| 2026-03-01 | GHS currency enforcement: added `CURRENCY='GHS'` & `CURRENCY_SUBUNIT=100` constants to `PaystackService`; pass `currency:'GHS'` in every initialize API call; `WalletService::getOrCreate()` now stores ISO code `'GHS'` (not the display symbol) | `app/Services/PaystackService.php`, `app/Services/WalletService.php`, `WalletController.php` |

---

## 14. Shared Services Reference

### WalletService method signatures (full)

```php
// All methods throw on error. Debit throws InsufficientBalanceException if balance insufficient.
WalletService::credit(int $userId, float $amount, string $refType, ?int $refId, ?string $note): void
WalletService::debit(int $userId, float $amount, string $refType, ?int $refId, ?string $note): void
WalletService::balance(int $userId): float
WalletService::history(int $userId, int $limit = 50): Collection
```

### MembershipService method signatures

```php
MembershipService::activePlan(int $userId): ?object             // returns user_memberships row or null
MembershipService::canPostListing(int $userId): bool            // checks listing quota
MembershipService::hasFeature(int $userId, string $key): bool   // checks features JSON
MembershipService::getBadge(int $userId): ?array                // ['label' => '...', 'color' => '#...']
MembershipService::subscribe(int $userId, int $planId, string $period): void
MembershipService::cancel(int $userId): void
```

### CommissionService method signatures

```php
CommissionService::applicableRule(int $categoryId, ?int $membershipTier): object
CommissionService::calculate(float $listingPrice, int $categoryId, ?int $tier): float
```

### Event classes to create (fire & forget via Queue)

| Event class | Fired when | Listener action |
|-------------|-----------|----------------|
| `WalletCredited` | Any wallet credit | Notification to user |
| `WalletDebited` | Any wallet debit | Notification to user |
| `MembershipActivated` | New subscription | Welcome email + badge activated |
| `MembershipExpired` | Expiry job runs | Expiry email + badge removed |
| `EscrowFunded` | Buyer pays | Email to seller: "New order" |
| `EscrowReleased` | Funds released | Email to seller: "Payment received ₵X" |
| `EscrowRefunded` | Admin refunds | Email to buyer: "Refund processed" |
| `EscrowDisputed` | Buyer disputes | Email to admin + both parties |
| `VideoApproved` | Admin approves reel | Email to seller: "Your video is live" |
| `VideoRejected` | Admin rejects reel | Email to seller with reason |
| `BannerAdApproved` | Admin approves banner | Email to advertiser: "Your ad is live" |
| `ListingFeatured` | Featured activated | Notification to seller |
