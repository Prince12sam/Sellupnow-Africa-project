# SellUpNow Admin Panel — Complete Service Audit

> **Purpose:** Maps every service in the admin panel — what is fully built, what is scaffolded, what DB tables it touches, and exactly what still needs to be built on the frontend to complete the user-facing loop.
>
> **Last updated:** 2026-02-28
> **Admin URL:** `http://127.0.0.1:8091/admin`
> **Legend:** ✅ Full implementation · 🔶 Admin done, frontend pending · ⚠️ Scaffold / stub only · ❌ Not started

---

## Table of Contents

1. [Classified Listings & Moderation](#1-classified-listings--moderation)
2. [Featured Ad Packages](#2-featured-ad-packages)
3. [Boost](#3-boost)
4. [Banner Ads — Admin Direct](#4-banner-ads--admin-direct)
5. [Banner Ad Requests — User Self-Serve](#5-banner-ad-requests--user-self-serve)
6. [Promo Video Ads](#6-promo-video-ads)
7. [Seller Video Reels (Video Moderation)](#7-seller-video-reels-video-moderation)
8. [Reel Ad Placements](#8-reel-ad-placements)
9. [Advertiser Portal](#9-advertiser-portal)
10. [Escrow & Dispute Resolution](#10-escrow--dispute-resolution)
11. [Wallet](#11-wallet)
12. [Commission Rules](#12-commission-rules)
13. [Membership Plans & Features](#13-membership-plans--features)
14. [KYC / Identity Verification](#14-kyc--identity-verification)
15. [Other Admin Services](#15-other-admin-services)
16. [DB Tables That Need Creating (Migrations Pending)](#16-db-tables-that-need-creating)
17. [Frontend Implementation Checklist](#17-frontend-implementation-checklist)

---

## 1. Classified Listings & Moderation

**Status: 🔶 Admin done · Frontend core done · Enhancements pending**

### Admin URL
`admin/listing-moderation` · `admin/listing-report`

### What the admin can do
- View all listings across queues: **All** / **New (pending)** / **Updated** / **Reported** / **Removed**
- Approve or reject a listing with a reason
- View listing detail (title, photos, price, location, seller info)
- Remove a listing (soft delete)
- View listing reports (user-flagged fraud/scam/prohibited) and dismiss or act on them

### DB tables (listocean_db)
| Table | Key columns |
|-------|------------|
| `listings` | `id`, `title`, `slug`, `price`, `status`, `is_published`, `video_url`, `video_is_approved`, `user_id`, `category_id`, `is_featured`, `featured_until`, `boosted_at`, `deleted_at` |
| `listing_reports` | `id`, `listing_id`, `reporter_user_id`, `reason`, `status` |
| `listing_images` | `listing_id`, `image` |

### Listing status values
| `status` | Meaning |
|----------|---------|
| `0` | Pending admin review |
| `1` | Active / live |
| `2` | Rejected |
| `3` | Sold |
| `4` | Expired |

### Frontend gap
- ✅ Browse, search, listing detail all working
- 🔶 Admin rejection reason shown to seller — needs notification email + in-app notification
- 🔶 "Sold" marking by seller — frontend button exists but backend confirmation needs wiring
- 🔶 Listing expiry — no scheduled command yet to expire old listings automatically

---

## 2. Featured Ad Packages

**Status: 🔶 Admin done · Frontend purchase flow pending**

### Admin URL
`admin/featured-ad-packages` (package catalog) · `admin/featured-ad-reports` (purchase/activation reports)

### What the admin can do
- **Create / Edit / Delete** featured ad packages (the products sellers buy)
- View featured ad **purchase history** (`featured_ad_purchases` table)
- View featured ad **activation history** (`featured_ad_activations` table)

### Package fields (what admin defines per package)
| Field | Purpose |
|-------|---------|
| `name` | Display name, e.g. "Gold Feature – 30 days" |
| `price` | Cost to seller (deducted from wallet) |
| `duration_days` | How many days the listing stays featured |
| `position` | Where it appears: top of category / homepage featured section |
| `max_listings` | Listings that can be active under this package at once |
| `is_active` | Whether sellers can currently purchase this package |

### DB tables (listocean_db)
| Table | Key columns |
|-------|------------|
| `featured_ad_packages` | `id`, `name`, `price`, `duration_days`, `position`, `is_active` |
| `featured_ad_purchases` | `id`, `user_id`, `package_id`, `listing_id`, `amount_paid`, `purchased_at` |
| `featured_ad_activations` | `id`, `purchase_id`, `listing_id`, `starts_at`, `ends_at`, `is_active` |

> ⚠️ **Migrations for these tables must be created in the listocean frontend DB.** The admin controller uses `Schema::connection('listocean')->hasTable(...)` as a safety check and gracefully shows empty state if tables don't exist yet.

### Frontend gap (what must be built)
1. **Package listing page** — seller views available packages at `/user/featured-ads` or on listing detail
2. **Purchase flow** — seller selects package + listing → wallet balance check → deduct → create `featured_ad_purchases` + `featured_ad_activations` record
3. **Display featured listings** — homepage "Featured" section and top of category page queries `featured_ad_activations` where `ends_at > now()` and `is_active = 1`
4. **Expiry job** — scheduled command sets `featured_ad_activations.is_active = 0` when `ends_at` passes

### Data flow diagram
```
Admin creates package (featured_ad_packages)
  ↓
Seller buys package → wallet debited → featured_ad_purchases created
  ↓
featured_ad_activations created (starts_at = now, ends_at = now + duration_days)
  ↓
listings.is_featured = 1, listings.featured_until = ends_at
  ↓
Frontend browse query: WHERE is_featured = 1 AND featured_until > NOW()
  ↓
Scheduled command (daily): SET is_featured = 0 WHERE featured_until < NOW()
```

---

## 3. Boost

**Status: 🔶 Admin done · Frontend purchase + feed ordering pending**

### Admin URL
`admin/boosts`

### What the admin can do
- View all active boosts (listing, seller, boost date, expiry)
- Manually create a boost for a listing (admin gift/override)
- Edit or delete a boost record

### What a boost does
A boost is a lightweight, cheaper re-promotion. It re-timestamps the listing so it appears at the top of "recent" or "popular near you" feeds for a configurable window (24–72 hours) without the full commitment of a featured package.

### DB tables (listocean_db)
| Table | Key columns |
|-------|------------|
| `boosts` | `id`, `listing_id`, `user_id`, `boosted_at`, `expires_at`, `amount_paid`, `status` |

### Frontend gap
1. **Boost button on seller's listing** — "Boost this Ad" with price display
2. **Wallet deduction** at purchase
3. **Feed ordering** — query adds `CASE WHEN b.expires_at > NOW() THEN 1 ELSE 0 END AS is_boosted` and sorts boosted-first within recency window
4. **Expiry** — scheduled command deactivates expired boosts

---

## 4. Banner Ads — Admin Direct

**Status: ✅ Admin fully implemented · Frontend slot rendering needs per-position verification**

### Admin URL
`admin/banner`

### What the admin can do
- Upload image banners with: title, image file, link URL, position slot, start/end dates, active toggle
- Edit or delete banners
- Preview banner

### Banner position slots (defined in `BannerController::$slotOptions`)
| Slot key | Renders on |
|----------|-----------|
| `homepage_hero_banner` | Homepage hero/slider area |
| `listing_details_left` | Listing detail page, left column |
| `listing_details_right` | Listing detail page, right column |
| `listing_details_under_gallery` | Listing detail page, below photo gallery |
| `user_profile_sidebar` | User public profile page, sidebar |
| `user_profile_under_header` | User public profile page, below header |
| `listings_under_image` | Listing grid cards, below the listing image |

### DB tables (listocean_db)
| Table | Key columns |
|-------|------------|
| `advertisements` | `id`, `title`, `type` (image/video), `image`, `redirect_url`, `slot`, `start_at`, `end_at`, `status`, `user_id` (NULL = admin-managed) |

### Frontend integration
Each slot key must have a Blade component/partial that queries:
```php
DB::connection('listocean')->table('advertisements')
    ->where('slot', $slot)
    ->where('status', 1)
    ->where('type', 'image')
    ->whereNull('start_at', 'or')->where('start_at', '<=', now())
    ->whereNull('end_at', 'or')->where('end_at', '>=', now())
    ->inRandomOrder()->first();
```
Then renders `<a href="{{ $ad->redirect_url }}"><img src="{{ asset_url($ad->image) }}"></a>`.

**Gap:** This query component needs to be embedded in each of the 7 slot positions listed above.

---

## 5. Banner Ad Requests — User Self-Serve

**Status: 🔶 Admin review queue done · User submission frontend pending**

### Admin URL
`admin/banner-ad-requests`

### What the admin can do
- View pending / approved banner requests from users/businesses
- **Approve** a request → `advertisements.status = 1` → banner goes live
- **Deactivate** an approved banner → `advertisements.status = 0`
- Edit the slot assignment and redirect URL of a request

### User-submitted request fields
| Field | Purpose |
|-------|---------|
| `title` | Campaign name |
| `type` | `image` (currently; video is a separate flow) |
| `size` | Banner dimensions (e.g. 728×90, 300×250) |
| `image` | Uploaded creative artwork |
| `redirect_url` | URL clicked through to |
| `slot` | Requested page position (admin may reassign) |

### Slot-to-reel-placement link
The `BannerAdRequestsController` also queries `reel_ad_placements` to show which approved ads are additionally running as reel overlay placements — the same `advertisement_id` can serve in both banner slots AND reel overlays.

### Frontend gap
1. **Advertiser submission page** — business registers/logs in, uploads banner creative, selects slot, submits request
2. **Advertiser dashboard** — view status of submitted requests (pending/approved/rejected)
3. **Payment before submission** — rate card lookup → wallet deduction → submit

---

## 6. Promo Video Ads

**Status: 🔶 Admin done · DB table pending · Frontend feed injection pending**

### Admin URL
`admin/promo-video-ads`

### What the admin can do
- View pending / approved / rejected promotional video ads
- **Create** a promo video ad (upload video or paste URL, caption, CTA text + URL, start/end dates, sponsored toggle)
- **Edit** a promo video ad
- **Approve** a user-submitted promo video → `ad_videos.is_approved = 1`
- **Reject** with reason → `ad_videos.is_rejected = 1`, `reject_reason` stored

### What a promo video ad is
This is an admin-controlled (or admin-approved) video that appears in the listing/homepage feed or reels feed at configured intervals — exactly like a Facebook in-feed sponsored video. It may be:
- Admin's own promotional video for the platform
- A business that paid for an in-feed video campaign and admin approved it

### DB table (listocean_db) — **NEEDS MIGRATION**
```sql
CREATE TABLE ad_videos (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id         BIGINT UNSIGNED NULL,           -- NULL = admin post
    video_url       VARCHAR(2000) NOT NULL,
    thumbnail_url   VARCHAR(2000) NULL,
    caption         TEXT NULL,
    cta_text        VARCHAR(255) NULL,              -- e.g. "Shop Now"
    cta_url         VARCHAR(2000) NULL,
    is_sponsored    TINYINT(1) NOT NULL DEFAULT 0,
    is_approved     TINYINT(1) NOT NULL DEFAULT 0,
    is_rejected     TINYINT(1) NOT NULL DEFAULT 0,
    reject_reason   TEXT NULL,
    start_at        TIMESTAMP NULL,
    end_at          TIMESTAMP NULL,
    view_count      INT UNSIGNED NOT NULL DEFAULT 0,
    created_at      TIMESTAMP NULL,
    updated_at      TIMESTAMP NULL
);
```

### Frontend feed injection
Every Nth item in the main listing feed or homepage feed injects an approved promo video:
```
listings[0] listings[1] listings[2] listings[3] listings[4]
→ [AD_VIDEO SPONSORED]
listings[5] listings[6] listings[7] ...
```
N is configurable (default: every 8 listings). The frontend compositor queries:
```php
$promoVideo = DB::connection('listocean')->table('ad_videos')
    ->where('is_approved', 1)->where('is_rejected', 0)
    ->where(fn($q) => $q->whereNull('end_at')->orWhere('end_at', '>=', now()))
    ->inRandomOrder()->first();
```

---

## 7. Seller Video Reels (Video Moderation)

**Status: 🔶 Admin done · Frontend upload + reels feed pending**

### Admin URL
`admin/video-moderation`

### What the admin can do
- View all listings that have a `video_url` attached (pending / approved / all)
- **Approve** a video → `listings.video_is_approved = 1` → video appears in reels feed
- **Reject** a video (sets `video_is_approved = 0`)
- **Edit** a video (reassign URL or upload a replacement file)
- **Add** a video to an existing listing directly (from admin, without seller upload)
- **Remove** a video from a listing

### Key distinction from Promo Video Ads
| | Seller Video Reels | Promo Video Ads |
|-|-------------------|-----------------|
| Who posts | Seller (attached to their listing) | Admin or paid advertiser |
| Purpose | Show listing product in action | Sponsored brand/campaign ad |
| DB location | `listings.video_url` + `listings.video_is_approved` | `ad_videos` table (separate) |
| Appears in feed? | Yes — organic reel | Yes — paid/sponsored reel |

### DB columns (listocean_db — `listings` table)
| Column | Purpose |
|--------|---------|
| `video_url` | URL/path of the uploaded video |
| `video_is_approved` | 0 = pending admin review, 1 = approved for reels feed |

### Frontend gap
1. **Upload form on listing create/edit** — seller attaches a video file to their listing
2. **Reels feed page** (`/reels`) — vertical scrollable feed pulling listings where `video_url IS NOT NULL AND video_is_approved = 1`
3. **Video player component** in the reels feed
4. **Like / share count** on reels
5. **"View listing" CTA** on each reel card

---

## 8. Reel Ad Placements

**Status: 🔶 Admin done · DB table pending · Frontend compositor pending**

### Admin URL
`admin/content/reel-ad-placements`

### What the admin can do
- View all active reel ad placement rules
- **Create** a placement: link an `advertisement_id` (or a `reel_id`) to a specific position in the reels feed with an overlay type
- **Update** placement settings
- **Delete** a placement

### How reel ad placements work
When a user scrolls the reels feed, at defined positions (slot numbers) the compositor injects a sponsored overlay or a full sponsored reel instead of an organic one.

Two supported placement types:
| `reel_type` | What gets inserted |
|-------------|-------------------|
| `listing` | A featured listing's video at this slot position |
| `ad_video` | A full promo video ad at this slot position |

Two overlay styles:
| `placement` | How it renders |
|-------------|---------------|
| `bottom_overlay` | Sponsor card overlaid on bottom of reel |
| `bottom_overlay_2` | Alternate sponsor overlay style |

### DB table (listocean_db) — **NEEDS MIGRATION**
```sql
CREATE TABLE reel_ad_placements (
    id                BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    advertisement_id  BIGINT UNSIGNED NULL,       -- links to advertisements.id
    reel_type         VARCHAR(50) NOT NULL,        -- 'listing' | 'ad_video'
    reel_id           BIGINT UNSIGNED NULL,        -- listing.id or ad_videos.id
    placement         VARCHAR(50) NOT NULL,        -- 'bottom_overlay' | 'bottom_overlay_2'
    slot_position     INT UNSIGNED NULL,           -- Nth position in feed (e.g. 5 = every 5th reel)
    is_active         TINYINT(1) NOT NULL DEFAULT 1,
    starts_at         TIMESTAMP NULL,
    ends_at           TIMESTAMP NULL,
    created_at        TIMESTAMP NULL,
    updated_at        TIMESTAMP NULL
);
```

### Frontend gap
The reels feed compositor needs to:
1. Load organic reels (approved listing videos) 
2. Query `reel_ad_placements` for active placements sorted by `slot_position`
3. At each `slot_position`, splice in the corresponding `listing` or `ad_video` reel with its overlay style

---

## 9. Advertiser Portal

**Status: ⚠️ Scaffold only — views exist but no real data persistence**

### Admin URL
`admin/advertiser-portal`

### Current state
The `AdvertiserPortalController` has:
- `index()` → renders `admin.advertiser-portal.index` (view exists)
- `create()` → renders `admin.advertiser-portal.create` (view exists)
- `store()` → validates `name`, `budget`, `target_categories` — **then does nothing** (comment: "Minimal scaffold: in future, create records and charge wallets")
- `purchases()` → renders `admin.advertiser-portal.purchases` (view exists)

### What needs to be built (full implementation)
1. **Advertiser account model** — `advertisers` table or use `users` table with `role = advertiser`
2. **Campaign model** — `ad_campaigns` table with budget, status, targeting, dates
3. **Budget deduction** — link to wallet system: campaign creation deducts from advertiser's wallet
4. **Campaign → Banner link** — campaigns own `advertisements` records that go through the existing banner request flow
5. **Performance reporting** — `ad_impressions` table tracks views per ad per day; `ad_clicks` for CTR

---

## 10. Escrow & Dispute Resolution

**Status: 🔶 Admin fully done · Frontend checkout + user escrow flow pending**

### Admin URL
`admin/escrow`

### What the admin can do

#### Escrow List (`/admin/escrow`)
- View all transactions with status filters: **All** / **Payment Pending** / **Funded** / **Seller Accepted** / **Seller Delivered** / **Released** / **Refunded** / **Disputed**
- Search by transaction ID, listing title, or payment gateway reference
- See buyer name, seller name, listing, amount, commission taken, timestamps

#### Escrow Detail (`/admin/escrow/{id}`)
- Full transaction record including deadlines and timeline events
- All state transitions logged in `escrow_events` table (who did what, when)

#### Admin Actions
| Action | Method | Effect |
|--------|--------|--------|
| `adminRelease` | POST `/admin/escrow/{id}/release` | Force-releases funds to seller. Used when buyer is unresponsive past deadline |
| `adminRefund` | POST `/admin/escrow/{id}/refund` | Refunds buyer. Used in dispute resolution favouring buyer |
| `adminDispute` | POST `/admin/escrow/{id}/dispute` | Opens/manages a dispute case, records admin notes |

#### Escrow Settings (`/admin/escrow/settings`)
Admin configures:
| Setting | Key | Example |
|---------|-----|---------|
| Platform fee % | `escrow_fee_percent` | `3.5` |
| Auto-release days | `escrow_auto_release_days` | `7` days after seller marks delivered |
| Seller accept deadline | `escrow_seller_accept_days` | `3` days from funding |
| Min transaction amount | `escrow_min_amount` | `10.00` |
| Enabled toggle | `escrow_enabled` | `1` / `0` |

### DB tables (listocean_db) — **NEED MIGRATIONS**
```sql
-- Main transaction record
CREATE TABLE escrow_transactions (
    id                        BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    listing_id                BIGINT UNSIGNED NOT NULL,
    buyer_user_id             BIGINT UNSIGNED NOT NULL,
    seller_user_id            BIGINT UNSIGNED NOT NULL,
    listing_price             DECIMAL(15,2) NOT NULL,
    admin_fee_amount          DECIMAL(15,2) NOT NULL DEFAULT 0,
    total_amount              DECIMAL(15,2) NOT NULL,
    currency                  VARCHAR(10) NOT NULL DEFAULT 'GHS',
    status                    ENUM(
                                  'payment_pending','funded','seller_confirmed',
                                  'seller_delivered','released','refunded','disputed'
                              ) NOT NULL DEFAULT 'payment_pending',
    payment_gateway           VARCHAR(100) NULL,
    payment_transaction_id    VARCHAR(255) NULL,
    funded_at                 TIMESTAMP NULL,
    seller_accepted_at        TIMESTAMP NULL,
    seller_delivered_at       TIMESTAMP NULL,
    buyer_confirmed_at        TIMESTAMP NULL,
    released_at               TIMESTAMP NULL,
    buyer_confirm_deadline_at TIMESTAMP NULL,
    seller_accept_deadline_at TIMESTAMP NULL,
    admin_note                TEXT NULL,
    created_at                TIMESTAMP NULL,
    updated_at                TIMESTAMP NULL
);

-- Audit timeline for every event
CREATE TABLE escrow_events (
    id                      BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    escrow_transaction_id   BIGINT UNSIGNED NOT NULL,
    event                   VARCHAR(100) NOT NULL,    -- e.g. 'funded', 'seller_accepted', 'disputed'
    actor_type              VARCHAR(50) NOT NULL,     -- 'buyer' | 'seller' | 'admin' | 'system'
    actor_user_id           BIGINT UNSIGNED NULL,
    from_status             VARCHAR(50) NULL,
    to_status               VARCHAR(50) NULL,
    note                    TEXT NULL,
    created_at              TIMESTAMP NULL
);
```

### Escrow lifecycle — full state machine
```
[Buyer initiates checkout]
  → status: payment_pending
    ↓ Buyer completes payment (gateway callback)
  → status: funded  (seller_accept_deadline_at = now + escrow_seller_accept_days)
    ↓ Seller accepts order
  → status: seller_confirmed
    ↓ Seller marks as delivered
  → status: seller_delivered  (buyer_confirm_deadline_at = now + escrow_auto_release_days)
    ↓ Buyer confirms receipt   OR   auto-release after deadline
  → status: released  →  wallet_histories: seller credited (amount - commission)
                       →  wallet_histories: commission credited to platform
    ↓ Buyer raises dispute (before or after delivered)
  → status: disputed
    ↓ Admin reviews, decides
  → status: released (seller wins)  OR  status: refunded (buyer wins)
    ↓ If refunded  →  wallet_histories: buyer credited full amount
```

### Frontend gap (all must be built)
1. **"Buy with Escrow" button** on listing detail page
2. **Escrow checkout page** — shows price breakdown: listing price + platform fee = total
3. **Payment gateway redirect** — after payment confirmed → `escrow_transactions.status = 'funded'`
4. **Seller: "Accept Order"** button in seller dashboard → `status = seller_confirmed`
5. **Seller: "Mark as Delivered"** button — with optional delivery proof upload → `status = seller_delivered`
6. **Buyer: "Confirm Receipt"** button → `status = released` → triggers wallet credit to seller
7. **Buyer: "Raise Dispute"** button — with description/evidence upload → `status = disputed`
8. **Admin auto-release cron** — scheduled command: set `released` where `buyer_confirm_deadline_at < NOW()` and `status = seller_delivered`
9. **Notifications at every step** — buyer and seller notified on each status change

---

## 11. Wallet

**Status: 🔶 Admin done · Frontend top-up + spend wiring pending**

### Admin URL
`admin/customer-web-wallet`

### What the admin can do
- **Search** users by name/email/username
- **View** a user's current wallet balance and full transaction ledger (`wallet_histories`)
- **Manually adjust** wallet balance (`adjust()` endpoint) — credit or debit with a note

### DB tables (listocean_db) — **NEED MIGRATIONS**
```sql
CREATE TABLE wallets (
    id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id     BIGINT UNSIGNED NOT NULL UNIQUE,
    balance     DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    currency    VARCHAR(10) NOT NULL DEFAULT 'GHS',
    updated_at  TIMESTAMP NULL
);

CREATE TABLE wallet_histories (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id         BIGINT UNSIGNED NOT NULL,
    type            ENUM('credit','debit') NOT NULL,
    amount          DECIMAL(15,2) NOT NULL,
    balance_after   DECIMAL(15,2) NOT NULL,
    reference_type  VARCHAR(100) NULL,    -- 'topup' | 'featured_purchase' | 'boost' | 'escrow_release' | 'commission' | 'admin_adjust'
    reference_id    BIGINT UNSIGNED NULL, -- ID of the related record
    note            TEXT NULL,
    created_at      TIMESTAMP NULL
);
```

### All wallet credit/debit events (frontend must trigger these)
| Event | Direction | reference_type |
|-------|-----------|---------------|
| User tops up via payment gateway | CREDIT | `topup` |
| User buys a featured ad package | DEBIT | `featured_purchase` |
| User boosts a listing | DEBIT | `boost_purchase` |
| User pays for membership | DEBIT | `membership_subscription` |
| Escrow releases to seller | CREDIT | `escrow_release` |
| Commission deducted from release | DEBIT | `commission` |
| Buyer refunded from escrow | CREDIT | `escrow_refund` |
| Admin manually credits user | CREDIT | `admin_adjust` |
| Admin manually debits user | DEBIT | `admin_adjust` |

### Frontend gap
1. Wallet top-up page — select gateway → pay → credit wallet
2. Wallet balance display in user dashboard header/sidebar
3. Transaction history page (`/user/wallet`)
4. Wallet deduction at point of every purchase (featured ads, boost, membership)

---

## 12. Commission Rules

**Status: ✅ Admin fully done · Deduction logic at escrow release pending**

### Admin URL
`admin/commission-rules`

### What the admin can do
- Create commission rules: global default, per-category override, per-membership-tier override
- Rules: type (percentage/flat), value, applies_to (category_id or tier)
- Edit or delete rules

### DB tables (sellupnow_admin)
| Table | Key columns |
|-------|------------|
| `commission_rules` | `id`, `type` (global/category/tier), `applies_to_id`, `commission_type` (percent/flat), `value`, `is_active` |

### How commission deduction must work (frontend/backend)
At `adminRelease()` or auto-release, the escrow controller must:
```
1. Fetch applicable commission rule (most specific wins: category > tier > global)
2. Calculate: commission_amount = listing_price × (rule.value / 100)   [if percent]
                                OR listing_price - rule.value             [if flat]
3. Seller receives: listing_price - commission_amount
4. Write wallet_histories: CREDIT seller with (listing_price - commission_amount)
5. Write wallet_histories: CREDIT platform account with commission_amount
6. Write escrow_transactions.admin_fee_amount = commission_amount
```

---

## 13. Membership Plans & Features

**Status: 🔶 Admin done · Frontend subscription flow pending**

### Admin URL
`admin/membership-plans` · `admin/membership-features`

### What the admin can do
- Create / edit / delete membership plans (name, price, billing period: monthly/yearly, listing quota, active toggle)
- Create feature flags for each plan (e.g. "Video reels", "Auto-feature 3 listings", "Verified badge")
- Delete plans

### DB tables (listocean_db)
| Table | Key columns |
|-------|------------|
| `membership_plans` | `id`, `name`, `price`, `billing_period` (monthly/yearly), `listing_quota`, `auto_feature_count`, `is_active`, `badge_label` |
| `membership_features` | `id`, `plan_id`, `feature_key`, `feature_label`, `value` |
| `user_memberships` | `id`, `user_id`, `plan_id`, `started_at`, `expires_at`, `status` (active/cancelled/expired) |

### Frontend gap
1. Membership plans page (`/user/my-membership`) — display plan comparison table
2. Subscribe button → wallet deduction → `user_memberships` record created
3. Quota enforcement on listing post — check `user_memberships` active plan quota vs listings posted this period
4. Badge display on profile/listings for premium members
5. Scheduled command — mark `user_memberships.status = expired` when `expires_at < NOW()`, downgrade user to free

---

## 14. KYC / Identity Verification

**Status: ✅ Admin fully done · Email notification pending**

### Admin URL
`admin/identity-verification`

Full workflow documented in `README.md`. Admin can: view queue, show detail with documents + selfie, approve, decline with reason. Audit trail written to `identity_verification_audits`.

### Only remaining gap
- **Email to user** on approval: "Your identity has been verified ✓"
- **Email to user** on decline: "Your verification was declined. Reason: [X]. Please re-submit corrected documents."
- These should also fire an in-app notification (write to `notifications` table)

---

## 15. Other Admin Services

These are complete admin-side implementations with their frontend status noted:

| Service | Admin URL | Admin Status | Frontend Status |
|---------|-----------|-------------|----------------|
| Blog management | `admin/blog` | ✅ CRUD | 🔶 `/blog` page needs rendering |
| Site notices/alerts | `admin/site-notices` | ✅ CRUD | 🔶 Banner rendering on frontend |
| Flash sale widget | `admin/flash-sale` | ✅ CRUD + timer | 🔶 Homepage countdown widget |
| Homepage hero | `admin/homepage-hero` | ✅ | 🔶 Frontend hero component |
| Site advertisements (generic) | `admin/site-advertisements` | ✅ CRUD | 🔶 Slot rendering per position |
| Customer push notifications | `admin/customer-notification` | ✅ Broadcast | 🔶 Firebase FCM event hooks |
| Chat oversight | `admin/chat-oversight` | ✅ Read-only | ✅ Chat exists |
| Listing reports moderation | `admin/listing-report` | ✅ | 🔶 Report button on frontend listing |
| Reviews moderation | `admin/review` | ✅ | 🔶 Review submission form |
| Language management | `admin/language` | ✅ | 🔶 Locale switcher on frontend |
| Legal pages (T&C, Privacy) | `admin/legal-page` | ✅ | ✅ Links in sidebar |
| Contact us messages | `admin/contact-us` | ✅ | 🔶 Contact form wiring |
| Page builder | `admin/page-builders` | ✅ | 🔶 Dynamic page rendering |
| Mail configuration | `admin/mail-config` | ✅ DB-driven | ✅ CustomConfigServiceProvider |
| Payment gateways | `admin/payment-gateway` | ✅ Config | 🔶 Checkout integration |
| Social auth settings | `admin/social-auth` | ✅ | 🔶 Login with Google/Facebook |
| Map settings | `admin/map-settings` | ✅ | 🔶 Map view on browse page |
| Employee/sub-admin | `admin/employee` | ✅ | N/A (admin only) |
| Roles & permissions | `admin/role` | ✅ | N/A (admin only) |

---

## 16. DB Tables — ✅ All Migrated (2026-02-28)

> **Status: COMPLETE.** All 13 tables were created on 2026-02-28.
> Column names were also aligned with the admin controllers via `2026_02_28_000013_align_controller_column_names.php`.
> The admin panel now writes real data — no more empty-state fallbacks.

| Table | Migration file | Status |
|-------|---------------|--------|
| `wallets` | `2026_02_28_000001_create_wallets_table` | ✅ Done |
| `wallet_histories` | `2026_02_28_000002_create_wallet_histories_table` | ✅ Done |
| `escrow_transactions` | `2026_02_28_000003_create_escrow_transactions_table` | ✅ Done |
| `escrow_events` | `2026_02_28_000004_create_escrow_events_table` | ✅ Done |
| `ad_videos` | `2026_02_25_000001_create_ad_videos_table` | ✅ Done |
| `reel_ad_placements` | `2026_02_28_000005_create_reel_ad_placements_table` | ✅ Done |
| `featured_ad_packages` | `2026_02_28_000006_create_featured_ad_packages_table` | ✅ Done |
| `featured_ad_purchases` | `2026_02_28_000007_create_featured_ad_purchases_table` | ✅ Done |
| `featured_ad_activations` | `2026_02_28_000008_create_featured_ad_activations_table` | ✅ Done |
| `boosts` | `2026_02_28_000009_create_boosts_table` | ✅ Done |
| `membership_plans` | `2026_02_28_000010_create_membership_plans_table` | ✅ Done |
| `membership_features` | `2026_02_28_000011_create_membership_features_table` | ✅ Done |
| `user_memberships` | `2026_02_28_000012_create_user_memberships_table` | ✅ Done |

> **Next step:** Admin panel can now create membership plans, featured ad packages, and escrow settings without empty-state fallbacks. Frontend implementation follows the phased plan in [`IMPLEMENTATION-WORKFLOW.md`](IMPLEMENTATION-WORKFLOW.md).

---

## 17. Frontend Implementation Checklist

Ordered by revenue impact:

### Sprint 1 — Money In / Trust
- [x] **All 13 DB migrations** — created and ran successfully ✅
- [x] **Column alignment migration** — `membership_plans` + `featured_ad_packages` aligned with controllers ✅
- [ ] **WalletService** — `Services/WalletService.php` (credit/debit/balance methods)
- [ ] **Wallet top-up page** — `/user/wallet/topup` → payment gateway → credit wallet
- [ ] **Wallet balance in dashboard** — show current balance in sidebar
- [ ] **Membership plans created in admin** — follow `MEMBERSHIP-SYSTEM.md` §6 for 4-tier setup
- [ ] **Membership subscribe flow** — `MembershipController` + `MembershipService`
- [ ] **Featured ad package purchase** — seller selects package → wallet deducted → listing featured
- [ ] **Featured listings display** — homepage "Featured" + category page top

### Sprint 2 — Escrow (Trust & Higher Value Transactions)
- [x] **Escrow migrations** — `escrow_transactions` + `escrow_events` created ✅
- [ ] **"Buy with Escrow" on listing detail** — for high-value items
- [ ] **Escrow checkout page** — price + fee breakdown → payment
- [ ] **Seller dashboard: accept/deliver buttons** — `seller_confirmed` → `seller_delivered`
- [ ] **Buyer dashboard: confirm/dispute buttons** — `released` or `disputed`
- [ ] **Auto-release cron** — scheduled artisan command
- [ ] **Commission deduction** on release using commission rules

### Sprint 3 — Content & Engagement
- [ ] **Seller video upload on listing form** — attach `video_url` to listing
- [ ] **Video moderation notice to seller** — "We're reviewing your video"
- [ ] **Reels feed page** (`/reels`) — vertical scroll, approved listing videos
- [ ] **Reel ad placement injection** — every Nth reel = ad placement
- [ ] **Promo video migration** (`ad_videos`) + feed injection
- [ ] **Boost purchase** on listing — "Boost this ad for ₵X"
- [ ] **Feed ordering** — boosted listings bubble up in browse

### Sprint 4 — Advertising Stack
- [ ] **Banner slot components** — Blade partials at all 7 position keys
- [ ] **Advertiser submission page** — business submits banner request
- [ ] **Membership subscription flow** — plan selection → payment → quota enforcement
- [ ] **Review submission form** — after a transaction, buyer rates seller
