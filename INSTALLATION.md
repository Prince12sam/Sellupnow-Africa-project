# SellUpNow — Installation & Route Reference

> **Last audited:** 2026-03-01  
> **Purpose:** Complete guide to starting the platform locally and a full reference for every route in both apps.

---

## Table of Contents

1. [Prerequisites](#1-prerequisites)
2. [Starting the Platform](#2-starting-the-platform)
3. [Runtime Addresses](#3-runtime-addresses)
4. [How the Two Apps Connect](#4-how-the-two-apps-connect)
5. [Frontend Routes](#5-frontend-routes)
6. [Admin Panel Routes](#6-admin-panel-routes)
7. [Admin API Routes (Flutter App)](#7-admin-api-routes-flutter-app)
8. [Key Boundary Rules](#8-key-boundary-rules)

---

## 1. Prerequisites

| Requirement | Version | Notes |
|-------------|---------|-------|
| PHP | 8.2+ | Installed via WinGet at `C:\Users\CybeRMogul\AppData\Local\Microsoft\WinGet\Packages\PHP.PHP.8.2_...` |
| MySQL | 5.7+ / 8.x | Bundled with XAMPP at `C:\xampp\mysql\bin\` |
| Composer | 2.x | Required for both Laravel apps |
| Node / NPM | 18+ | Required only for Vite asset compilation on admin panel |

---

## 2. Starting the Platform

Start services in this order every time:

### Step 1 — Start MySQL

```powershell
Start-Process "C:\xampp\mysql\bin\mysqld.exe" `
    -ArgumentList "--defaults-file=C:\xampp\mysql\bin\my.ini" `
    -WindowStyle Hidden

# Verify it is listening
netstat -ano | Select-String ":3306"
# Expected:  TCP  0.0.0.0:3306  LISTENING
```

### Step 2 — Start the Admin Panel (port 8091)

```powershell
cd "C:\Users\CybeRMogul\Downloads\Sellupnow project\sellupnow-admin"
php artisan serve --host=127.0.0.1 --port=8091
```

### Step 3 — Start the Frontend (port 8090)

```powershell
cd "C:\Users\CybeRMogul\Downloads\Sellupnow project\main-file\listocean\core"
php artisan serve --host=127.0.0.1 --port=8090
```

### Verify everything is up

```powershell
netstat -ano | Select-String ":3306|:8090|:8091"

Invoke-WebRequest http://127.0.0.1:8091/health -UseBasicParsing | Select-Object StatusCode
Invoke-WebRequest http://127.0.0.1:8090        -UseBasicParsing | Select-Object StatusCode
```

---

## 3. Runtime Addresses

| App | URL | Database | Config file |
|-----|-----|----------|-------------|
| **Frontend** | `http://127.0.0.1:8090` | `listocean_db` | `main-file/listocean/core/.env` |
| **Admin Panel** | `http://127.0.0.1:8091/admin` | `sellupnow_admin` + bridge → `listocean_db` | `sellupnow-admin/.env` |
| **MySQL** | `127.0.0.1:3306` | — | `C:\xampp\mysql\bin\my.ini` |

---

## 4. How the Two Apps Connect

### Mechanism 1 — Shared Database (primary — all data flow)

The admin panel declares two DB connections in `sellupnow-admin/config/database.php`:

| Connection name | Points to | Used by |
|----------------|-----------|---------|
| `mysql` (default) | `sellupnow_admin` | Admin users, roles, permissions, settings |
| `listocean` (bridge) | `listocean_db` | Every `Listocean*` controller — same DB the frontend reads |

```
Admin Panel ──DB::connection('listocean')──► listocean_db ◄── Frontend
```

All admin data entry (categories, ads, reel placements, advertisements, banners, membership plans, wallet adjustments) is written directly into `listocean_db`. No sync job, no HTTP call — both apps share the same rows.

Env variables driving the bridge (`sellupnow-admin/.env`):

```ini
LISTOCEAN_DB_CONNECTION=mysql
LISTOCEAN_DB_HOST=127.0.0.1
LISTOCEAN_DB_PORT=3306
LISTOCEAN_DB_DATABASE=listocean_db
LISTOCEAN_DB_USERNAME=root
LISTOCEAN_DB_PASSWORD=
```

### Mechanism 2 — API Key HTTP Call (file uploads only)

`GeneraleSettingController::postFileToListoceanApi()` makes one outbound POST from admin → frontend:

```
POST http://127.0.0.1:8090/api/admin/badge-upload
Header: X-Admin-Api-Key: <LISTOCEAN_ADMIN_API_KEY>
```

Used exclusively when uploading badge/logo images from admin General Settings. All other admin operations use Mechanism 1.

Env var in `sellupnow-admin/.env`:
```ini
LISTOCEAN_ADMIN_API_KEY=5e3f1b9a2c4d6f7a8b9c0d1e2f3a4b5c
```

### Mechanism 3 — URL References in Config

```ini
# sellupnow-admin/.env
APP_URL=http://127.0.0.1:8091
CUSTOMER_WEB_URL=http://127.0.0.1:8090    # admin root "/" redirects here
LISTOCEAN_APP_URL=http://127.0.0.1:8090   # used in views and email templates

# main-file/listocean/core/.env
APP_URL=http://127.0.0.1:8090
# (no reference back to admin — frontend is unaware of admin's existence)
```

---

## 5. Frontend Routes

Route files: `main-file/listocean/core/routes/web.php` and `routes/user.php`

### 5.1 Public Routes — `web.php`

| Method | URI | Controller | Route name |
|--------|-----|-----------|------------|
| GET | `/` | `FrontendController@home_page` | `homepage` |
| GET | `/explore` | `TrendingVideoController@explore` | `frontend.trending.videos` |
| GET | `/reels` | `ReelController@index` | `reels.index` |
| GET | `/reels/load` | `ReelController@load` | `reels.load` |
| GET | `/reels/{id}` | `TrendingVideoController@watch` | `frontend.reel.watch` |
| GET | `/listing/{slug}` | `FrontendListingController@frontendListingDetails` | `frontend.listing.details` |
| POST | `/listing/load-more-relevant` | `FrontendListingController@loadMoreListing` | `frontend.listing.load-more-relevant` |
| GET | `/listing/category/{slug}` | `CategoryWiseListingController@showListingsByCategory` | `frontend.show.listing.by.category` |
| GET | `/listing/sub-category/{slug}` | `CategoryWiseListingController@showListingsBySubCategory` | `frontend.show.listing.by.subcategory` |
| GET | `/listing/child-category/{slug}` | `CategoryWiseListingController@showListingsByChildCategory` | `frontend.show.listing.by.child.category` |
| GET | `/profile/{slug}` | `FrontendUserProfileController@frontendUserProfileView` | `about.user.profile` |
| GET | `/home-search/listings` | `FrontendSearchController@home_search` | `frontend.home.search` |
| GET/POST | `/login` | `LoginController@userLogin` | `user.login` |
| GET/POST | `/forget-password` | `LoginController@forgetPassword` | `user.forgot.password` |
| GET/POST | `/password-reset-otp` | `LoginController@passwordResetOtp` | `user.forgot.password.otp` |
| GET/POST | `/password-reset` | `LoginController@passwordReset` | `user.forgot.password.reset` |
| GET/POST | `/user-register` | `RegisterController@userRegister` | `user.register` |
| GET/POST | `/email-verify` | `RegisterController@emailVerify` | `email.verify` |
| GET | `/resend-verify-code-again` | `RegisterController@resendCode` | `resend.verify.code` |
| GET | `/google/redirect` | `SocialLoginController@google_redirect` | `login.google.redirect` |
| GET | `/google/callback` | `SocialLoginController@google_callback` | `google.callback` |
| GET | `/facebook/redirect` | `SocialLoginController@facebook_redirect` | `login.facebook.redirect` |
| GET | `/facebook/callback` | `SocialLoginController@facebook_callback` | `facebook.callback` |
| GET | `/membership` | — | Redirects 301 → `/explore` |
| POST | `/submit-custom-form` | `FrontendFormController@custom_form_builder_message` | `frontend.form.builder.custom.submit` |
| POST | `/favorite/listing-add-remove` | `ListingFavoriteController@listingFavoriteAddRemove` | `listing.favorite.add.remove` |
| POST | `/listing/report-add` | `ListingReportController@listingReportAdd` | `listing.report.add` |
| POST | `/user/review-add` | `UserReviewController@listingReviewAdd` | `user.review.add` |
| GET | `/{slug}` | `FrontendController@dynamic_single_page` | `frontend.dynamic.page` |
| ANY | `/admin/{any}` | — | **Aborts 404 — admin path is blocked on frontend** |

### 5.2 Authenticated User Routes — `user.php` (prefix `/user/`)

Middleware: `auth`, `userEmailVerify`, `globalVariable`, `maintains_mode`, `setlang`

| Method | URI | Controller | Route name |
|--------|-----|-----------|------------|
| GET | `/user/dashboard/info` | `DashboardController@dashboard` | `user.dashboard` |
| GET | `/user/dashboard/analytics` | `DashboardController@analytics` | `user.analytics` |
| GET | `/user/profile/settings` | `UserController@profile` | `user.profile` |
| POST | `/user/profile/edit-profile` | `UserController@edit_profile` | `user.profile.edit` |
| GET/POST | `/user/profile/identity-verification` | `UserController@identity_verification` | `user.identity.verification` |
| POST | `/user/profile/change-password` | `UserController@change_password` | `user.password` |
| GET | `/user/profile/logout` | `UserController@logout` | `user.logout` |
| GET/POST | `/user/account-settings` | `AccountSettingController@userAccountSetting` | `user.account.settings` |
| POST | `/user/account-deactive` | `AccountSettingController@accountDeactive` | `user.account.deactive` |
| POST | `/user/account/delete` | `AccountSettingController@accountDelete` | `user.account.delete` |
| GET | `/user/notification/list` | `NotificationController@index` | `user.notification.index` |
| POST | `/user/notification/read` | `NotificationController@read_notification` | `user.notification.read` |
| GET | `/user/listing/all` | `ListingController@allListing` | `user.all.listing` |
| GET/POST | `/user/listing/add` | `ListingController@addListing` | `user.add.listing` |
| GET/POST | `/user/listing/edit/{id}` | `ListingController@editListing` | `user.edit.listing` |
| POST | `/user/listing/delete/{id}` | `ListingController@deleteListing` | `user.delete.listing` |
| POST | `/user/listing/published-on-off/{id}` | `ListingController@listingPublishedStatus` | `user.listing.published.status` |
| GET | `/user/wallet/` | `WalletController@index` | `user.wallet.index` |
| GET | `/user/wallet/topup` | `WalletController@topupForm` | `user.wallet.topup` |
| POST | `/user/wallet/topup` | `WalletController@topupSubmit` | `user.wallet.topup.submit` |
| GET | `/user/wallet/paystack/callback` | `WalletController@paystackCallback` | `user.wallet.paystack.callback` |
| GET | `/user/membership/` | `MembershipController@plans` | `user.membership.plans` |
| POST | `/user/membership/subscribe` | `MembershipController@subscribe` | `user.membership.subscribe` |
| POST | `/user/membership/cancel` | `MembershipController@cancel` | `user.membership.cancel` |
| GET | `/user/featured-ads/` | `FeaturedAdController@index` | `user.featuredAds.index` |
| GET | `/user/featured-ads/packages` | `FeaturedAdController@packages` | `user.featuredAds.packages` |
| GET | `/user/my-reviews` | `UserController@myReviews` | `user.my.reviews` |
| GET | `/user/my-videos` | `UserController@myVideos` | `user.my.videos` |
| GET | `/user/blocked-users` | `UserController@blockedUsers` | `user.blocked.users` |

### 5.3 Minimal API — `api.php`

| Method | URI | Auth | Purpose |
|--------|-----|------|---------|
| GET | `/api/user` | Sanctum | Return authenticated user |
| POST | `/api/admin/badge-upload` | `X-Admin-Api-Key` header | Admin uploads badge/logo image |

---

## 6. Admin Panel Routes

Route file: `sellupnow-admin/routes/web.php`

### 6.1 Top-level

| URI | Behaviour |
|-----|-----------|
| `GET /` | Redirects to `CUSTOMER_WEB_URL` (frontend `http://127.0.0.1:8090`) |
| `GET /health` | Returns `{"status":"ok"}` — health probe |
| `GET /warning` | Redirects to `admin.login` |
| `GET /content/reel-ad-placements` | Convenience alias → `admin.reelAdPlacement.index` |

### 6.2 Auth routes (guest-only)

| Method | URI | Route name | Notes |
|--------|-----|-----------|-------|
| GET | `/admin/login` | `admin.login` | Login form |
| POST | `/admin/login` | `admin.login.submit` | Throttle: 5 req/min |
| POST | `/admin/logout` | `admin.logout` | Auth required |

### 6.3 Protected routes (middleware: `auth` + `checkPermission`)

| Section | URI prefix | Notes |
|---------|-----------|-------|
| **Dashboard** | `/admin/dashboard` | Stats, listing stats, notifications |
| **Global Search** | `/admin/global-search` | Header search across entities |
| **Categories** | `/admin/category` | Full CRUD + status toggle + menu reorder |
| **Brands** | `/admin/brand` | CRUD |
| **Listings** | `/admin/listing` | Index, details, approve/reject, feature toggle, delete |
| **Listing Reports** | `/admin/listing-report` | Review flagged listings |
| **Video Moderation** | `/admin/video-moderation` | Approve / reject uploaded reels |
| **Promo Video Ads** | `/admin/promo-video-ads` | CRUD sponsored in-feed videos |
| **Reel Ad Placements** | `/admin/content/reel-ad-placements` | CRUD overlay targeting per reel — `POST /{id}` update, `POST /{id}/delete` |
| **Advertisements** | `/admin/advertisement` | CRUD banner/overlay ad assets (image stored as attachment ID) |
| **Banner Ads** | `/admin/banner` | Admin-created banners |
| **Banner Ad Requests** | `/admin/banner-ad-requests` | User-submitted banner requests |
| **Ads Hub** | `/admin/ads-hub` | Unified ad management view |
| **Flash Sales** | `/admin/flash-sale` | CRUD flash sale events |
| **Coupons** | `/admin/coupon` | CRUD discount coupons |
| **Orders** | `/admin/order` | Order list and detail |
| **Customers** | `/admin/customer` | List users, details, ban toggle |
| **Identity Verification** | `/admin/identity-verification` | Review submitted ID proofs |
| **Membership Plans** | `/admin/membership-plan` | CRUD plans + features JSON |
| **Wallet** | `/admin/wallet` | User balance view, manual credit/debit |
| **Featured Ad Packages** | `/admin/featured-ad-package` | CRUD packages |
| **Featured Ad Reports** | `/admin/featured-ad-report` | Purchase history / reports |
| **Escrow** | `/admin/escrow` | Disputes, release funds, refund |
| **Withdraw** | `/admin/withdraw` | Pending withdrawal requests |
| **Blog** | `/admin/blog` | CRUD blog posts |
| **Pages (CMS)** | `/admin/page` | CMS page CRUD |
| **Page Builder** | `/admin/page-builder` | Homepage sections builder |
| **Homepage Hero** | `/admin/website/homepage-hero` | Edit hero banner (PageBuilder) |
| **Flash Sale Widget** | `/admin/website/flash-sale-widget` | Edit flash sale placement |
| **Menu** | `/admin/menu` | Nav menu management |
| **Footer** | `/admin/footer` | Footer links |
| **Social Links** | `/admin/social-link` | Social media URLs |
| **Countries** | `/admin/country` | CRUD countries |
| **States** | `/admin/state` | CRUD states/regions |
| **Cities** | `/admin/city` | CRUD cities |
| **Currency** | `/admin/currency` | Currency list |
| **VAT/Tax** | `/admin/vat-tax` | Tax rule management |
| **Language** | `/admin/language` | Language CRUD + translation |
| **Email Templates** | `/admin/email-template` | Editable transactional emails |
| **Mail Config** | `/admin/mail-configuration` | SMTP / Mailgun settings |
| **General Settings** | `/admin/generale-setting` | App name, logos, payment config, env editor |
| **Payment Gateways** | `/admin/payment-gateway` | Paystack, PayPal, MoMo credentials |
| **Social Auth** | `/admin/social-auth` | Google / Facebook OAuth credentials |
| **Firebase** | `/admin/firebase` | FCM / Firebase config |
| **Pusher Config** | `/admin/pusher-config` | Real-time config |
| **Google reCAPTCHA** | `/admin/google-re-captcha` | reCAPTCHA v2/v3 |
| **Map Settings** | `/admin/map-settings` | Google Maps API key |
| **WhatsApp Chat** | `/admin/whatsapp-chat` | WhatsApp widget config |
| **Support Tickets** | `/admin/support-ticket` | View + reply to user tickets |
| **Contact Messages** | `/admin/contact-us` | Contact form submissions |
| **Reviews** | `/admin/reviews` | Review moderation |
| **Report Reasons** | `/admin/report-reason` | CRUD report reason labels |
| **Notifications** | `/admin/notification` | Broadcast push notifications |
| **Roles & Permissions** | `/admin/role-permission` | Employee role management |
| **Employees** | `/admin/employee` | Staff accounts |
| **Profile** | `/admin/profile` | Admin user own profile |
| **Subscriber Plans** | `/admin/subscriber-plan` | Subscription plan visibility |
| **Chat Oversight** | `/admin/chat-oversight` | Monitor user chats |
| **Rider** | `/admin/rider` | Delivery rider management |
| **Verify Manage** | `/admin/verify-manage` | Verification badge settings |

---

## 7. Admin API Routes (Flutter App)

Route file: `sellupnow-admin/routes/api.php`  
Base URL: `http://127.0.0.1:8091/api/`

### 7.1 `/api/v1/` — Original REST namespace

**Public (no auth)**

| Method | URI | Purpose |
|--------|-----|---------|
| GET | `/api/v1/master` | System config (app settings) |
| GET | `/api/v1/home` | Homepage data |
| GET | `/api/v1/banners` | Active banners |
| GET | `/api/v1/categories` | Category tree |
| GET | `/api/v1/blogs` | Blog list |
| GET | `/api/v1/blogs/{slugOrId}` | Single blog post |
| GET | `/api/v1/products` | Product list |
| GET | `/api/v1/reviews` | Reviews list |
| POST | `/api/v1/auth/register` | Register new user |
| POST | `/api/v1/auth/login` | Login → returns token |
| POST | `/api/v1/auth/social/login` | Social OAuth login |

**Firebase-auth required** (`firebase.auth` middleware)

| Method | URI | Purpose |
|--------|-----|---------|
| POST | `/api/v1/auth/logout` | Logout |
| GET | `/api/v1/user/profile` | Own profile |
| POST | `/api/v1/user/profile` | Update profile |
| GET | `/api/v1/chat/messages` | Chat history |
| POST | `/api/v1/chat/messages` | Send message |

**Admin only** (`auth:api` + `role:root|admin`)

| Method | URI | Purpose |
|--------|-----|---------|
| GET/POST | `/api/v1/admin/users` | List / create users |
| PUT | `/api/v1/admin/users/{id}` | Update user |
| DELETE | `/api/v1/admin/users/{id}` | Delete user |
| GET | `/api/v1/admin/listings` | All listings |
| PATCH | `/api/v1/admin/listings/{id}/publish` | Toggle publish |
| GET | `/api/v1/admin/listing-reports` | Flagged listings |

### 7.2 `/api/client/` — Flutter client alias group

**Public**

| URI | Purpose |
|-----|---------|
| `GET setting/retrieveSystemConfig` | App settings |
| `GET banner/retrieveBannerList` | Banners |
| `GET category/retrieveCategoryList` | Categories |
| `GET adListing/fetchAdListingRecords` | Listing feed |
| `GET adListing/fetchAdDetailsById` | Single listing |
| `GET adVideo/getAdVideos` | Video reel feed |
| `GET user/verifyUserExistence` | Check if user exists |
| `POST user/loginOrSignupUser` | Login / register |
| `GET reportReason/fetchReportReasons` | Report reason labels |
| `GET faq/retrieveFAQList` | FAQ entries |
| `GET subscriptionPlan/fetchSubscriptionPlans` | Subscription plans |
| `GET featureAdPackage/fetchFeaturedAdPackages` | Featured ad packages |

**Firebase-auth required**

| URI | Purpose |
|-----|---------|
| `GET user/fetchUserProfile` | Own profile |
| `POST user/updateProfileInfo` | Update profile |
| `POST adListing/createAdListing` | Create listing |
| `POST adListing/updateAdListing` | Update listing |
| `POST adListing/removeAdListing` | Delete listing |
| `POST adVideo/uploadAdVideo` | Upload reel video |
| `POST adVideoLike/toggleAdVideoLike` | Like / unlike video |
| `POST videoView/recordVideoView` | Record a video view |
| `POST adView/recordAdView` | Record an ad view |
| `POST adLike/toggleAdLike` | Favourite listing |
| `GET adLike/fetchLikedAdListingRecords` | My favourites |
| `POST follow/toggleFollowStatus` | Follow / unfollow user |
| `GET follow/getSocialConnections` | Followers / following |
| `POST block/toggleBlockUser` | Block / unblock user |
| `POST report/reportAd` | Report a listing |
| `POST report/reportUser` | Report a user |
| `POST report/reportAdVideo` | Report a video |
| `POST paystack/initialize-package-payment` | Start Paystack payment |
| `GET paystack/verify-package-payment` | Verify Paystack payment |
| `POST paypal/create-order` | Create PayPal order |
| `POST paypal/capture-order` | Capture PayPal order |
| `GET wallet/getBalance` | Wallet balance |
| `GET wallet/getTransactions` | Wallet history |
| `POST withdraw/submitWithdrawRequest` | Submit withdrawal |
| `POST support/createTicket` | Open support ticket |
| `GET support/getTickets` | My tickets |
| `GET support/getTicket/{id}` | Single ticket |
| `POST support/replyTicket/{id}` | Reply to ticket |
| `GET notification/getMyNotifications` | My notifications |
| `POST notification/clearMyNotifications` | Clear notifications |
| `POST verification/submitUserVerification` | Submit ID proof |
| `POST purchaseHistory/createPurchaseHistory` | Log a purchase |
| `GET purchaseHistory/getPurchaseHistory` | Purchase history |
| `POST aiListing/suggestListingContent` | AI listing assistant |
| `POST aiRecommend/rankListings` | AI recommendations |
| `POST user/deactivateAccount` | Deactivate own account |

---

## 8. Key Boundary Rules

| Rule | Detail |
|------|--------|
| **Frontend blocks `/admin/*`** | `Route::any('/admin/{any?}', fn() => abort(404))` is the first catch-all in `routes/web.php`. Any admin URL on port 8090 returns 404. |
| **Separate auth guards** | Admin panel uses its own `auth` guard against `sellupnow_admin.users`. Frontend user sessions are in `listocean_db.users`. The two are completely separate. |
| **No reverse HTTP calls** | The frontend never calls port 8091. Data flows only via the shared `listocean_db`. |
| **Flutter talks only to admin API** | The mobile app hits `/api/client/...` and `/api/v1/...` on port 8091. It never touches port 8090. |
| **reel_type tolerance** | `reel_ad_placements` lookup key is `reel_id:placement` (reel_type excluded) to stay tolerant of admin data entry errors. |
