# SellUpNow — Platform Services, Business Logic & Workflow Reference

> **Purpose:** This document defines every service the platform offers, how each one works, who the actors are, where money flows, and what needs to be built. Use this as the single source of truth when implementing any feature.
>
> **Last updated:** 2026-02-28
> **Platform type:** Classified ads marketplace with layered promotional & trust services

---

## Platform Philosophy

SellUpNow is a **classified ad marketplace** — the core loop is:

```
Seller posts an ad → Buyer discovers it → Buyer contacts Seller → Deal happens
```

Everything else on the platform exists to either:
- **Accelerate that loop** (featured ads, boost, video content, membership)
- **Make it safer** (escrow, KYC, verified badges, reporting)
- **Monetize it** (ad packages, banners, commissions, promo videos)
- **Build trust & repeat usage** (reviews, notifications, wallets, blog)

---

## Service Map (All Services)

```
┌─ CORE (free) ──────────────────────────────────────────────────────────┐
│  1.  Free Classified Listings                                           │
│  2.  Browse & Search                                                    │
│  3.  Direct Messaging (Chat)                                            │
│  4.  User Reviews                                                       │
│  5.  Listing Reports (fraud/scam flagging)                              │
└─────────────────────────────────────────────────────────────────────────┘
┌─ SELLER PAID PROMOTIONS ────────────────────────────────────────────────┐
│  6.  Featured Ad Packages (pin listing to top of category/search)       │
│  7.  Boost (one-click re-push to top of feed)                           │
│  8.  Membership Plans (listing quota, auto-feature, badge perks)        │
│  9.  Seller Video Reels (short product videos in reels feed)            │
└─────────────────────────────────────────────────────────────────────────┘
┌─ BUSINESS / BRAND ADVERTISING ─────────────────────────────────────────┐
│  10. Banner Ads (image banners on defined page positions)               │
│  11. Promo Video Ads (admin-posted sponsored videos in feed)            │
│  12. Reel Ad Placements (video ad slots inside the reels feed)          │
│  13. Advertiser Portal (external businesses manage their campaigns)     │
│  14. Site Advertisements (generic ad slot management)                   │
│  15. Flash Sale Widgets (time-limited promotional banners)              │
└─────────────────────────────────────────────────────────────────────────┘
┌─ TRUST & SAFETY ────────────────────────────────────────────────────────┐
│  16. KYC / Identity Verification (document + selfie → verified badge)  │
│  17. Escrow & Dispute Resolution                                        │
│  18. Blocked Users                                                      │
│  19. Chat Oversight (admin monitoring)                                  │
└─────────────────────────────────────────────────────────────────────────┘
┌─ PLATFORM REVENUE MECHANICS ───────────────────────────────────────────┐
│  20. Commission Rules (platform cut on escrow transactions)             │
│  21. Wallet (user balance for buying services + receiving escrow)       │
│  22. Coupons & Discounts                                                │
│  23. Payment Gateways                                                   │
└─────────────────────────────────────────────────────────────────────────┘
┌─ ENGAGEMENT & CONTENT ─────────────────────────────────────────────────┐
│  24. Push Notifications (Firebase)                                      │
│  25. Admin Broadcasts (send to all / segment)                           │
│  26. Blog / Editorial Content                                           │
│  27. Site Notices / Announcements                                       │
│  28. Homepage Hero / Page Builder                                       │
└─────────────────────────────────────────────────────────────────────────┘
```

---

## Detailed Service Definitions

---

### 1. Free Classified Listings

**What it is:** Any registered user can post an ad/listing for free (subject to the quota their membership plan allows or platform default).

**Actors:** Seller (posts), Buyer (browses/contacts), Admin (moderates)

**Workflow:**
```
Seller fills listing form (title, category, description, price, photos, location)
  → Listing saved with status = pending (or active if auto-approve is on)
  → Admin reviews in Listing Moderation queue
  → Admin approves → Listing goes live, Seller notified
  → Admin rejects → Seller notified with reason
  → Buyer browses, clicks listing, sees detail page
  → Buyer taps "Contact Seller" → opens chat thread
```

**Key statuses:** `pending` · `active` · `rejected` · `sold` · `expired` · `deleted`

**Admin surface:** `admin/listing-moderation` (queues: new / updated / reported / removed)

**Implementation status:** Core listing flow ✅ · Moderation ✅ · Video on listing — needs wiring

---

### 2. Browse & Search

**What it is:** Public-facing discovery layer. No login required to browse.

