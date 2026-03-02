# SellUpNow — Platform Engineering Notes

> **Last updated:** 2026-02-28
> **Stack:** Laravel 10 (admin) · PHP 8.2 built-in server (frontend/ListOcean) · MySQL · Flutter (mobile)

---

> **Engineering docs quick links:**
> - Platform services, business logic & revenue model → [`PLATFORM-SERVICES.md`](PLATFORM-SERVICES.md)
> - Admin panel deep audit (controllers, DB tables, implementation status) → [`ADMIN-PANEL-AUDIT.md`](ADMIN-PANEL-AUDIT.md)
> - Implementation workflow — user journeys, file list, phased release plan → [`IMPLEMENTATION-WORKFLOW.md`](IMPLEMENTATION-WORKFLOW.md)
> - Membership tier design — 4 tiers, pricing, business rules, setup steps → [`MEMBERSHIP-SYSTEM.md`](MEMBERSHIP-SYSTEM.md)

## Table of Contents

1. [System Architecture](#1-system-architecture)
2. [Local Development Setup](#2-local-development-setup)
3. [Database Architecture](#3-database-architecture)
4. [Admin Panel — SellUpNow (`sellupnow-admin`)](#4-admin-panel--sellupnow)
5. [Customer Web — ListOcean Frontend](#5-customer-web--listocean-frontend)
6. [Feature Implementation Log](#6-feature-implementation-log)
7. [Business Logic Reference](#7-business-logic-reference)
8. [Known Issues & Fixes Applied](#8-known-issues--fixes-applied)
9. [Remaining Work](#9-remaining-work)
10. [Environment Variables Reference](#10-environment-variables-reference)

---

## 1. System Architecture

```
┌─────────────────────────────────────────────────────────┐
│                     SellUpNow Platform                  │
├──────────────────────┬──────────────────────────────────┤
│  Admin Panel         │  Customer Web (ListOcean)        │
│  sellupnow-admin/    │  main-file/listocean/core/       │
│  Laravel 10          │  Laravel (frontend)              │
│  :8091               │  :8090                           │
├──────────────────────┴──────────────────────────────────┤
│              MySQL Database Layer                        │
│  sellupnow_admin DB  ←→  listocean_db                   │
│  (admin users,           (customers, listings,          │
│   settings, roles,        identity_verifications,       │
│   permissions)            blocked_users, notifications) │
├─────────────────────────────────────────────────────────┤
│              Flutter Mobile App                         │
│  Flutter App/  — connects to ListOcean API              │
└─────────────────────────────────────────────────────────┘
```

### Cross-DB Communication Pattern

The admin panel reads/writes the ListOcean database using Laravel's **secondary DB connection** named `listocean` (configured in `sellupnow-admin/config/database.php`). This is how admin actions like identity verification, listing moderation, and customer management touch the frontend database without API calls.

---

## 2. Local Development Setup

### Prerequisites
- PHP 8.2+ with extensions: `pdo_mysql`, `mbstring`, `openssl`, `fileinfo`, `gd`
- MySQL 8.0+
- Composer
- Node.js + npm (for Vite asset pipeline on admin)

### Start Frontend (ListOcean)
```powershell
php -S 127.0.0.1:8090 -t "main-file\listocean" "main-file\listocean\server-router.php"
```
Document root: `main-file/listocean/`  
Laravel core: `main-file/listocean/core/`

### Start Admin Panel (SellUpNow)
```powershell
Set-Location sellupnow-admin
php artisan serve --host=127.0.0.1 --port=8091
```
Admin URL: `http://127.0.0.1:8091/admin`

### Useful Artisan Commands
```bash
# Clear all caches (run after .env or config changes)
php artisan config:clear && php artisan cache:clear && php artisan view:clear

# Run migrations (frontend)
cd main-file/listocean/core && php artisan migrate

# Import location data (countries/states/cities)
php artisan listocean:import-locations --country="Ghana"
```

---

## 3. Database Architecture

### Frontend DB — `listocean_db`

| Table | Purpose |
|-------|---------|
| `users` | Customer accounts. Key cols: `verified_status` (0=unverified, 1=verified), `status`, `first_name`, `last_name`, `username`, `profile_photo`, `address`, `about`, `is_notifications_allowed`, `is_contact_info_visible` |
| `listings` | Ad listings posted by users |
| `identity_verifications` | KYC requests. Cols: `user_id`, `identification_type`, `identification_number`, `front_document`, `back_document`, `selfie_photo`, `status` (0=pending, 1=approved, 2=declined), `verify_by`, `decline_reason` |
| `blocked_users` | User-level blocks. Cols: `blocker_id`, `blocked_user_id`, `created_at` |
| `notifications` | Laravel `DatabaseNotifications` for users |
| `countries` / `states` / `cities` | Location hierarchy used by listing forms |
| `static_options` | Key-value store for frontend configuration (SMTP, social auth, captcha, etc.) |

### Admin DB — `sellupnow_admin`

| Table | Purpose |
|-------|---------|
| `users` | Admin accounts (separate from frontend users) |
| `roles` / `permissions` | Spatie Laravel Permission. Root role bypasses all permission checks |
| `identity_verification_audits` | Audit trail when admin approves/declines KYC requests |
| `settings` | Platform-wide settings including SMTP configuration (keys: `site_smtp_mail_host`, `site_smtp_mail_port`, `site_smtp_mail_username`, `site_smtp_mail_password`, `site_smtp_mail_encryption`, `site_smtp_mail_mailer`, `site_global_email`) |

### Key Relationships (Cross-DB via `listocean` connection)
- Admin `approve()` → updates `listocean_db.identity_verifications.status = 1` + `users.verified_status = 1`
- Admin `decline()` → updates `listocean_db.identity_verifications.status = 2` + stores `decline_reason`
- Admin settings page → writes `site_smtp_mail_*` to `sellupnow_admin.settings`
- `CustomConfigServiceProvider::boot()` → reads those settings at runtime and injects into Laravel's mail config

---

## 4. Admin Panel — SellUpNow

**Location:** `sellupnow-admin/`  
**Framework:** Laravel 10, Spatie Permissions, Vite

### Permission Model

All admin controller methods follow this guard pattern:

```php
try {
    $user = auth()->user();
    $isRoot = false;
    try { $isRoot = $user?->getRoleNames()?->contains('root'); } catch (\Throwable $_) {}
    if (! ($isRoot || $user?->hasPermissionTo('admin.resource.action'))) {
        abort(403);
    }
} catch (\Throwable $_) {
    abort(403);
}
```

**Rule:** The `root` role always bypasses permission checks. Sub-admin users need explicit permissions assigned. Always include the `$isRoot` bypass — omitting it causes 403s for the root admin.

### Key Admin Controllers

| Controller | Route Prefix | Purpose |
|-----------|-------------|---------|
| `ListoceanIdentityVerificationController` | `admin/identity-verification` | Review, approve, decline KYC submissions from frontend |
| `ListoceanCustomerController` (siteCustomerController) | `admin/site-customer` | Manage frontend user accounts |
| `ListingModerationController` | `admin/listing-moderation` | Review new/updated/flagged listings |
| `VideoModerationController` | `admin/video-moderation` | Review user video uploads |
| `MailConfigurationController` | `admin/mail-configuration` | SMTP settings (reflected to frontend at runtime) |
| `ListoceanMembershipPlanController` | `admin/membership-plan` | Subscription tiers for frontend users |

### Identity Verification Workflow (Admin Side)

1. Frontend user submits KYC via `/user/identity-verification` form (uploads docs + selfie)
2. Record appears in `listocean_db.identity_verifications` with `status=0` (pending)
3. Admin visits `admin/identity-verification` → filtered queues: **Queue** (pending+declined), **Approved**, **Declined**, **All**
4. Admin clicks through to detail view → sees front doc, back doc, selfie, user info
5. Admin hits **Approve** → POST `admin/identity-verification/{id}/approve`
   - Sets `identity_verifications.status = 1`
   - Sets `users.verified_status = 1` (shows verified badge on frontend)
   - Logs to `identity_verification_audits`
6. Admin hits **Decline** → POST `admin/identity-verification/{id}/decline` with optional `decline_reason`
   - Sets `identity_verifications.status = 2`
   - Stores `decline_reason` on the record
   - Sets `users.verified_status = 0`
   - Logs to `identity_verification_audits`

### Mail Configuration (Admin Controls SMTP)

The admin updates SMTP settings via `admin/mail-configuration`. These are stored in `sellupnow_admin.settings` under keys:
- `site_smtp_mail_host`
- `site_smtp_mail_port`
- `site_smtp_mail_username`
- `site_smtp_mail_password`
- `site_smtp_mail_encryption`
- `site_smtp_mail_mailer`
- `site_global_email`

`sellupnow-admin/app/Providers/CustomConfigServiceProvider.php` reads these at boot and calls `config(['mail.mailers.smtp.*' => ...])` to inject them at runtime. The frontend `.env` has **no hardcoded SMTP keys** — all mail config comes from the DB. This means changing SMTP in admin panel takes effect on next request without deployment.

---

## 5. Customer Web — ListOcean Frontend

**Location:** `main-file/listocean/core/`  
**Framework:** Laravel (custom frontend setup with PHP built-in server)

### User Dashboard Pages

All user dashboard pages share the same layout wrapper. The pattern is strictly:

```blade
<div class="profile-setting [page-class] section-padding2">
  <div class="container-1920 plr1">
    <div class="row"><div class="col-12">
      <div class="profile-setting-wraper">
        @include('frontend.user.layout.partials.user-profile-background-image')
        <div class="down-body-wraper">
          @include('frontend.user.layout.partials.sidebar')
          <div class="main-body">
            <x-frontend.user.responsive-icon/>
            <div class="relevant-ads box-shadow1">
              <h4 class="dis-title">Page Title</h4>
              {{-- page content here --}}
            </div>
          </div>
        </div>
      </div>
    </div></div>
  </div>
</div>
```

**Never use** `dashboard-layout-wrapper` + Bootstrap grid cols for user pages — that is the wrong layout and produces broken/inconsistent design.

### User Dashboard Routes (`core/routes/user.php`)

| Route | Name | View |
|-------|------|------|
| `GET /user/profile-setting` | `user.profile.setting` | Account settings |
| `GET /user/my-reviews` | `user.my.reviews` | Reviews received by user |
| `GET /user/my-videos` | `user.my.videos` | Videos uploaded by user |
| `GET /user/blocked-users` | `user.blocked.users` | Users this user has blocked |
| `POST /user/block/{id}` | `user.block.user` | Block a user |
| `DELETE /user/unblock/{id}` | `user.unblock.user` | Unblock (AJAX, returns JSON) |
| `GET /user/notification/list` | `user.notification.index` | Notification centre |
| `POST /user/notification/read` | `user.notification.read` | Mark all notifications read |

### Sidebar Navigation (`frontend/user/layout/partials/sidebar.blade.php`)

Current sidebar order (matching mobile app):
1. Dashboard
2. My Listing
3. My Favourite
4. Post New Ad
5. My Membership
6. Wallet
7. My Orders
8. Blog
9. **Notifications** (with unread count badge — live from DB)
10. **Blocked Users**
11. Settings
12. **Language** (anchor to `#language` section in account-settings)
13. _(divider)_
14. **FAQs** → `/faq`
15. **Contact Us** → `/contact`
16. **About Us** → `/about`
17. **Terms & Conditions** → `/terms-and-conditions`
18. **Privacy Policy** → `/privacy-policy`
19. _(divider)_
20. **Log Out** (red, uses `route('user.logout')`)

### Blocked Users Business Logic

- `blocked_users` table: `blocker_id` (the acting user), `blocked_user_id` (who is blocked)
- Model: `core/app/Models/Frontend/BlockedUser.php`
  - `blockedUser()` → `belongsTo(User, 'blocked_user_id')` — the person who was blocked
  - `blocker()` → `belongsTo(User, 'blocker_id')` — who did the blocking
- Controller: `UserController::blockUser($id)` / `unblockUser($id)`
- Unblock is via `DELETE /user/unblock/{id}` returning JSON `{success, message}`
- The view uses JS fetch with a 300ms fade-out animation on the row before removal

### Notifications Business Logic

- Uses Laravel's built-in `DatabaseNotifications` (stored in `notifications` table)
- `NotificationController::index()` → paginates `auth()->user()->notifications()->paginate(20)`
- `NotificationController::read_notification()` → calls `markAllAsRead()` on the user's unread notifications
- The unread count badge in the sidebar: `auth()->user()->unreadNotifications()->count()`

---

## 6. Feature Implementation Log

### Sprint: 2026-02 (this session)

#### Profile Fields Gap — Migrations & UI
- **Migration:** `2026_02_28_000001_add_profile_settings_to_users_table.php`
  - Added `is_notifications_allowed` (tinyint, default 1)
  - Added `is_contact_info_visible` (tinyint, default 1)
- **View:** `account-settings.blade.php` — added Address, About/Bio fields, notification + contact visibility toggles
- **Controller:** profile update AJAX payload extended to include new fields
- **Model:** `User.php` `$fillable` updated

#### New User Pages
- `frontend/user/profile/my-reviews.blade.php` — Reviews with correct layout
- `frontend/user/profile/my-videos.blade.php` — Videos with correct layout
- `frontend/user/blocked/blocked-users.blade.php` — Blocked users with correct layout
- `frontend/user/notification/index.blade.php` — Notification centre

#### Sidebar — Full Completion
All sidebar items from the mobile app added to `sidebar.blade.php`:
- Notifications with unread badge count
- Blocked Users
- Language (anchor to account-settings)
- FAQs, Contact Us, About Us, T&C, Privacy Policy
- Log Out (red)

#### Identity Verification — DB Schema Fix
- **Migration:** `2026_03_01_000002_add_selfie_photo_decline_reason_to_identity_verifications.php`
  - Added `selfie_photo` (varchar, nullable)
  - Added `decline_reason` (text, nullable)
- These columns were referenced in the show view but missing from the DB, causing 500 errors

#### Identity Verification — 403 Fix
- **Root cause:** `approve()` and `decline()` methods in `ListoceanIdentityVerificationController` used `abort(403)` in the catch block, meaning even root admins got 403 because the permission name didn't exist in the DB
- **Fix:** Added `$isRoot = $user?->getRoleNames()?->contains('root')` bypass, matching pattern used in all other controllers (`BoostController`, `CommissionRuleController`, etc.)

#### Mail Configuration — DB-Driven SMTP
- **Root cause:** Frontend had `MAIL_MAILER=log` hardcoded; production emails weren't sending. Real fix: admin controls SMTP from the panel.
- **Implementation:** `CustomConfigServiceProvider::boot()` reads `site_smtp_mail_*` keys from `sellupnow_admin.settings` and injects into Laravel mail config at runtime via `config([...])` calls
- Frontend `.env` has no SMTP keys — config comes entirely from the admin DB

#### Account Settings — Browser Console Error Fixes
| Error | Root Cause | Fix |
|-------|-----------|-----|
| `PS000000000 cannot be parsed` | `zip_code` input was `type="number"` — can't handle leading zeros or alphanumeric | Changed to `type="text"` |
| `id_number cannot be parsed` | `identification_number` input was `type="number"` | Changed to `type="text"` |
| Password autocomplete warnings | No `autocomplete` attribute on password fields | Added `autocomplete="current-password"` / `autocomplete="new-password"` |

#### 403 Error Page — Broken Image Fix
- `sellupnow-admin/resources/views/errors/403.blade.php` referenced `https://via.placeholder.com` which doesn't resolve
- Replaced with inline SVG — no external network dependency

---

## 7. Business Logic Reference

### User Verification Status (`users.verified_status`)
| Value | Meaning |
|-------|---------|
| `0` | Not verified (default) |
| `1` | Identity verified — shows verified badge on profile/listings |

This is set by admin approval only. Users cannot self-verify.

### Identity Verification Status (`identity_verifications.status`)
| Value | Meaning |
|-------|---------|
| `0` | Pending review |
| `1` | Approved |
| `2` | Declined |

### Permission Guard Pattern (Admin Controllers)
Every admin controller action that modifies data must follow:
```php
$user = auth()->user();
$isRoot = false;
try { $isRoot = $user?->getRoleNames()?->contains('root'); } catch (\Throwable $_) {}
if (! ($isRoot || $user?->hasPermissionTo('admin.resource.action'))) {
    abort(403);
}
```
**Never** do a bare `hasPermissionTo` without the `$isRoot` bypass — it causes 403 for root admins when the permission doesn't exist in the DB.

### Cross-DB Rule
When admin writes to the ListOcean database, always use `DB::connection('listocean')`. Never use the default connection from an admin controller for ListOcean tables.

### Blocked Users — Enforcement
The block relationship is currently stored for UI purposes (blocked users page). Feed/listing/chat filtering based on `blocked_users` should be wired in the respective query scopes when implementing those features.

### Mail Config Precedence
1. `CustomConfigServiceProvider` runs at boot
2. Reads `site_smtp_mail_*` from `sellupnow_admin.settings`
3. If host + username are both non-empty → inject into Laravel mail config
4. If either is empty → fall back to whatever is in `.env` (which may be `log` mailer for dev)

---

## 8. Known Issues & Fixes Applied

| Issue | File(s) | Status |
|-------|---------|--------|
| 403 on identity verify approve/decline for root admin | `ListoceanIdentityVerificationController.php` | ✅ Fixed |
| `via.placeholder.com` 404 on 403 error page | `resources/views/errors/403.blade.php` | ✅ Fixed |
| SMTP relay denied on frontend mail sends | `CustomConfigServiceProvider.php` | ✅ Fixed (DB-driven) |
| `selfie_photo`/`decline_reason` columns missing causing 500 | Migration `2026_03_01_000002` | ✅ Fixed |
| `zip_code` and `identification_number` parse errors in browser | `account-settings.blade.php` | ✅ Fixed |
| Password autocomplete browser warnings | `account-settings.blade.php` | ✅ Fixed |
| Blocked users page wrong layout (`dashboard-layout-wrapper`) | `blocked-users.blade.php` | ✅ Fixed |

---

## 9. Remaining Work

### High Priority (before production)
- [ ] Wire `blocked_users` table into feed/listing/chat queries so blocked users are filtered out of results
- [ ] Notification creation — currently the `notifications` table may be empty; need to add `notify()` calls at key events (new message, listing approved/rejected, new review, identity verification result)
- [ ] Identity verification: send email to user on approve/decline
- [ ] Test full mail flow end-to-end with real SMTP credentials set via admin panel
- [ ] `APP_ENV=production`, `APP_DEBUG=false` for production deploy

### Medium Priority (features)
- [ ] My Reviews page — wire to actual `reviews` table query in `UserController::myReviews()`
- [ ] My Videos page — wire to actual `videos`/`listing_videos` table query in `UserController::myVideos()`
- [ ] Language switcher — currently links to `#language` anchor on account-settings; implement actual locale switch
- [ ] Boost / featured ads — booking flow + admin management surface
- [ ] Escrow checkout flow + dispute handling

### Low Priority / Polish
- [ ] Pagination on notifications page (already implemented, verify UI renders properly)
- [ ] Block user button on user profile page (currently only unblock is in the blocked-users page; the block action needs to surface on profile/listing views)
- [ ] KYC document upload progress / re-submission flow after decline

---

## 10. Environment Variables Reference

### Frontend — `main-file/listocean/core/.env`

```ini
APP_NAME=ListOcean
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://127.0.0.1:8090

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=listocean_db
DB_USERNAME=root
DB_PASSWORD=

# SMTP is NOT set here — admin panel controls it via DB
# CustomConfigServiceProvider reads from sellupnow_admin.settings at runtime
```

### Admin — `sellupnow-admin/.env`

```ini
APP_NAME=SellUpNow
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://127.0.0.1:8091

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sellupnow_admin
DB_USERNAME=root
DB_PASSWORD=

# Secondary connection for ListOcean DB (used by cross-DB controllers)
LISTOCEAN_DB_HOST=127.0.0.1
LISTOCEAN_DB_PORT=3306
LISTOCEAN_DB_DATABASE=listocean_db
LISTOCEAN_DB_USERNAME=root
LISTOCEAN_DB_PASSWORD=

# URL of customer web (used in admin to build document preview URLs)
CUSTOMER_WEB_URL=http://127.0.0.1:8090

MAIL_FROM_ADDRESS=noreply@sellupnow.com
MAIL_FROM_NAME=SellUpNow
```
