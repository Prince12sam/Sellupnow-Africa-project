# SellUpNow — Web/Admin Status

> Last updated: 2026-02-28
> See `README.md` for full architecture, business logic, and implementation detail.

## Current state

- ListOcean customer web runs locally at `http://127.0.0.1:8090`
- SellUpNow admin runs locally at `http://127.0.0.1:8091/admin`
- ListOcean admin panel is **disabled** — SellUpNow admin is the single control surface
- Admin manages ListOcean data via secondary `listocean` MySQL connection (no API hop)

## Completed features (2026-02 sprint)

### Infrastructure
- [x] Cross-DB connection (`listocean`) — admin reads/writes frontend DB directly
- [x] Mail config DB-driven — admin sets SMTP via panel, no `.env` keys needed on frontend
- [x] `CustomConfigServiceProvider` injects SMTP config at runtime from DB settings

### User Profile & Account Settings
- [x] Added `is_notifications_allowed`, `is_contact_info_visible` columns + migration
- [x] Profile form: Address, About/Bio, notification toggles, contact visibility toggle
- [x] Fixed `zip_code` and `identification_number` input types (`number` → `text`)
- [x] Fixed password field autocomplete attributes

### User Dashboard Pages
- [x] My Reviews page (`/user/my-reviews`)
- [x] My Videos page (`/user/my-videos`)
- [x] Blocked Users page (`/user/blocked-users`) — correct site layout, AJAX unblock with fade
- [x] Notifications page (`/user/notification/list`) — paginated, mark-all-read action

### Sidebar Navigation
- [x] All mobile app sidebar items mirrored to web:
  Notifications (unread badge) · Blocked Users · Language · FAQs · Contact Us · About Us · T&C · Privacy Policy · Log Out

### KYC / Identity Verification
- [x] Added `selfie_photo`, `decline_reason` columns to `identity_verifications` (migration)
- [x] Admin approve/decline endpoints — 403 fixed (root role bypass added)
- [x] Audit log on approve/decline (`identity_verification_audits` table)

### Admin Panel Fixes
- [x] 403 error page — replaced broken `via.placeholder.com` external image with inline SVG

## Remaining work

### Must-do before production
- [ ] `APP_ENV=production`, `APP_DEBUG=false`, real domains + HTTPS
- [ ] Queue driver: change from `sync` to `database` or `redis`
- [ ] Session + cache: configure for production (not `file`)
- [ ] End-to-end mail test with real SMTP credentials via admin panel
- [ ] Email user when identity verification is approved or declined

### Feature work (next sprint)
- [ ] Notifications — add `notify()` calls at key events (message received, listing status change, KYC result, new review)
- [ ] Block enforcement — filter blocked users from feeds, listing results, and chat
- [ ] Block button on user profile / listing detail page
- [ ] My Reviews — wire controller to actual `reviews` table
- [ ] My Videos — wire controller to actual `videos`/`listing_videos` table
- [ ] Language switcher — implement actual locale change (not just anchor link)
- [ ] Boost / featured ads — booking UI + admin management
- [ ] Escrow checkout + dispute flow
- [ ] Re-submission flow for declined KYC

## Commands

```bash
# Start frontend
php -S 127.0.0.1:8090 -t "main-file\listocean" "main-file\listocean\server-router.php"

# Start admin
cd sellupnow-admin && php artisan serve --host=127.0.0.1 --port=8091

# Frontend migrations
cd main-file/listocean/core && php artisan migrate

# Clear all caches (admin)
cd sellupnow-admin && php artisan config:clear && php artisan cache:clear && php artisan view:clear

# Import location data
php artisan listocean:import-locations --country="Ghana"
```


## What this file is

This is the short, up-to-date status/checklist for the **SellUpNow admin + ListOcean customer web** program.

Detailed historical logs and deep matrices were moved to:

- `FRONTEND-ADMIN-CONNECTION-AUDIT.md` (what changed, where, and why)
- `ADMIN-PANEL-LISTOCEAN-BACKEND-CUTOVER-MATRIX.md` (strategy/risk/decommission planning)

## Current state (high level)

- ListOcean customer web is live locally at `http://127.0.0.1:8090`
- SellUpNow admin is the only admin panel (ListOcean admin disabled) and runs locally at `http://127.0.0.1:8098/admin`
- SellUpNow manages ListOcean data/settings via:
  - Secondary SQLite connection `listocean` → `main-file/listocean/core/database/database.sqlite`
  - Syncing settings into ListOcean `static_options` (social auth, captcha)

## Key capabilities implemented

- Social login settings centralized in SellUpNow and reflected in ListOcean frontend
- Captcha centralized in SellUpNow with provider toggle (Google reCAPTCHA / Cloudflare Turnstile)
- Listing moderation in SellUpNow pulls ListOcean listings with queues (all/new/update/removed)
- Brands in SellUpNow manage ListOcean `brands` so listing create dropdowns populate
- Listing Locations managed in SellUpNow and used by ListOcean frontend:
  - Country → City → Town (UI labels)
  - Stored as ListOcean `countries` → `states` → `cities`
  - Auto-import supported via CountriesNow public API

## Smoke test summary (latest)

- Web auth (register/login): 
- Web listing flows (browse/detail/favorite/report/profile): 
- Web chat send/history: 
- Admin core pages open without runtime route/permission errors: 
- Admin listing moderation + settings/content surfaces: 

## Commands you may need

### Re-import Cities/Towns for a country (no manual entry)

From SellUpNow app:

`php artisan listocean:import-locations --country="Ghana"`

Note:

- On Windows, HTTPS may fail with `cURL error 60` if a CA bundle is missing.
- Local/dev fallback retry is supported; for production, set `COUNTRIESNOW_CA_BUNDLE`.

## Remaining work (real blockers)

- Production hardening:
  - `APP_ENV=production`, `APP_DEBUG=false`, real domains/HTTPS
  - queues/sessions/caches configured for production (not sync/file)
  - credentials/monitoring/runbook validated

## Policy / execution order

- Web + Admin must be stable in production before mobile starts.

## Pending tasks — Modern product features

These are the next feature adds requested before/around production. This list is intentionally short and grouped by domain.

### Ads / Promo / Banners (monetization + growth)

- Admin banner management enhancements (positions/active toggle/image upload) + confirm customer-web rendering
- Admin promo content management (sections/pages/rich content) + confirm client fetch wiring
- Sponsored ad UI blocks (hero/inline/category units)

### Video reels (short-form video)

- Reels feed page + upload flow
- Video API layer (feed/upload/like/delete/seller videos)
- Admin moderation for reels (approve/reject/feature/sponsor/delete)

### Escrow + disputes (trust + payments)

- Customer escrow checkout flow + dispute raise UI
- Orders statuses for escrow/disputed + wallet release flow
- Backend endpoints for dispute resolution + admin handling surface

### Scam alerts / safety

- Safety tips (ensure API-driven content + fallback content)
- Reporting reasons include fraud/scam categories
- Listing detail UI: “report suspicious ads” + safety tips block

### KYC / verification badge (trust)

- Verified badge workflow: selfie requirement + admin review queue

Notes:

- For production on VPS, finalize the above only after environment hardening (APP_ENV/DEBUG, queue, cache/session, HTTPS, CA bundle).