**Features:**
- Category & subcategory drill-down
- Location filter (Country → State → City)
- Price range filter
- Keyword search
- Featured listings surface at top of results (paid placement)
- Map view of nearby listings (Google Maps integration)

**Admin surface:** `admin/map-settings` (Google Maps API key), `admin/site-country` / `site-state` / `site-city` (location data)

---

### 3. Direct Messaging (Chat)

**What it is:** Real-time buyer-seller messaging on a per-listing thread. Powered by Pusher.

**Workflow:**
```
Buyer clicks "Contact Seller" on listing
  → Unique chat thread created (listing_id + buyer_id + seller_id)
  → Messages sent in real time via Pusher
  → Both parties receive push notification (Firebase) on new message
  → Admin can view all threads via Chat Oversight (read-only monitoring)
```

**Trust layer:** Blocked users cannot initiate or receive messages from users they've blocked.

**Admin surface:** `admin/chat-oversight` (view threads), `admin/pusher` (Pusher credentials)

**Implementation status:** Chat ✅ · Block enforcement on chat — pending

---

### 4. User Reviews

**What it is:** After a transaction, buyers can leave a star rating + text review on the seller's profile. Builds seller reputation.

**Business logic:**
- Reviews display on seller's public profile
- Aggregate rating shown on listings
- Admin can moderate/remove abusive reviews
- Sellers cannot review themselves
- My Reviews page (`/user/my-reviews`) shows all reviews a user has received

**Admin surface:** `admin/review`

**Implementation status:** Admin side ✅ · Frontend My Reviews page ✅ (UI) · Review submission flow — needs wiring

---

### 5. Listing Reports (Fraud / Scam Flagging)

**What it is:** Any user can report a listing as fraudulent, misleading, prohibited, or a scam.

**Workflow:**
```
User clicks "Report this Ad" on listing detail
  → Selects reason (fraud / scam / prohibited / other)
  → Report created, listing flagged in admin queue
  → Admin reviews listing report
  → Admin can: approve (dismiss report), or remove listing + warn/ban user
```

**Admin surface:** `admin/listing-report`

---

### 6. Featured Ad Packages

**What it is:** Sellers pay a fixed fee to have their listing highlighted and pinned to the top of category/search results for a defined duration. This is the primary per-listing monetization mechanism.

**Business logic:**
- Admin defines packages: name, price, duration (days), listing slots per package
- Seller goes to listing detail → "Boost this Ad" / "Feature this Ad" → selects package → pays
- Money deducted from Wallet (or direct payment gateway)
- Listing gets `is_featured = 1` and `featured_until = [date]`
- Featured listings appear in the "Featured" section on homepage and at top of category pages
- Package expires after duration → listing returns to normal position

**Package fields (admin-managed):**
| Field | Example |
|-------|---------|
| Name | Gold Feature |
| Price | ₵ 25 |
| Duration | 30 days |
| Position | Top of category |
| Active | Yes / No |

**Admin surface:** `admin/featured-ad-packages` (CRUD) · `admin/featured-ad-reports` (performance data)

**Implementation status:** Admin CRUD ✅ · Frontend purchase flow — pending · Featured display on browse — pending

---

### 7. Boost

**What it is:** A lightweight, lower-cost push that re-timestamps a listing so it appears at the top of "recent" feeds without a full featured-ad package. Similar to Facebook Marketplace "Boost."

**Business logic:**
- Seller clicks "Boost" on their listing
- Flat fee deducted from wallet
- Listing `updated_at` or `boosted_at` re-set to now
- Feed ordering logic prioritizes boosted listings for a configurable window (e.g. 24–72 hours)
- Admin manages active boosts + can manually remove

**Admin surface:** `admin/boosts` (CRUD + list of active boosts)

**Implementation status:** Admin side ✅ · Frontend purchase + boost ordering — pending

---

### 8. Membership Plans

**What it is:** Subscription tiers that unlock higher listing quotas, automatic featuring, profile badges, and reduced fees. Think: Free → Silver → Gold → Platinum.

**Business logic:**
- Admin defines plans: name, price, billing period, listing quota per period, auto-feature slots, chat priority, badge label
- User subscribes via `/user/my-membership` → pays via wallet or payment gateway
- `users.membership_plan_id` updated, expiry stored
- Platform checks membership when user tries to post a listing (quota enforcement)
- Premium members get badge on profile + listings
- When membership expires → user drops to Free tier automatically

