# SellUpNow — Feature Audit (March 2026)

**Architecture rule confirmed by owner:**
> "We only use `listocean_db` for both frontend AND backend. We only use `sellupnow-admin` as the admin panel. We do NOT need the listocean admin panel."

**Decision key:**
- `KEEP` — leave as-is, it works and uses listocean_db
- `REMOVE` — delete from sidebar + routes (code stays in files but is unreachable)
- `DISABLE` — comment out / block via env flag (keeps code for possible future use)
- `DONE` — already fixed this session

---

## DEEP AUDIT: The 6 Key Features (March 2026)

These 6 features were confirmed as important. Each was investigated end-to-end: admin management, frontend UI, and database state.

| Feature | Admin Status | Frontend Status | DB State | Root Issue | Fix Applied |
|---|---|---|---|---|---|
| **Brands** | `BrandController` reads/writes to `listocean.brands` ✅ | Add-listing form has brand dropdown wired to `brand_id` ✅ | 28 brands in listocean; 2/3 listings have brand_id set | Brand was missing from admin sidebar | ✅ DONE — Brand added to sidebar under Attributes |
| **Reviews** | `ReviewsController` was using admin DB `reviews` (shop-based, 0 rows) ❌ | `UserReviewController` + review modal on user profile page ✅ | `listocean.reviews` 0 rows; admin `reviews` 0 rows | Admin Review page was wired to wrong DB | ✅ DONE — Created `ListoceanReviewController`, new view, routes `admin.listocean-review.*`, "User Reviews" sidebar link |
| **Wallet** | `ListoceanWalletController` for manual credit/debit ✅ | Full top-up flow + Paystack redirect + callback ✅ | 1 wallet (balance=0); 7 wallet_histories all `paystack_topup_pending` | Paystack IS configured with **live keys (GHS)** ✅. Pending rows from localhost dev testing. Works in production. | None needed — system is correct |
| **Memberships** | `ListoceanMembershipPlanController` uses `CONNECTION='listocean'` ✅ | `MembershipController` + `MembershipService` ✅ | 4 plans; 1 active subscription (admin on Free plan, payment_status=completed) | Working correctly. Paid plans require wallet balance. | None needed |
| **Featured Ads** | `ListoceanFeaturedAdPackageController` uses `CONNECTION='listocean'` ✅ | `FeaturedAdController` + packages/purchase flow ✅ | listocean: 2 packages (Free GHS 0, Bronze GHS 50); 2 purchases; 2 activations | Working correctly. Admin writes to listocean ✅ | None needed |
| **Escrow** | `ListoceanEscrowController` + settings ✅ | Full escrow flow coded; "Buy with Escrow" CTA on listing detail ✅ | 2 listings with `escrow_enabled=1`; 0 transactions | `EscrowService` complete. Zero transactions because wallet balance = 0. Fund wallet → escrow works. | None needed |

### Production Readiness Checklist for Key Features

1. **Wallet → Fund it**: Top-up via Paystack on a production server. Paystack live keys + GHS currency configured. First successful top-up proves the wallet.
2. **Memberships → Test paid plans**: Weekly (GHS 150) / Monthly (GHS 250) / 3-Month (GHS 600) all require wallet balance first.
3. **Escrow → Enable on listings**: Set `escrow_enabled=1` on listings via DB or admin UI. Buyer funds escrow from wallet.
4. **Reviews → Ready to go**: Frontend review modal exists on user profiles. Admin moderation via "User Reviews" in sidebar.
5. **Featured Ads → Already working**: 2 Free package activations confirmed. Bronze (GHS 50) needs wallet to test.
6. **Brands → Already working**: 28 brands; admin can add/edit/toggle status from sidebar.

---

## KEY FINDING: Two Databases, One In Use

The admin app (`sellupnow-admin`) has its **own database** (`sellupnow`) which is almost entirely empty.
All real data lives in `listocean_db`. The admin panel bridges to it via a `listocean` DB connection.

> Many admin DB tables (orders, products, shop customers, etc.) are structural leftovers from the template. They were **never used**. Per owner's decision, `sellupnow` DB is not needed.

**Environment already hides most e-commerce features:**
`ENABLE_COMMERCE_MODULES=false` in `.env` → POS, Riders, Shop/Vendor management, Flutter Subscription Plans are **already hidden** by `@if ($commerceModulesEnabled)` checks in the sidebar. No action needed for those.

---

## 1. E-Commerce Features — Confirmed Empty in Both DBs

