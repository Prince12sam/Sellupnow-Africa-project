# SellUpNow — API Reference & Changelog

> Last updated: March 8, 2026

---

## Table of Contents

1. [API Reference](#api-reference)
2. [Changelog — All Modifications](#changelog)
3. [Build & Deploy Guide](#build--deploy-guide)
4. [Security Architecture](#security-architecture)

---

## API Reference

### Base URL

```
https://www.sellupnow.com/api/
```

All endpoints are grouped under two prefixes:

| Prefix        | Purpose                                    | Auth              |
| ------------- | ------------------------------------------ | ----------------- |
| `api/client/` | Mobile Flutter app (legacy naming)         | API key + Firebase |
| `api/v1/`     | Modernised REST API (same backend)         | API key + Firebase |

### Authentication

- **API Key**: sent via `key` HTTP header. Currently in `log` mode (not enforced); switch env `API_KEY_ENFORCE=strict` to block unauthenticated requests.
- **Firebase Auth**: protected routes require `Authorization: Bearer <firebase_token>` and `x-meta-auth-id: <uid>` headers.

---

### Client API Endpoints (`api/client/`)

#### Settings & Configuration

| Method | Endpoint                                  | Auth     | Description                     |
| ------ | ----------------------------------------- | -------- | ------------------------------- |
| GET    | `/client/setting/retrieveSystemConfig`    | API Key  | App settings, payment keys, etc.|

#### User / Authentication

| Method | Endpoint                                          | Auth             | Description                     |
| ------ | ------------------------------------------------- | ---------------- | ------------------------------- |
| GET    | `/client/user/verifyUserExistence`                | API Key          | Check if phone/email exists     |
| POST   | `/client/user/loginOrSignupUser`                  | API Key          | Login or register               |
| POST   | `/client/user/initiatePasswordReset`              | API Key          | Send password reset OTP         |
| POST   | `/client/user/verifyPasswordResetOtp`             | API Key          | Verify OTP code                 |
| POST   | `/client/user/resetPassword`                      | API Key          | Reset password after OTP        |
| GET    | `/client/user/fetchUserProfile`                   | Firebase         | Get logged-in user profile      |
| POST   | `/client/user/updateProfileInfo`                  | Firebase         | Update profile                  |
| POST   | `/client/user/changePassword`                     | Firebase         | Change password                 |
| POST   | `/client/user/deactivateAccount`                  | Firebase         | Deactivate/delete account       |
| POST   | `/client/user/manageUserPermission`               | Firebase         | Toggle notification permissions |

#### Banners

| Method | Endpoint                                  | Auth     | Description                                        |
| ------ | ----------------------------------------- | -------- | -------------------------------------------------- |
| GET    | `/client/banner/retrieveBannerList`       | API Key  | List banners. `?placement=mobile_after_live_auction` |

#### Banner Ad Requests (Seller)

| Method | Endpoint                                  | Auth     | Description                     |
| ------ | ----------------------------------------- | -------- | ------------------------------- |
| GET    | `/client/bannerAd/getMyBannerAds`         | Firebase | Seller's banner ad requests     |
| POST   | `/client/bannerAd/submitBannerAd`         | Firebase | Submit new banner ad request    |

#### Categories

| Method | Endpoint                                          | Auth     | Description                     |
| ------ | ------------------------------------------------- | -------- | ------------------------------- |
| GET    | `/client/category/retrieveCategoryList`           | API Key  | All categories                  |
| GET    | `/client/category/fetchSubcategoriesByParent`     | API Key  | Subcategories by parent ID      |
| GET    | `/client/category/getHierarchicalCategories`      | API Key  | Full category tree              |
| GET    | `/client/attributes/fetchCategoryAttributes`      | API Key  | Category-specific attributes    |

#### Ad Listings

| Method | Endpoint                                          | Auth     | Description                     |
| ------ | ------------------------------------------------- | -------- | ------------------------------- |
| GET    | `/client/adListing/fetchAdListingRecords`         | API Key  | Browse all listings             |
| GET    | `/client/adListing/fetchAdDetailsById`            | API Key  | Single listing detail           |
| GET    | `/client/adListing/fetchPopularAdListingRecords`  | API Key  | Popular listings                |
| GET    | `/client/adListing/fetchMostLikedAdListings`      | API Key  | Most liked listings             |
| GET    | `/client/adListing/fetchCategoryWiseAdListings`   | API Key  | Listings by category            |
| GET    | `/client/adListing/fetchAdsByRelatedCategory`     | API Key  | Related product listings        |
| GET    | `/client/adListing/fetchAuctionAdListings`        | API Key  | Live auction listings           |
| GET    | `/client/adListing/getAdListingsOfSeller`         | API Key  | Listings by seller              |
| GET    | `/client/adListing/getSellerProductsBasicInfo`    | Firebase | Seller's products (basic info)  |
| GET    | `/client/adListing/fetchMyListings`               | Firebase | My own listings (all statuses)  |
| POST   | `/client/adListing/createAdListing`               | Firebase | Create new listing              |
| POST   | `/client/adListing/updateAdListing`               | Firebase | Update listing                  |
| POST   | `/client/adListing/removeAdListing`               | Firebase | Delete listing                  |
| POST   | `/client/adListing/promoteAds`                    | Firebase | Promote listing (featured ads)  |
| POST   | `/client/adListing/report`                        | API Key  | Report a listing                |

#### Likes / Favorites

| Method | Endpoint                                          | Auth     | Description                     |
| ------ | ------------------------------------------------- | -------- | ------------------------------- |
| POST   | `/client/adLike/toggleAdLike`                     | Firebase | Like/unlike listing             |
| GET    | `/client/adLike/fetchLikedAdListingRecords`       | Firebase | My liked listings               |
| GET    | `/client/adLike/getLikesForAd`                    | API Key  | Likes count for a listing       |

#### Ad Views

| Method | Endpoint                                  | Auth     | Description                     |
| ------ | ----------------------------------------- | -------- | ------------------------------- |
| POST   | `/client/adView/recordAdView`             | API Key  | Record a view                   |
| GET    | `/client/adView/getAdViews`               | API Key  | View count for a listing        |

#### Ad Videos

| Method | Endpoint                                          | Auth     | Description                     |
| ------ | ------------------------------------------------- | -------- | ------------------------------- |
| GET    | `/client/adVideo/getAdVideos`                     | API Key  | Browse videos (reels)           |
| GET    | `/client/adVideo/getAdVideosOfSeller`             | API Key  | Videos by seller                |
| POST   | `/client/adVideo/uploadAdVideo`                   | Firebase | Upload video                    |
| POST   | `/client/adVideo/updateAdVideo`                   | Firebase | Update video                    |
| POST   | `/client/adVideo/deleteAdVideo`                   | Firebase | Delete video                    |
| POST   | `/client/adVideoLike/toggleAdVideoLike`           | Firebase | Like/unlike video               |
| POST   | `/client/videoView/recordVideoView`               | Firebase | Record video view               |

#### Auction Bids

| Method | Endpoint                                  | Auth     | Description                     |
| ------ | ----------------------------------------- | -------- | ------------------------------- |
| POST   | `/client/auctionBid/placeManualBid`       | Firebase | Place a bid on auction listing  |

#### Chat

| Method | Endpoint                                  | Auth     | Description                     |
| ------ | ----------------------------------------- | -------- | ------------------------------- |
| GET    | `/client/chatTopic/getChatList`           | Firebase | My chat conversations           |
| GET    | `/client/chat/getChatHistory`             | Firebase | Messages in a conversation      |
| POST   | `/client/chat/sendChatMessage`            | Firebase | Send message (text/image/audio) |

#### Blog

| Method | Endpoint                                  | Auth     | Description                     |
| ------ | ----------------------------------------- | -------- | ------------------------------- |
| GET    | `/client/blog/retrieveBlogList`           | API Key  | All blog posts                  |
| GET    | `/client/blog/retrieveBlogPost`           | API Key  | Single blog post                |
| GET    | `/client/blog/retrieveTrendingBlogPosts`  | API Key  | Trending posts                  |

#### Location

| Method | Endpoint                                  | Auth     | Description                     |
| ------ | ----------------------------------------- | -------- | ------------------------------- |
| GET    | `/client/country/fetchCountryList`        | API Key  | All countries                   |
| GET    | `/client/country/fetchStatesByCountry`    | API Key  | States by country               |
| GET    | `/client/country/fetchCitiesByState`      | API Key  | Cities by state                 |
| GET    | `/client/state/fetchStateList`            | API Key  | Alias → states by country       |
| GET    | `/client/city/fetchCityList`              | API Key  | Alias → cities by state         |

#### Reviews

| Method | Endpoint                                  | Auth     | Description                     |
| ------ | ----------------------------------------- | -------- | ------------------------------- |
| GET    | `/client/review/retrieveReview`           | API Key  | Reviews for user/listing        |
| POST   | `/client/review/giveReview`               | Firebase | Submit a review                 |

#### Reports

| Method | Endpoint                                  | Auth     | Description                     |
| ------ | ----------------------------------------- | -------- | ------------------------------- |
| POST   | `/client/report/reportAd`                 | Firebase | Report a listing                |
| POST   | `/client/report/reportUser`               | Firebase | Report a user                   |
| POST   | `/client/report/reportAdVideo`            | Firebase | Report a video                  |
| GET    | `/client/reportReason/fetchReportReasons` | API Key  | Available report reasons        |

#### Social (Follow / Block)

| Method | Endpoint                                  | Auth     | Description                     |
| ------ | ----------------------------------------- | -------- | ------------------------------- |
| POST   | `/client/follow/toggleFollowStatus`       | Firebase | Follow/unfollow user            |
| GET    | `/client/follow/getSocialConnections`     | Firebase | Followers/following list        |
| POST   | `/client/block/toggleBlockUser`           | Firebase | Block/unblock user              |
| GET    | `/client/block/getBlockedUsers`           | Firebase | Blocked users list              |

#### Subscriptions & Payments

| Method | Endpoint                                              | Auth     | Description                    |
| ------ | ----------------------------------------------------- | -------- | ------------------------------ |
| GET    | `/client/subscriptionPlan/fetchSubscriptionPlans`     | API Key  | Available subscription plans   |
| GET    | `/client/featureAdPackage/fetchFeaturedAdPackages`    | API Key  | Featured ad packages           |
| POST   | `/client/purchaseHistory/createPurchaseHistory`       | Firebase | Record purchase                |
| GET    | `/client/purchaseHistory/getPurchaseHistory`           | Firebase | My purchase history            |
| POST   | `/client/paystack/initialize-package-payment`         | Firebase | Paystack payment init          |
| GET    | `/client/paystack/verify-package-payment`             | Firebase | Paystack payment verify        |
| POST   | `/client/paypal/create-order`                         | Firebase | PayPal order create            |
| POST   | `/client/paypal/capture-order`                        | Firebase | PayPal order capture           |

#### Wallet & Withdrawals

| Method | Endpoint                                      | Auth     | Description                    |
| ------ | --------------------------------------------- | -------- | ------------------------------ |
| GET    | `/client/wallet/getBalance`                   | Firebase | Wallet balance                 |
| GET    | `/client/wallet/getTransactions`              | Firebase | Transaction history            |
| GET    | `/client/withdraw/getWithdrawRequests`        | Firebase | Withdrawal requests            |
| POST   | `/client/withdraw/submitWithdrawRequest`      | Firebase | Request withdrawal             |

#### Support Tickets

| Method | Endpoint                                  | Auth     | Description                     |
| ------ | ----------------------------------------- | -------- | ------------------------------- |
| GET    | `/client/support/getTickets`              | Firebase | My support tickets              |
| POST   | `/client/support/createTicket`            | Firebase | Create support ticket           |
| GET    | `/client/support/getTicket/{id}`          | Firebase | Get ticket detail               |
| POST   | `/client/support/replyTicket/{id}`        | Firebase | Reply to ticket                 |

#### Miscellaneous

| Method | Endpoint                                          | Auth     | Description                     |
| ------ | ------------------------------------------------- | -------- | ------------------------------- |
| GET    | `/client/faq/retrieveFAQList`                     | API Key  | FAQ list                        |
| GET    | `/client/tip/listHelpfulHints`                    | API Key  | Safety tips                     |
| GET    | `/client/idProof/listIdProofs`                    | API Key  | ID proof types                  |
| POST   | `/client/verification/submitUserVerification`     | Firebase | Submit identity verification    |
| POST   | `/client/aiListing/suggestListingContent`         | Firebase | AI listing content suggestions  |
| POST   | `/client/aiRecommend/rankListings`                | Firebase | AI recommendation ranking       |

#### Health Check

| Method | Endpoint      | Auth | Description   |
| ------ | ------------- | ---- | ------------- |
| GET    | `/health`     | None | Server health |

---

### V1 API Endpoints (`api/v1/`)

The `v1` prefix contains a modernised RESTful version of the same backend plus admin endpoints.

#### V1 — Public / API Key Only

| Method | Endpoint                              | Description                    |
| ------ | ------------------------------------- | ------------------------------ |
| GET    | `/v1/master`                          | Master config                  |
| GET    | `/v1/home`                            | Home screen aggregate          |
| GET    | `/v1/home/popular-products`           | Popular products               |
| GET    | `/v1/banners`                         | Banner list                    |
| GET    | `/v1/countries`                       | Country list                   |
| GET    | `/v1/categories`                      | Category list                  |
| GET    | `/v1/categories/attributes`           | Category attributes            |
| GET    | `/v1/blogs`                           | Blog list                      |
| GET    | `/v1/blogs/{slugOrId}`                | Single blog                    |
| GET    | `/v1/products`                        | Product list                   |
| GET    | `/v1/products/show`                   | Product detail                 |
| GET    | `/v1/reviews`                         | Reviews                        |
| POST   | `/v1/auth/register`                   | Register                       |
| POST   | `/v1/auth/login`                      | Login                          |
| POST   | `/v1/auth/social/login`               | Social login                   |
| POST   | `/v1/auth/forgot-password/resend-otp` | Resend OTP                     |
| POST   | `/v1/auth/forgot-password/verify-otp` | Verify OTP                     |
| POST   | `/v1/auth/forgot-password/reset-password` | Reset password             |

#### V1 — Firebase Auth Required

| Method | Endpoint                         | Description                    |
| ------ | -------------------------------- | ------------------------------ |
| POST   | `/v1/auth/logout`                | Logout                         |
| GET    | `/v1/user/profile`               | Get profile                    |
| POST   | `/v1/user/profile`               | Update profile                 |
| POST   | `/v1/user/change-password`       | Change password                |
| POST   | `/v1/products/favorite`          | Add to favorites               |
| GET    | `/v1/products/favorites`         | My favorites                   |
| POST   | `/v1/reviews`                    | Submit review                  |
| GET    | `/v1/chat/shops`                 | Chat shops                     |
| GET    | `/v1/chat/messages`              | Chat messages                  |
| POST   | `/v1/chat/messages`              | Send message                   |
| GET    | `/v1/chat/unread`                | Unread message count           |

#### V1 — Admin Endpoints (`api/v1/admin/`)

Requires `auth:api` + `role:root|admin`.

| Method | Endpoint                                  | Description                    |
| ------ | ----------------------------------------- | ------------------------------ |
| GET    | `/v1/admin/users`                         | List users                     |
| POST   | `/v1/admin/users`                         | Create user                    |
| PUT    | `/v1/admin/users/{id}`                    | Update user                    |
| DELETE | `/v1/admin/users/{id}`                    | Delete user                    |
| POST   | `/v1/admin/users/{id}/permissions`        | Update user permissions        |
| POST   | `/v1/admin/users/{id}/reset-password`     | Reset user password            |
| GET    | `/v1/admin/roles`                         | List roles                     |
| POST   | `/v1/admin/roles`                         | Create role                    |
| PUT    | `/v1/admin/roles/{id}`                    | Update role                    |
| DELETE | `/v1/admin/roles/{id}`                    | Delete role                    |
| POST   | `/v1/admin/roles/{id}/permissions`        | Update role permissions        |
| GET    | `/v1/admin/listings`                      | List all listings              |
| GET    | `/v1/admin/listings/{id}`                 | Show listing                   |
| PUT    | `/v1/admin/listings/{id}`                 | Update listing                 |
| DELETE | `/v1/admin/listings/{id}`                 | Delete listing                 |
| PATCH  | `/v1/admin/listings/{id}/publish`         | Publish/unpublish listing      |
| GET    | `/v1/admin/report-reasons`                | List report reasons            |
| POST   | `/v1/admin/report-reasons`                | Create report reason           |
| PUT    | `/v1/admin/report-reasons/{id}`           | Update report reason           |
| DELETE | `/v1/admin/report-reasons/{id}`           | Delete report reason           |
| GET    | `/v1/admin/listing-reports`               | List listing reports           |
| GET    | `/v1/admin/listing-reports/{id}`          | Show listing report            |
| PUT    | `/v1/admin/listing-reports/{id}`          | Update listing report          |
| DELETE | `/v1/admin/listing-reports/{id}`          | Delete listing report          |

---

## Changelog

All modifications made during this development session (March 2026).

---

### 1. Banner Placement System (Mobile After Live Auction)

**Problem:** Banners were hardcoded to homepage only. Mobile app needed a banner carousel after the Live Auction section, admin-controlled.

**Files Modified:**

| File | Change |
| ---- | ------ |
| `sellupnow-admin/app/Models/Banner.php` | Added placement constants (`PLACEMENT_HOMEPAGE`, `PLACEMENT_MOBILE_AFTER_LIVE_AUCTION`), `PLACEMENT_OPTIONS` array, `isHomepagePlacement()` helper, fixed `thumbnail` accessor to resolve from `public` disk |
| `sellupnow-admin/app/Http/Requests/BannerRequest.php` | Added `placement` validation rule |
| `sellupnow-admin/app/Repositories/BannerRepository.php` | Persist `placement` on create/update |
| `sellupnow-admin/app/Http/Controllers/Admin/BannerController.php` | Placement-aware sync logic (homepage sync only applies to homepage banners) |
| `sellupnow-admin/resources/views/admin/banner/create.blade.php` | Added placement dropdown selector |
| `sellupnow-admin/resources/views/admin/banner/edit.blade.php` | Added placement dropdown selector |
| `sellupnow-admin/app/Http/Controllers/API/BannerController.php` | Placement-aware filtering (`?placement=mobile_after_live_auction`), multi-banner support, backward-compatible fallback |
| `sellupnow-admin/app/Http/Resources/BannerResource.php` | Created — Flutter-friendly keys (`_id`, `image`, `redirectUrl`, `isActive`, `placement`, timestamps) |
| `sellupnow-admin/database/migrations/2026_03_08_000001_add_placement_to_banners_table.php` | Created — adds `placement` column with index to `banners` table |

**Flutter App Files Modified:**

| File | Change |
| ---- | ------ |
| `Flutter App/lib/ui/home_screen/api/banner_api.dart` | Added optional `placement` query parameter support |
| `Flutter App/lib/ui/home_screen/controller/home_screen_controller.dart` | Requests `placement: mobile_after_live_auction` |
| `Flutter App/lib/ui/home_screen/widget/home_screen_widget.dart` | Carousel autoplay/infinite only when >1 banner |

---

### 2. Banner Image Fix

**Problem:** Banner images showed default placeholder even when image was uploaded via admin.

**Root Cause:** The `thumbnail` accessor on `Banner` model checked wrong storage disk.

**Fix:** Updated `Banner.php` model to resolve image URLs from `Storage::disk('public')` instead of the default disk.

---

### 3. Seller Banner Ad Request — Mobile Slot

**Problem:** Sellers couldn't request banner ad placement for the mobile "after live auction" slot.

**Files Modified:**

| File | Change |
| ---- | ------ |
| `sellupnow-admin/app/Http/Controllers/Admin/BannerAdRequestsController.php` | Added `mobile_live_after_auction` slot option |
| `sellupnow-admin/app/Http/Controllers/API/BannerAdApiController.php` | Added `mobile_live_after_auction` slot option |
| `Flutter App/lib/ui/banner_ad_screen/controller/banner_ad_screen_controller.dart` | Added mobile live slot option in Flutter UI |

---

### 4. API Key Security Middleware

**Problem:** All API endpoints were publicly accessible with no authentication beyond Firebase for protected routes.

**Implementation:**

| File | Change |
| ---- | ------ |
| `sellupnow-admin/app/Http/Middleware/ValidateApiKey.php` | Created — validates `key` header against configured secrets. Supports two modes: `log` (default, logs failures but allows through) and `strict` (returns 401). Uses `hash_equals()` for timing-safe comparison |
| `sellupnow-admin/app/Http/Kernel.php` | Registered `api.key` middleware alias |
| `sellupnow-admin/config/app.php` | Added `api_secret_key`, `api_secret_keys` (rotation support, comma-separated), and `api_key_enforce` config entries |
| `sellupnow-admin/routes/api.php` | Applied `api.key` middleware to both `v1` and `client` route groups |

**Environment Variables:**

```env
API_SECRET_KEY=<your-secret-key>
API_SECRET_KEYS=<key1>,<key2>          # For key rotation
API_KEY_ENFORCE=log                     # "log" or "strict"
```

**Current Status:** Set to `log` mode. All 78 Flutter API files already send `Api.secretKey` via the `key` header. Once all users install the new APK (built with the key), switch to `strict`.

---

### 5. Flutter APK Build with API Key

**Problem:** The installed app was built without `--dart-define=API_SECRET_KEY`, so it sent empty key headers.

**Solution:** Built new APK with both defines:

```powershell
flutter build apk --release `
  --dart-define=API_BASE_URL=https://www.sellupnow.com/ `
  --dart-define=API_SECRET_KEY=<key>
```

**APK Location:** `Flutter App/build/app/outputs/flutter-apk/app-release.apk` (98.7 MB, debug-signed for testing)

**Note:** Production Play Store release requires the original `upload-keystore.jks` signing key in `Flutter App/android/app/`.

---

### 6. Production Database Migration

**Migration Applied:** `2026_03_08_000001_add_placement_to_banners_table.php`

- Adds `placement` VARCHAR(64) column to `banners` table
- Default value: `homepage`
- Indexed for query performance

---

### 7. Cleanup

- Removed 15 leftover Python debug/fix scripts (`check_db*.py`, `fix_*.py`, `check_*.py`, `patch_*.py`) that contained hardcoded database credentials

---

## Build & Deploy Guide

### Flutter APK Build

```powershell
cd "Flutter App"
flutter build apk --release \
  --dart-define=API_BASE_URL=https://www.sellupnow.com/ \
  --dart-define=API_SECRET_KEY=<key-from-server-env>
```

For Play Store release, ensure `upload-keystore.jks` is in `Flutter App/android/app/` and `key.properties` has correct passwords.

### Backend Deployment (VPS)

```bash
# Upload changed files
scp <file> root@76.13.211.92:/home/sellupnow/htdocs/www.sellupnow.com/sellupnow-admin/<path>

# Clear caches and rebuild
ssh root@76.13.211.92 "cd /home/sellupnow/htdocs/www.sellupnow.com/sellupnow-admin \
  && php artisan optimize:clear \
  && php artisan config:cache \
  && php artisan route:cache"
```

### Running Migrations

```bash
ssh root@76.13.211.92 "cd /home/sellupnow/htdocs/www.sellupnow.com/sellupnow-admin \
  && php artisan migrate --force"
```

---

## Security Architecture

### API Key Flow

```
Flutter App                          Laravel Backend
    │                                      │
    │  GET /api/client/...                │
    │  Header: key = <API_SECRET_KEY>     │
    │─────────────────────────────────────>│
    │                                      │
    │                          ValidateApiKey middleware
    │                          ┌─────────────────────┐
    │                          │ Read key header      │
    │                          │ Compare vs config    │
    │                          │ hash_equals()        │
    │                          │                      │
    │                          │ if enforce=strict:   │
    │                          │   invalid → 401      │
    │                          │ if enforce=log:      │
    │                          │   invalid → log+pass │
    │                          │ valid → pass through │
    │                          └─────────────────────┘
    │                                      │
    │            200 / response            │
    │<─────────────────────────────────────│
```

### Key Rotation

To rotate keys without breaking existing apps:

1. Generate new key
2. Add both old and new to `API_SECRET_KEYS=<new>,<old>` in `.env`
3. Build and deploy new app with new key
4. After all users update, remove old key from `API_SECRET_KEYS`

### Switching to Strict Mode

Once all app installs use the embedded API key:

```bash
# On production server
ssh root@76.13.211.92
cd /home/sellupnow/htdocs/www.sellupnow.com/sellupnow-admin

# Edit .env
sed -i 's/API_KEY_ENFORCE=log/API_KEY_ENFORCE=strict/' .env

# Rebuild config cache
php artisan config:cache
```

### Production Logs

Logs are daily-rotated at:
```
storage/logs/laravel-YYYY-MM-DD.log
```

To check API key failures:
```bash
grep "API key validation failed" storage/logs/laravel-$(date +%Y-%m-%d).log
```

---

## Session 2 — Bug Fixes, Escrow, Profile & Video Improvements

All modifications below were made during the second development session.

---

### 8. Phone Country Code Fix

**Problem:** The phone field on the Edit Profile screen was always locked (read-only). Users could not update their phone number or country code.

**Root Cause:** `UserResource.php` returned `loginType: 1` for all users because `auth_type` is always `null` in the database. The Flutter app interprets `loginType == 1` as "phone OTP login" and locks the phone field with `AbsorbPointer` + `readOnly`.

**Files Modified:**

| File | Change |
| ---- | ------ |
| `sellupnow-admin/app/Http/Resources/UserResource.php` | Replaced broken `$this->auth_type ? 2 : 1` with `resolveLoginType()` method. Returns `4` for email/password users (has `password` hash), `2` for social auth (has `auth_type`), `1` for phone OTP (phone verified but no email), `0` fallback |
| `Flutter App/lib/ui/dashboard_screen/profile_screen/edit_profile_screen/api/edit_profile_api.dart` | Changed HTTP method from `PATCH` to `POST` (matches Laravel route). Changed field name from `phoneNumber` to `phone` (matches Laravel validation) |

---

### 9. Profile API Fixes

**Problem:** Several profile fields were silently dropped on update — address, profile image, notifications preference, and contact info visibility.

**Files Modified:**

| File | Change |
| ---- | ------ |
| `sellupnow-admin/app/Http/Requests/UserRequest.php` | Made `phone` nullable (was `required`). Added `profileImage` validation rule alongside existing `profile_photo` |
| `sellupnow-admin/app/Repositories/UserRepository.php` | Added `address` to the `updateByRequest()` update array. Updated `updateProfilePhoto()` to accept both `profileImage` and `profile_photo` file keys |
| `sellupnow-admin/app/Http/Resources/UserResource.php` | Added `isNotificationsAllowed` and `isContactInfoVisible` fields to API response |

---

### 10. Video Performance Optimization

**Problem:** The Videos (Reels) screen froze on load. All video players were initialized simultaneously, causing massive memory usage and UI hang.

**Solution:** Lazy initialization — only the current video + immediate neighbors are initialized. Players are disposed when scrolled far away. Backend pagination was also added.

**Files Modified:**

| File | Change |
| ---- | ------ |
| `Flutter App/lib/ui/videos_screen/controller/videos_screen_controller.dart` | Rewrote to lazy-init architecture: `_initializePlayer(i)` creates controllers on-demand, `_ensureInitialized(index)` pre-loads ±2 neighbors, `_disposeDistant(index)` frees players >3 slots away. Added `controllersReady` flag for UI gating |
| `Flutter App/lib/ui/videos_screen/widget/videos_screen_widget.dart` | Added `GetBuilder` wrapper gated on `controllersReady` to prevent premature rendering |
| `sellupnow-admin/app/Http/Controllers/API/AdVideoController.php` | Added SQL pagination (`LIMIT/OFFSET`) to `getAdVideos()` — response time dropped from ~2s to ~46ms |
| Database | Added indexes on `ad_videos(user_id)`, `ad_videos(ad_listing_id)`, `ad_video_likes(ad_video_id, user_id)` |

---

### 11. Video Display Bug Fix

**Problem:** After the lazy-init refactor, the video screen showed a blank/black area because the `GetBuilder` had no `id` parameter, so the `update()` call from the controller didn't reach the correct widget.

**Fix:** Aligned the `update()` call in the controller (no ID) with the `GetBuilder` widget (no ID) so they use the same default rebuild channel.

---

### 12. HTML Stripping in Video Descriptions

**Problem:** Video descriptions showed raw HTML tags (e.g., `<p>Great product</p>`) from the admin rich-text editor.

**Fix:** Added `_stripHtml()` utility in the video card widget to strip HTML tags before rendering descriptions.

---

### 13. Upload Video — Product Selection Fix

**Problem:** When uploading a video, the "Select Product" dropdown was empty. The seller couldn't link a video to their product.

**Root Cause:** `sellerProductsBasicInfo()` in `ListingController.php` returned the wrong data shape and field names. The Flutter app expected `_id`, `title`, `subTitle`, `primaryImage` but the API returned different keys.

**Files Modified:**

| File | Change |
| ---- | ------ |
| `sellupnow-admin/app/Http/Controllers/API/ListingController.php` | Fixed `sellerProductsBasicInfo()` to return flat array with correct field names: `_id`, `title`, `subTitle`, `primaryImage` (via `$listing->thumbnail`), `price`. Wrapped in `status: true` response |

---

### 14. Escrow Payment System (Flutter)

**Problem:** The web frontend had a full escrow system, but the Flutter mobile app had no way for buyers to purchase products using escrow.

**Implementation:** Added end-to-end escrow flow — buyer sees price breakdown, confirms payment from wallet, funds are held in escrow until delivery is confirmed.

**Backend Files (all deployed):**

| File | Change |
| ---- | ------ |
| `sellupnow-admin/app/Http/Resources/ListingDetailsResource.php` | Added `escrowEnabled` boolean field to listing detail API response |
| `sellupnow-admin/routes/api.php` | Added 4 escrow routes under `api/client/escrow/`: `GET getOrders`, `GET getOrderDetail`, `GET getBreakdown`, `POST initiateEscrow` |
| `sellupnow-admin/app/Http/Controllers/API/EscrowApiController.php` | Fixed DB connection (removed wrong `DB::connection('listocean')`). Added `getBreakdown()` — returns listing price, platform fee (via `CommissionService`), total, wallet balance, `canAfford` flag. Added `initiateEscrow()` — validates listing, checks balance, debits wallet in DB transaction, creates `escrow_transactions` record, logs `escrow_events` entry |

**Flutter Files (new/modified):**

| File | Change |
| ---- | ------ |
| `Flutter App/lib/services/api/api.dart` | Added `escrowBreakdown` and `escrowInitiate` endpoint constants |
| `Flutter App/lib/ui/product_detail/model/product_detail_response_model.dart` | Added `bool? escrowEnabled` field to `Product` class |
| `Flutter App/lib/ui/product_detail/api/escrow_api.dart` | **New file** — `callBreakdownApi(listingId)` (GET) and `callInitiateApi(listingId)` (POST) |
| `Flutter App/lib/ui/product_detail/model/escrow_response_model.dart` | **New file** — `EscrowBreakdownResponse` (price, fee, total, balance, canAfford) and `EscrowInitiateResponse` (status, message, escrowId) |
| `Flutter App/lib/ui/product_detail/widget/escrow_bottom_sheet.dart` | **New file** — Bottom sheet UI showing price breakdown, wallet balance, and "Pay Securely" button. Handles loading state, insufficient balance warning, success/error toasts |
| `Flutter App/lib/ui/product_detail/widget/product_detail_screen_widget.dart` | Added "Buy with Escrow" button in `DetailBottomView` — appears for non-owner users when `escrowEnabled == true`. Opens `EscrowBottomSheet` via `Get.bottomSheet()` |

**Database Tables Used:**

| Table | Purpose |
| ----- | ------- |
| `escrow_transactions` | Holds escrow records: listing, buyer, seller, amounts, status (pending → released/disputed/refunded) |
| `escrow_events` | Audit log of all escrow state changes |
| `wallets` | User wallet balances (debited on escrow initiation) |
| `transactions` | Wallet transaction history |

**Escrow API Endpoints:**

| Method | Endpoint | Auth | Description |
| ------ | -------- | ---- | ----------- |
| GET | `/client/escrow/getBreakdown` | Firebase | Price breakdown for a listing (price + platform fee + total + wallet balance) |
| POST | `/client/escrow/initiateEscrow` | Firebase | Create escrow transaction, debit buyer wallet |
| GET | `/client/escrow/getOrders` | Firebase | List user's escrow orders |
| GET | `/client/escrow/getOrderDetail` | Firebase | Single escrow order detail |

---

### 15. Keystore Generation

Generated `upload-keystore.jks` for signing release APKs:
- **Location:** `Flutter App/android/app/upload-keystore.jks`
- **Alias:** `upload`
- **Validity:** 10,000 days
- **Key properties** configured in `Flutter App/android/key.properties`

---

### 16. Cleanup — Debug Print Removal

Removed `print('[VIDEO_DEBUG]...')` debug statements from `videos_screen_controller.dart` before production build.

---

### 17. Profile Features Audit

Performed comprehensive comparison of Flutter mobile app profile features vs. web frontend. Key findings:

**Features in Flutter missing from Web (15):**
- Switch to selling/buying, Tawk.to live chat, app share, rate app, dynamic theme switching, multiple profile photo sources, biometric login, app language selector, local notifications, font size, RTL support, cache management, login-type-based field locking, structured verification flow, social connection counts on profile

**Features in Web missing from Flutter (2):**
- Change password (email/password users)
- Web push notification management

**Priority recommendation:** Implement Change Password in Flutter as highest-priority gap.