**Plan features matrix (example):**
| Feature | Free | Silver | Gold | Platinum |
|---------|------|--------|------|----------|
| Listings/month | 5 | 20 | 50 | Unlimited |
| Auto-featured slots | 0 | 1 | 3 | 5 |
| Video reels upload | ✗ | ✓ | ✓ | ✓ |
| Verified badge | ✗ | ✗ | ✓ | ✓ |

**Admin surface:** `admin/membership-plans` (plan CRUD) · `admin/membership-features` (feature flags)

**Implementation status:** Admin CRUD ✅ · Frontend membership flow — partial · Quota enforcement — pending

---

### 9. Seller Video Reels

**What it is:** Sellers can upload short videos (reels) showcasing their products/services. These appear in a dedicated Reels feed on the platform — similar to TikTok/Instagram Reels but for product promotion. This is a significant engagement driver.

**Business logic:**
- Seller uploads video on their listing or profile
- Video goes into moderation queue with `status = pending`
- Admin reviews: approve → video appears in reels feed · reject → seller notified
- Reels feed orders by: recency, engagement (likes/views), and whether seller has active membership
- Users can like/share reels; comments optional
- Admin can feature/sponsor specific reels (ListoceanReelAdPlacementController places paid video ads inside the reels feed)

**Where reels appear:**
1. Dedicated `/reels` feed page
2. On the poster's seller profile
3. On the associated listing detail page
4. Inside video ad placement slots (if admin chooses to feature it)

**Admin surface:** `admin/video-moderation` (approve/reject/feature/delete) · `admin/reel-ad-placements` (control ad slots in reels feed)

**Implementation status:** Admin moderation ✅ · Admin reel ad placement ✅ · Frontend reels feed — pending · Upload flow — pending

---

### 10. Banner Ads

**What it is:** Image-based advertising banners, similar to traditional display advertising. Businesses pay to place their banner at defined positions on the platform (homepage hero, category page top, listing detail sidebar, between search results, etc.).

**Two tracks:**