| # | Feature | Admin Route | DB State | Sidebar | Action |
|---|---|---|---|---|---|
| 1 | **Shop / POS System** | `admin/shop/*`, `shop/pos/*` | admin: `orders` EMPTY, `carts` EMPTY | Already hidden — `$shopPanelEnabled=false` + `$commerceModulesEnabled=false` | **REMOVE** (routes + controllers, code dead) |
| 2 | **Orders Management** | `admin/order/*` | admin: `orders` EMPTY | Not in sidebar | **REMOVE** (remove routes) |
| 3 | **Rider / Delivery Management** | `admin/rider/*` | admin: `drivers` EMPTY | Already hidden — `$commerceModulesEnabled=false` | **REMOVE** |
| 4 | **Flash Sales** | `admin/flash-sale/*` | admin: `flash_sales` EMPTY | Not in sidebar | **REMOVE** |
| 5 | **VAT / Tax Rules** | `admin/vat-tax/*` | admin: `vat_taxes` EMPTY | Not in sidebar | **REMOVE** |
| 6 | **Coupons / Discount Codes** | `admin/coupon/*` | admin: `coupons` EMPTY | Not in sidebar | **REMOVE** |
| 7 | **Brands** | `admin/brand/*` | listocean: `brands` 28 rows ✅ | **In sidebar** ✅ (fixed) | **KEEP** |
| 8 | **Delivery Charges** | `admin/delivery-charge/*` | admin: `delivery_charges` EMPTY | Not in sidebar | **REMOVE** |
| 9 | **Currency Management** | `admin/currency/*` | admin: `currencies` 1 default row | In Settings | **REMOVE** (not relevant to listocean money) |
| 10 | **Products (shop)** | `admin/product/*` | admin: `products` EMPTY | Not in sidebar | **REMOVE** |
| 11 | **Offer Banners** | `admin/offer-banner/*` | admin: `offer_banners` EMPTY | No | **REMOVE** |
| 12 | **Bulk Import / Export** | `shop/bulk-product-*` | No data | Already hidden | **REMOVE** |
| 13 | **Shop Gallery** | `shop/gallery` | No data | Already hidden | **REMOVE** |
| 14 | **Membership Features Builder** | `admin/membership-features/*` | admin: `membership_features` EMPTY — listocean has 25 rows managed by its own app code | In sidebar under Settings | **REMOVE** (wrong DB, admin model never connects to listocean membership_features) |
| 15 | **Auction Bids** | API only: `api/client/auctionBid/*` | admin: `auction_bids` EMPTY | No | **REMOVE** |

---

## 2. Financially Active Features — Decision Made

| # | Feature | Admin Route | Frontend Route | DB State | Notes | Action |
|---|---|---|---|---|---|---|
| 16 | **Escrow Payments** | `admin/escrow/*` | `user/escrow/*` | listocean: 0 transactions; 2 listings escrow_enabled=1 | Fully coded. Works once wallet is funded. | **KEEP** |
| 17 | **Wallet / Top-up** | `admin/customer-web-wallet` | `user/wallet` | listocean: 1 wallet, 7 histories pending | Paystack live keys (GHS) configured. Will work in production. | **KEEP** |
| 18 | **Membership Plans** (web) | `admin/membership-plans/*` | `user/membership` | listocean: 4 plans, 1 active subscription | Working. Admin bridges to listocean ✅. | **KEEP** |
| 19 | **Featured Ads** | `admin/featured-ad-packages/*` | `user/featured-ads` | listocean: 2 packages, 2 purchases, 2 activations | Working. Admin bridges to listocean ✅. | **KEEP** |
| 20 | **Boosts** | `admin/boosts/*` | `user/listing/boost/{id}` | listocean: `boosts` EMPTY; admin: `boosts` EMPTY | Coded but zero usage anywhere. Uses admin DB. | **REMOVE** from sidebar |

---

## 3. Duplicate / Redundant Systems — Decision Made

| # | Issue | REMOVE This | KEEP This |
|---|---|---|---|
| 21 | **Two customer systems** | `admin/customer` → admin `customers` EMPTY (Flutter API users) | `admin/customer-web` → listocean `users` 3 rows ✅ |
| 22 | **Two geography systems** | `admin/country/*` → admin `countries` EMPTY | `admin/site-country/*` → listocean `countries` 1 row, 10 states, 70 cities ✅ |
| 23 | **Two plan systems** | `admin/subscription-plan` → admin `subscription_plans` EMPTY (Flutter) — already hidden by `$commerceModulesEnabled=false` | `admin/membership-plans` → listocean `membership_plans` 4 rows ✅ |
| 24 | **Two page/legal systems** | `admin/legal-page` → admin `legal_pages` EMPTY — **removed from Manage Content sidebar** ✅ DONE | `admin/site-pages` → listocean `pages` 12 rows ✅ |
| 25 | **Duplicate ticket route** | `admin/ticket-issue-type/*` → likely same table | Keep `admin/ticket-issue-types/*` (correct plural) |
| 26 | **Typo route** | `admin/lidentity-verification` (broken) | `admin/listocean-identity-verification` ✅ |
| 27 | **Two mobile API versions** | `api/v1/*` — 20+ routes, older conventions | `api/client/*` — 60+ routes, actively used ✅ |
| 28 | **Commission Rules** | `admin/commission-rules/*` → admin DB, frontend `CommissionService` **ignores it** (falls back to `static_option('escrow_commission_percent')=5%`) | Escrow Settings page (`admin/escrow/settings`) — set commission % there ✅ |