#### Track A — Admin-Managed Banners (`BannerController`)
Admin directly uploads and places banners without a user request. Used for:
- House ads (promote the platform's own services)
- Pre-sold banner campaigns where billing is handled offline
- Seasonal promotions

**Workflow:**
```
Admin → admin/banner → Create Banner
  → Upload image, set position, set start/end date, set link URL
  → Banner goes live immediately with status = active
  → Frontend renders banner at the configured position
  → Admin deactivates or deletes when campaign ends
```

#### Track B — Self-Serve Advertiser Requests (`BannerAdRequestsController`)
Businesses request a banner placement through the platform and await admin approval.

**Workflow:**
```
Advertiser submits banner request (image + desired position + campaign dates)
  → Request in admin queue with status = pending
  → Admin reviews: approve (banner goes live) or reject
  → Admin can deactivate a live banner at any time
```

**Banner placement positions (to be configured by admin):**
| Position Key | Where It Appears |
|-------------|-----------------|
| `homepage_hero` | Homepage hero/slider area |
| `homepage_mid` | Mid-page break on homepage |
| `category_top` | Top of category page |
| `listing_sidebar` | Sidebar of listing detail page |
| `search_banner` | Between search results |
| `reels_interstitial` | Full-screen between reels |
| `checkout_footer` | Bottom of checkout flow |

**Admin surface:** `admin/banner` (direct banners) · `admin/banner-ad-requests` (approve/deactivate advertiser requests)

**Implementation status:** Admin banners ✅ · Ad requests ✅ · Frontend banner rendering — needs verification per position

---

### 11. Promo Video Ads

**What it is:** Admin posts sponsored/promotional videos that appear in the main content feed. These are distinct from user-uploaded reels — they are platform-controlled ad placements, functioning like Facebook's in-feed video ads.

**Business logic:**
- Admin uploads video + thumbnail + CTA link + target URL + campaign dates
- Video ad appears in the listing feed or homepage feed at configured intervals (e.g. every 8th listing = a promo video)
- Admin controls active/inactive status + view cap (stop serving after N views)
- Click-through tracked, view counts reported

**Use cases:**
- Platform promoting a brand's new product
- Seasonal campaign (e.g. Christmas sale)
- Cross-promoting platform features to users

**Admin surface:** `admin/promo-video-ads` (CRUD)

**Implementation status:** Admin CRUD ✅ · Frontend feed injection — pending

---

### 12. Reel Ad Placements

**What it is:** Admin controls the specific slots inside the reels feed where paid video ads (separate from user reels) are injected. Every Nth reel position can be a paid placement instead of an organic user reel.

**Business logic:**
- Admin creates a reel ad placement: position number, video file, target URL, campaign period
- When a user scrolls the reels feed, the feed compositor checks placement rules and injects the ad reel at the configured slot
- Multiple advertisers can book different slot positions
- Admin can pause, update, or delete placements

**Admin surface:** `admin/reel-ad-placements` (CRUD + active/inactive toggle)

**Implementation status:** Admin CRUD ✅ · Frontend feed compositor — pending

---

### 13. Advertiser Portal

**What it is:** A self-serve section where registered businesses/advertisers can manage their own ad campaigns — submit banner requests, upload video ads, view performance metrics — without going through admin manually for every action.

**Business logic:**
- Businesses register as Advertiser accounts (separate role from regular seller)
- Advertiser can: create campaigns, upload creatives, set budgets, select positions, view impressions/clicks
- Admin still approves creatives before they go live (brand safety)
- Billing: pre-pay credits into advertiser wallet, deducted as impressions are served

**Admin surface:** `admin/advertiser-portal` (manage advertiser accounts + campaigns)

**Implementation status:** Admin side ✅ · Full advertiser self-serve portal — pending

---

### 14. Site Advertisements

**What it is:** A generic catch-all advertisement slot system — admin can inject any HTML/image/script ad unit into configurable positions on the customer-facing site. Useful for third-party ad networks (Google AdSense, etc.) or arbitrary promotional blocks.

**Admin surface:** `admin/site-advertisements`

**Implementation status:** Admin ✅ · Frontend slot rendering — needs verification

---

### 15. Flash Sale Widgets

**What it is:** Admin creates a time-boxed promotional highlight widget that displays on the homepage — a countdown timer with a curated selection of discounted/featured listings. Drives urgency.

**Business logic:**
- Admin sets: sale name, start datetime, end datetime, listing IDs to feature
- Widget renders on homepage with live countdown
- When timer hits zero, widget disappears and listings return to normal state
- Multiple flash sales can be scheduled (non-overlapping)

**Admin surface:** `admin/flash-sale`

**Implementation status:** Admin ✅ · Frontend widget rendering — needs verification

---

### 16. KYC / Identity Verification

**What it is:** Users submit government ID documents + a selfie to receive a verified badge on their profile and listings. Builds buyer trust in sellers.

**Workflow:**
```
User → /user/identity-verification → Upload:
  - Identification type (National ID / Passport / Driver's License)
  - ID number
  - Front of document (photo)
  - Back of document (photo)
  - Selfie holding the document

→ Submitted: identity_verifications.status = 0 (pending)
→ Admin queue: admin/identity-verification (shows pending + declined queue)
→ Admin reviews documents + selfie

→ APPROVE:
    identity_verifications.status = 1
    users.verified_status = 1
    Audit log written
    Email sent to user: "Your identity has been verified"
    Verified badge appears on profile + all their listings

→ DECLINE:
    identity_verifications.status = 2
    decline_reason stored
    Audit log written
    Email sent to user: "Verification declined — reason: [X]"
    User can correct documents and re-submit
```

**DB tables:** `identity_verifications` · `identity_verification_audits`

**Admin surface:** `admin/identity-verification` (queue → show → approve/decline)

**Implementation status:** Admin fully done ✅ · Frontend submission form ✅ · Email on approve/decline — pending

---

### 17. Escrow & Dispute Resolution

**What it is:** A payment protection layer for high-value transactions. Buyer's money is held by the platform until the buyer confirms receipt — protecting both parties. This is what distinguishes SellUpNow from pure classifieds.

**Workflow:**
```
Buyer agrees to buy → chooses Escrow checkout
  → Buyer pays into platform escrow wallet (held, not released yet)
  → Seller is notified: "Payment secured. Ship/deliver the item."
  → Seller delivers item
  → Buyer confirms receipt → funds released to Seller's wallet
  → OR: Buyer raises dispute (item not as described / not received)
      → Dispute enters admin review
      → Admin reviews evidence from both parties
      → Admin decision: Release to Seller | Refund to Buyer | Split
  → Platform deducts commission before releasing to Seller
```

**Escrow states:** `held` → `in_transit` → `delivered` → `released` | `disputed` → `resolved`

**Admin actions:**
- `adminRelease()` — force-release funds to seller
- `adminRefund()` — refund buyer
- `adminDispute()` — open/manage dispute case
- `settings()` — configure escrow fee %, minimum transaction amount, auto-release timer (days after delivery with no dispute)

**Admin surface:** `admin/escrow` (list + show + release/refund/dispute) · escrow settings

**Implementation status:** Admin fully done ✅ · Frontend checkout escrow flow — pending · Dispute UI for user — pending

---

### 18. Blocked Users

**What it is:** Users can block other users to prevent messages, profile views in their feed, and contact attempts.

**Business logic:**
- `blocked_users` table: `blocker_id`, `blocked_user_id`
- Blocked users do not appear in the blocker's feed or search results (when enforced in queries)
- Blocked users cannot initiate a chat with the blocker
- Block/Unblock managed from `/user/blocked-users` page

**Implementation status:** DB + model + controller + UI ✅ · Feed/chat enforcement — pending

---

### 19. Chat Oversight

**What it is:** Admin-only read-only monitoring of user chat threads. Used for safety investigations (fraud, harassment, scam coordination).

**Business logic:**
- No admin injection into chats (read-only)
- Admin can search by user or listing
- Can link to a listing report or user ban from the oversight view

**Admin surface:** `admin/chat-oversight`

---

### 20. Commission Rules

**What it is:** The platform takes a percentage or flat fee on transactions processed through escrow. Commission rules define when and how much to take.

**Rule types:**
- **Global default** — flat % on all escrow transactions
- **Category-specific** — different % per listing category (e.g. electronics vs clothes)
- **Seller tier** — premium members pay lower commission

**Business logic:**
- At escrow release: commission deducted before funds hit seller's wallet
- Commission credited to platform's own balance (reportable in admin dashboard)
- Admin can create multiple rules; most specific rule wins

**Admin surface:** `admin/commission-rules` (CRUD)

**Implementation status:** Admin CRUD ✅ · Deduction logic at release — needs wiring

---

### 21. Wallet

**What it is:** Every registered user has an internal platform wallet. Used to pay for featured ads, boost, membership, and to receive escrow payouts.

**Wallet operations:**
| Operation | Direction | Trigger |
|-----------|-----------|---------|
| Top-up | IN | User pays via payment gateway |
| Featured ad purchase | OUT | User buys featured package |
| Boost purchase | OUT | User boosts listing |
| Membership subscription | OUT | Recurring subscription charge |
| Escrow release | IN | Admin or auto-release after delivery |
| Commission deduction | OUT | Auto at escrow release |
| Refund | IN | Admin issues refund from escrow |
| Admin credit | IN | Admin manually credits user |

**Admin surface:** `admin/customer-web-wallet` (view balances, manual credit/debit, transaction history)

**Implementation status:** Admin ✅ · Frontend wallet top-up + spend — needs wiring end to end

---

### 22. Coupons & Discounts

**What it is:** Promotional codes that reduce the price of paid services (featured packages, membership, boost).

**Business logic:**
- Admin creates coupon: code, discount type (% or flat), max uses, expiry
- User enters code at checkout → discount applied before wallet deduction
- Admin tracks usage per coupon

**Admin surface:** `admin/coupon` (CRUD)

---

### 23. Payment Gateways

**What it is:** The payment processing layer supporting wallet top-ups and direct purchases. Multiple gateways configured by admin.

**Supported gateways (configured in admin):** Stripe · Razorpay · CashFree · In-App Purchase (mobile)

**Business logic:**
- User selects gateway at checkout → redirected to gateway → on success → wallet credited or service activated
- All gateway credentials configured via `admin/payment-gateway` (not in `.env`)
- Mobile payments use Razorpay/CashFree Flutter SDK + In-App Purchase for app store compliance

**Admin surface:** `admin/payment-gateway`

---

### 24. Push Notifications (Firebase)

**What it is:** Real-time mobile push notifications via Firebase Cloud Messaging (FCM). Web push via browser PWA.

**Events that trigger a push:**
| Event | Recipient | Message |
|-------|-----------|---------|
| New chat message | Recipient user | "New message from [Seller]" |
| Listing approved | Seller | "Your ad is now live" |
| Listing rejected | Seller | "Your ad was not approved" |
| KYC approved | User | "Your identity has been verified" |
| KYC declined | User | "Verification needs attention" |
| Escrow released | Seller | "Payment of ₵X released to your wallet" |
| New review | Seller | "[Buyer] left you a review" |
| Flash sale starts | All subscribed users | "Flash Sale is live!" |
| Admin broadcast | Segment / All | Custom message |

**Admin surface:** `admin/firebase` (FCM config) · `admin/customer-notification` (manual push broadcast)

**Implementation status:** Firebase config ✅ · Event-triggered notifications — partially wired · Admin broadcast ✅

---

### 25. Admin Broadcasts

**What it is:** Admin can send custom push notifications and in-app notifications to all users or a defined segment (e.g. users in a city, premium members only).

**Admin surface:** `admin/customer-notification`

---

### 26. Blog / Editorial Content

**What it is:** Admin publishes articles, guides, and news. Drives SEO and positions the platform as a trusted buying/selling resource (e.g. "How to spot a scam ad", "Best practices for selling electronics").

**Admin surface:** `admin/blog`

**Frontend:** `/blog` → article listing → `/blog/[slug]`

---

### 27. Site Notices / Announcements

**What it is:** Admin publishes short banners/announcements that display site-wide or on specific pages (e.g. "Scheduled maintenance on Sunday", "New payment gateway added").

**Admin surface:** `admin/site-notices`

---

### 28. Homepage Hero / Page Builder

**What it is:** Admin controls the hero section of the homepage (ListoceanHomepageHeroController) and can use the page builder (ListoceanPageBuildersController) to construct custom page layouts without code.

**Admin surface:** `admin/homepage-hero` · `admin/page-builders` · `admin/page-settings`

---

## Revenue Streams Summary

| Revenue Stream | Type | Mechanism |
|---------------|------|-----------|
| Featured Ad Packages | Per-listing, one-time | Seller pays to feature a single listing |
| Boost | Per-listing, one-time | Seller pays to push listing to top |
| Membership Plans | Subscription, recurring | Monthly/yearly subscription |
| Banner Ads (Track A) | Direct sale, campaign | Negotiated with admin |
| Banner Ads (Track B) | Self-serve | Advertiser pays per request |
| Promo Video Ads | Campaign | Rate card × views / duration |
| Reel Ad Placements | Slot rental | Per slot × duration |
| Advertiser Portal | Campaign management fee | % of ad spend or flat booking fee |
| Escrow Commission | Transaction % | Deducted at fund release |

---

## Page Placement Map (Where Services Appear on Frontend)

| Page | Services Rendered |
|------|------------------|
| Homepage | Hero banner · Flash sale widget · Featured listings · Promo video ad · Site advertisements |
| Category listing page | Featured listings at top · Banner (category_top) · Boosted listings |
| Search results page | Featured listings interspersed · Banner (search_banner) |
| Listing detail page | Sidebar banner (listing_sidebar) · Related featured listings |
| Reels feed | User reels · Reel ad placements (every Nth) · Promo video ads |
| User profile page | Verified badge · Review aggregate · Member tier badge |
| Checkout / Wallet top-up | Coupon input · Payment gateway selection |
| Dashboard sidebar | Membership status · Wallet balance |

---

## Implementation Priority Matrix

### Phase 1 — Complete Core Revenue Loop
These must work before any marketing or launch:

1. **Featured Ad Package purchase** — frontend `/listing/{id}/feature` → select package → pay from wallet → listing featured
2. **Escrow checkout** — buyer selects escrow on listing → pays → seller delivers → buyer confirms → release
3. **Commission deduction** — at escrow release, auto-deduct per commission rules, credit platform balance
4. **Wallet top-up** — user adds funds via payment gateway → wallet credited
5. **Membership subscription** — user subscribes → quota enforced on listing creation
6. **Email notifications** — KYC approved/declined · listing approved/rejected · escrow events

### Phase 2 — Engagement & Trust
7. **Reels feed** — `/reels` page · upload flow · moderation queue (admin already done)
8. **Block enforcement** — filter blocked users from chat, feed, search
9. **Review submission** — buyers can leave reviews after transactions
10. **Push notifications** — wire all events from table above to FCM

### Phase 3 — Full Advertising Stack
11. **Banner positions enforced** — all `position` keys rendering correct Blade slots
12. **Promo video in feed** — inject every Nth position in listing/reels feed
13. **Reel ad placement compositor** — inject ad reels at configured slot numbers
14. **Advertiser self-serve portal** — frontend for advertisers to manage campaigns

### Phase 4 — Production Hardening
15. `APP_ENV=production`, `APP_DEBUG=false`, real domain, HTTPS
16. Queue worker for async jobs (notifications, escrow timers, commission calculations)
17. Scheduled commands: expire featured ads · expire memberships · auto-release escrow after N days