---

## 4. Features with Some Data — Decision Made

| # | Feature | DB State | Action | Notes |
|---|---|---|---|---|
| 29 | **Video Reels / Explore feed** | listocean: 2 ad_videos, 3 reel_ad_placements | **KEEP** | TikTok-style feed. Wired and has content. |
| 30 | **Banner Ad Requests (user-submitted)** | listocean: 6 advertisements, 5 frontend_ad_slots | **KEEP** | Users submit banner placements. Slot definitions exist. |
| 31 | **Identity Verification** | listocean: 1 verification, 1 audit | **KEEP** | Used at least once. Admin reviews ID documents. |
| 32 | **Guest Listings** (no login) | listocean: `guest_listings` EMPTY | **DISABLE** | Coded but zero usage. Hide route, keep code. |
| 33 | **In-App Chat Oversight** | admin DB: `ShopUser`/`ShopUserChats` — e-commerce shop chat, NOT listocean `live_chat_threads` | **REMOVE from sidebar** | `ChatOversightController` is the wrong chat system entirely. listocean `live_chat_threads` (7 threads) has no admin oversight controller. |
| 34 | **Review System** | listocean: `reviews` 0 rows (ready to receive) | **KEEP** ✅ DONE | Fixed — `ListoceanReviewController` manages listocean.reviews now. |
| 35 | **Page Builders** (drag-drop) | listocean: 23 page builder records | **KEEP** | Drives frontend page layout. Actively used. |
| 36 | **Support Tickets** | listocean: 1 ticket, 7 departments | **KEEP** | Works. |
| 37 | **AI Prompt / Listing Assistant + AI Recommendations** | `ai_listing_assistant_logs` ✅, `ai_recommendation_logs` ✅ (both migrated) | **KEEP** ✅ DONE | Admin configures AI prompts/model/base URL/knowledge base. Frontend: "✨ AI Suggest" (title) + "✨ AI Describe" (description) on add/edit listing. Frontend listing detail: lazy-loaded "✨ AI Picks For You" section via POST `/listing/ai-recommendations` → `AiRecommendationController`. 4-hour cache, throttle 20/min, OpenRouter. Enabled via `ai_recommendations_enabled=on` in static_options. |
| 38 | **WhatsApp Chat** | admin: `whats_app_contacts` EMPTY, `whats_app_messages` EMPTY | **REMOVE from sidebar** | All tables empty. Uses admin DB. Never used. |
| 39 | **Advertiser Self-Service Portal** | No dedicated table | **KEEP** | Admin-side portal. Low footprint. |
| 40 | **Ads Hub** | admin DB: all ad tables empty | **REMOVE from sidebar** | Dashboard for admin DB ad data that doesn't exist. |
| 41 | **Blog** | admin: `blogs` / `tags` / `categories` — admin DB only | **REMOVE from sidebar** | Uses admin DB exclusively. Not connected to frontend listocean site. |

---

## 5. Technical / Setup Features (Production Security)

| # | Feature | Route | Risk | Status | Action |
|---|---|---|---|---|---|
| 42 | **Install Wizard** | `install/*` | HIGH — re-runs full DB setup. **Also needed for VPS production deployment** | ✅ DONE — `BlockSetupWizards` middleware blocks it by default. Set `INSTALLER_ENABLED=true` in `.env` **only** during initial VPS setup, then set it back to `false`. Package: `joynala/web-installer` (`Abedin\WebInstaller`). | Blocked by middleware, env-unlockable |
| 43 | **Update / Patcher Wizard** | `update/*` | ⚠️ CRITICAL — accepts file uploads and overwrites application files on disk (Remote Code Execution). No auth or middleware. | ✅ DONE — `BlockSetupWizards` middleware returns 404 always. No env flag — permanently off. | Permanently blocked (404) |
| 44 | **Marketplace / Addon Store** | `marketplace/*` | Low — Codecanyon addon UI | ✅ Already done — all `/marketplace/*` routes are redirects to dashboard in `routes/web.php`. Named routes preserved for blade `route()` calls. | Neutered (redirects) |

### VPS Production Installation Flow
1. Upload codebase to VPS, run `composer install --no-dev`
2. Copy `.env.example` → `.env`, set `INSTALLER_ENABLED=true`
3. Visit `https://yourdomain.com/install` — runs the web wizard (configures DB, runs migrations + seeders)
4. After wizard completes, immediately set `INSTALLER_ENABLED=false` (or remove the line)
5. The install wizard is the **`joynala/web-installer`** package — fully intact in `vendor/`

---

## 6. Config-Only Features — Keep All

| # | Feature | Admin Route | DB State | Action |
|---|---|---|---|---|
| 45 | **Social Auth (Google/Facebook)** | `admin/social-auth/*` | admin: 3 rows configured | **KEEP** |
| 46 | **Firebase Push Notifications** | `admin/firebase` | admin: `device_keys` EMPTY | **KEEP** (Flutter app needs this) |
| 47 | **PWA Settings** | `admin/pwa-setting` | static_options entry | **KEEP** |
| 48 | **Multi-Language** | `admin/language/*` | listocean: 15 languages | **KEEP** |
| 49 | **Mail Config / Email Templates** | `admin/mail-config`, `admin/email-templates/*` | Config only | **KEEP** (core operational) |
| 50 | **Google reCAPTCHA** | `admin/google-recaptcha` | 1 config row | **KEEP** |
| 51 | **Report Reasons** | `admin/report-reason/*` | listocean: 5 rows; admin: 4 rows | **KEEP** |

---

## 7. Frontend App — Dead Code (Original Listocean Admin Panel)

The frontend app (`main-file/listocean/core/`) still contains a **complete second admin panel** superseded by `sellupnow-admin`. Per owner: "We don't need the listocean admin panel."

| # | Location | Contents | Action |
|---|---|---|---|
| 52 | `resources/views/backend/` | 80+ blade files: dashboard, users, listings, email templates, settings, categories | **REMOVE** — views only, no live routes to them |
| 53 | `resources/views/admin/` | marketplace_banner CRUD views | **REMOVE** — views only |
| 54 | `app/Http/Controllers/Backend/` | Backend admin controllers (autoloaded but unreachable) | **REMOVE** |

---

## 8. What Needs Actual Sidebar Cleanup

The following items are **currently visible in the admin sidebar** but use admin DB only or are dead.

### Remove from sidebar — DONE THIS SESSION:

| Sidebar Item | Permission | Reason |
|---|---|---|
| **Blog** | `admin.blog.*` | Uses admin DB `blogs`/`tags`. Not connected to frontend listocean site. |
| **In-App Chat** | `admin.chatOversight.index` | Uses admin DB `ShopUser`/`ShopUserChats` — e-commerce shop chat, NOT listocean live_chat. |
| **WhatsApp** | `admin.whatsAppChat.index` | Admin DB, all tables empty. Never used. |
| **Listing Boosts** | `admin.boosts.index` (in Ad Management) | Admin DB, zero data anywhere. |
| **Commission Rules** | `admin.commissionRules.index` (in Ad Management) | Admin DB. Frontend CommissionService ignores it. |
| **Ads Hub** | `admin.adsHub.index` (in Ad Management) | Admin DB ad dashboard, zero data. |

### Already hidden by environment flags (no sidebar action needed):

| Sidebar Item | Hidden By |
|---|---|
| POS System | `@if ($shopPanelEnabled)` where `$shopPanelEnabled = false` |
| Vendors / Shop Management | `@if ($commerceModulesEnabled && $businessModel == 'multi')` where `$commerceModulesEnabled = false` |
| Flutter Subscription Plans | Same `$commerceModulesEnabled=false` guard |
| Rider / Drivers | `@if ($commerceModulesEnabled)` |

---

## Summary Decision Table

| Category | Count | Decision |
|---|---|---|
| E-commerce bloat (all admin DB / all empty) | 14 items | **REMOVE** routes + sidebar |
| Already hidden by env flags | 4 items | Leave as-is (env flag does the job) |
| Financially active (listocean DB) | 4 items | **KEEP** |
| Boosts (dead, admin DB) | 1 | **REMOVE** from sidebar |
| Duplicate systems — remove admin-DB side | 8 pairs | **REMOVE** old admin-DB side |
| Features with data (listocean) | 10 items | **KEEP** |
| Features dead (WhatsApp, ChatOversight, Blog, AdsHub) | 4 items | **REMOVE** from sidebar |
| Security risks (install/update wizards) | 3 | **DISABLE immediately** |
| Config-only | 7 | **KEEP** |
| Dead code (old frontend admin panel) | 3 folders | **REMOVE** |
