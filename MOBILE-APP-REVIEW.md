# Mobile App Review — Sellupnow Flutter Application

> **Review Date:** March 6, 2026  
> **Framework:** Flutter (GetX state management)  
> **Package ID:** `com.company.sellupnow`  
> **Internal package namespace:** `package:listify/`

---

## Table of Contents

1. [Application Overview](#1-application-overview)
2. [Architecture](#2-architecture)
3. [Features & Screens](#3-features--screens)
4. [Dependencies / Plugins](#4-dependencies--plugins)
5. [Payment Integrations](#5-payment-integrations)
6. [Security Issues](#6-security-issues-critical)
7. [Bugs](#7-bugs)
8. [Code Quality & Architecture Issues](#8-code-quality--architecture-issues)
9. [Strengths](#9-strengths)
10. [Issue Summary Table](#10-issue-summary-table)
11. [Recommended Fix Order](#11-recommended-fix-order)

---

## 1. Application Overview

Sellupnow is a classifieds/marketplace mobile application enabling users to:

- Post, browse, and manage product listings (ads)
- Participate in live auctions with bidding
- Watch and upload short video reels tied to listings
- Chat in real-time with sellers/buyers (Socket.IO)
- Purchase subscription plans and featured ad packages
- Verify their identity with ID proof uploads
- Manage a wallet, submit withdrawal requests, and view transaction history
- Submit and track support tickets
- Follow sellers and browse nearby listings on a map

The app targets both **Android** and **iOS**.

---

## 2. Architecture

### State Management
The app uses **GetX** throughout:
- Every screen follows the **Controller / Binding / View** separation
- Controllers are injected lazily via `Binding` classes registered in `AppPages`
- Permanent controllers (`LikeManager`, `MapController`) are registered in `main()` at startup

### Routing
Centralised in two files:
| File | Purpose |
|---|---|
| `lib/routes/app_routes.dart` | Defines all named route string constants |
| `lib/routes/app_pages.dart` | Maps each route to a `GetPage` with its view and binding |

Approximately **65 named routes** are registered.

### Navigation Flow
```
SplashScreen → OnBoarding (first launch)
             → BottomBar (logged-in)
             → LoginScreen → OTP / Register → FillProfile → BottomBar
```

### Local Storage
`GetStorage` is used as the local key-value store, wrapped by the `Database` class (`lib/utils/database.dart`). It persists:
- Auth tokens (Firebase UID, FCM token, device identity)
- User profile model
- Selected location and radius
- Language / locale preferences
- Notification preferences
- Onboarding seen flag

### Networking
All API base URLs and API secret keys are injected at build time via `--dart-define`:
```dart
// lib/utils/api.dart
static const baseUrl = String.fromEnvironment('API_BASE_URL', defaultValue: "http://127.0.0.1:8098/");
static const secretKey = String.fromEnvironment('API_SECRET_KEY', defaultValue: "");
```

### Real-Time Chat
Socket.IO (`socket_io_client`) connects on login using the user's ID as a room identifier. Events are defined in `lib/utils/socket_events.dart` and `socket_params.dart`.

### Localisation
18 languages are supported via GetX translations (`AppLanguages`):
Arabic, Bengali, Chinese, English, French, German, Hindi, Indonesian, Italian, Korean, Portuguese, Russian, Spanish, Swahili, Tamil, Telugu, Turkish, Urdu.

---

## 3. Features & Screens

| Screen | Route Constant | Notes |
|---|---|---|
| Splash | `splashScreenView` | Entry point; decides login vs onboarding |
| Onboarding | `onBoardingScreenView` | Shown on first launch only |
| Login | `loginScreen` | Email/password + Google Sign-In |
| Mobile Login | `mobileLogIn` | Phone number entry |
| Verify OTP | `verifyOtp` | |
| Register | `register` | |
| Fill Profile | `fillProfileScreen` | Mandatory after first sign-up |
| Forgot Password | `forgotPasswordScreen` | |
| Bottom Navigation Bar | `bottomBar` | Hosts Home, My Ads, Add, Messages, Videos tabs |
| Home | `homeScreen` | Banners, categories, popular/auction listings |
| Categories | `categoriesScreen` | |
| Sub-categories | `subCategoriesScreen` | |
| Sub-category Products | `subCategoryProductScreen` | |
| Product Filter | `productFilterScreen` | Sort, price range, location filter |
| Product Detail | `productDetailScreen` | Full listing detail, offer, bid, chat |
| Add Product / Listing | `addProductScreen`, `addListingScreen` | AI listing assistant integrated |
| Edit Product | `editProductView` | |
| Upload Images | `uploadImageScreenView` | |
| Product Pricing | `productPricingScreen` | |
| My Ads | `myAdsScreen` | |
| Featured Ads | `featuredAdsScreen`, `featuredAdsShowScreen` | Promote listings |
| Popular Products | `popularProductScreen` | |
| Most Liked | `mostLikedViewAllScreen` | |
| Home Search | `homeScreenProductScreenView` | |
| Near By Listing (Map) | `nearByListingScreen` | Google Maps integration |
| Location / State / City | `locationScreen`, `selectStateScreen`, `selectCityScreen` | |
| Confirm Location | `confirmLocationScreen` | |
| Favourites | `favoriteScreen` | |
| Live Auction | `liveAuctionScreen` | |
| Videos (Reels) | `videosScreen` | |
| My Videos | `myVideosScreen` | |
| Upload Video | `uploadVideoScreen`, `uploadVideoDetailScreen` | |
| Messages | `messageScreen` | Chat list |
| Chat Detail | `chatDetailScreenView` | Real-time Socket.IO chat |
| Profile | `profileScreenView` | |
| Edit Profile | `editProfileView` | |
| Seller Detail | `sellerDetailScreenView` | |
| Seller All Products | `sellerDetailProductAllView` | |
| Reviews | `reviewScreenView` | |
| Subscription Plans | `subscriptionPlanScreen` | |
| Transaction History | `transactionHistoryScreenView` | |
| Wallet | `walletScreen` | Balance + transaction list |
| Withdraw | `withdrawScreen` | Submit withdrawal request |
| Support Tickets | `supportTicketScreen` | |
| Support Ticket Detail | `supportTicketDetailScreen` | Reply thread |
| Notifications | `notificationScreenView` | |
| User Verification | `userVerificationView`, `userVerificationScreenView` | ID proof upload |
| Language | `languageScreenView` | |
| Blog | `blogScreen`, `fashionBlogScreen` | |
| FAQ | `faqScreen` | |
| Contact Us | `contactUsScreen` | |
| About Us | `aboutUsScreen` | |
| Block List | `blockScreenView` | |
| Specific Ad Likes/Views | `specifAdLikeShowScreen`, `specifAdViewShowScreen` | Analytics |

---

## 4. Dependencies / Plugins

Key Android/iOS plugins confirmed via `.flutter-plugins-dependencies`:

| Plugin | Purpose |
|---|---|
| `firebase_core` | Firebase initialisation |
| `firebase_auth` | Authentication (Google + email) |
| `firebase_messaging` | Push notifications (FCM) |
| `firebase_crashlytics` | Crash reporting |
| `flutter_local_notifications` | Local notification display |
| `google_maps_flutter_android` | Map view for nearby listings |
| `geolocator_android` / `geocoding_android` | GPS location + reverse geocoding |
| `socket_io_client` | Real-time chat |
| `razorpay_flutter` | Razorpay payments |
| `stripe_android` | Stripe payments |
| `flutter_cashfree_pg_sdk` | Cashfree payments |
| `in_app_purchase_android` | Google Play In-App Purchases |
| `google_sign_in_android` | Google OAuth login |
| `image_picker_android` | Photo/camera picker |
| `file_picker` | File/document picker |
| `video_player_android` / `video_compress` / `video_thumbnail` | Video reels |
| `record_android` | Audio recording (voice messages) |
| `flutter_inappwebview_android` | Web views (PayPal, blog, etc.) |
| `permission_handler_android` | Runtime permissions |
| `get_storage` | Local key-value storage |
| `mobile_device_identifier` | Unique device ID |
| `sqflite_android` | SQLite (likely for offline data) |
| `url_launcher_android` | Open external URLs |
| `wakelock_plus` | Prevent screen sleep during video |

---

## 5. Payment Integrations

| Gateway | File | Status | Notes |
|---|---|---|---|
| **Stripe** | `payment/stripe/stripe_service.dart` | ✅ Production-ready pattern | Payment intent created server-side; client uses `flutter_stripe` sheet |
| **Razorpay** | `payment/razor_pay/razor_pay_service.dart` | ⚠️ Bug (see §7) | Hardcoded test key `rzp_test_SjZz9HC7RGCfCb` in checkout options |
| **PayPal** | `payment/paypal/paypal_package_api.dart` | Server-side create/capture | Uses WebView for approval flow |
| **Paystack** | `payment/paystack/paystack_package_api.dart` | Initialize + verify pattern | Via backend proxy |
| **Flutterwave** | `payment/flutter_wave/flutter_wave_services.dart` | — | |
| **PhonePe** | `payment/phone_pay/phone_pay_service.dart` | ⛔ Placeholder only | `merchantId`, `saltKey` are `"YOUR_..."` literals; checksum is a stub |
| **In-App Purchase** | `payment/in_app_purchase/` | iOS + Android IAP | Receipt validation helper included |

---

## 6. Security Issues *(Critical)*

### S-1 · Plaintext HTTP default base URL
**File:** `lib/utils/api.dart`  
**Severity:** High

```dart
static const baseUrl = String.fromEnvironment(
  'API_BASE_URL',
  defaultValue: "http://127.0.0.1:8098/",  // ← HTTP, localhost
);
```

If a production build is ever compiled without `--dart-define=API_BASE_URL=https://...`, all network traffic will go to localhost over unencrypted HTTP, either failing silently or exposing auth tokens if pointed to a non-TLS server.

**Fix:** Change `defaultValue` to an empty string and add a startup assertion:
```dart
static const baseUrl = String.fromEnvironment('API_BASE_URL', defaultValue: "");
// In main(), before runApp:
assert(Api.baseUrl.isNotEmpty && Api.baseUrl.startsWith('https://'), 'API_BASE_URL must be set to an HTTPS URL');
```

---

### S-2 · IP Geolocation API over HTTP
**File:** `lib/utils/api.dart`  
**Severity:** Medium

```dart
static const ipApi = "http://ip-api.com/json";  // ← HTTP
```

The user's IP address and derived location are transmitted and received over an unencrypted channel, making them vulnerable to interception or MITM response spoofing.

**Fix:**
```dart
static const ipApi = "https://ip-api.com/json";
```

---

### S-3 · Stripe payment errors silently swallowed
**File:** `lib/payment/stripe/stripe_service.dart`  
**Severity:** High

```dart
} catch (e) {
  log('Error Charging User: ${e.toString()}');
  // ← returns null, no rethrow
}
```

`stripePay()` returns `null` on any failure (network error, declined card, user cancel). Callers that check for a non-null return to grant access/credits will incorrectly succeed if they don't handle `null` explicitly.

**Fix:** Rethrow the exception so callers can handle failures correctly:
```dart
} catch (e) {
  log('Error Charging User: ${e.toString()}');
  rethrow;
}
```

---

### S-4 · Razorpay test key hardcoded in production code
**File:** `lib/payment/razor_pay/razor_pay_service.dart`  
**Severity:** High

```dart
'key': "rzp_test_SjZz9HC7RGCfCb",  // ← hardcoded test key, ignores injected razorKey
```

The `init()` method accepts a `razorKey` parameter (intended to come from the server settings API), but `razorPayCheckout()` ignores it and uses this hardcoded test key. No live payments will be processed.

**Fix:**
```dart
'key': razorKeys,  // use the key passed to init()
```

---

### S-5 · PhonePe checksum is a stub — payments not verified
**File:** `lib/payment/phone_pay/phone_pay_service.dart`  
**Severity:** Critical (if PhonePe is live)

```dart
String generateChecksum(String payload) {
  return "CHECKSUM###$saltIndex";  // ← not a real HMAC checksum
}
```

The PhonePe integration also has placeholder credentials:
```dart
final String merchantId = "YOUR_MERCHANT_ID";
final String saltKey = "YOUR_SALT_KEY";
```

This entire service must not be reachable in production until properly implemented.

---

## 7. Bugs

### B-1 · `Database.setSelectedLocationText` never called
**File:** `lib/main.dart`  
**Severity:** Medium

```dart
await Database.initSelectedLocation();
Database.setSelectedLocationText;  // ← missing (), this is a no-op reference
```

The getter is referenced but not invoked. Any initial location text setup that function performs is skipped on every app launch.

**Fix:** Add parentheses (or remove if the call is not needed):
```dart
Database.setSelectedLocationText();
```

---

### B-2 · FCM token null causes entire `Database.init()` to be skipped
**File:** `lib/main.dart`  
**Severity:** Medium

```dart
if (fcmToken != null) {
  await Database.init(identity, fcmToken);
}
await Database.initSelectedLocation();
```

If FCM token retrieval fails (network unavailable, Play Services issue), `Database.init()` is skipped. User session, stored profile, and preferences are never loaded, causing downstream null-reference errors and showing the splash screen indefinitely or crashing.

**Fix:** Call `Database.init()` unconditionally, passing an empty string for the token:
```dart
await Database.init(identity, fcmToken ?? "");
```

---

### B-3 · `fontStyleW600` uses W500 weight (copy-paste error)
**File:** `lib/utils/font_style.dart`  
**Severity:** Low

```dart
static fontStyleW600(...) {
  return TextStyle(
    fontWeight: FontWeight.w500,        // ← should be w600
    fontFamily: "AirbnbCereal_W_Md",   // ← should be W_Lt or Bd depending on intent
    ...
  );
}
```

`fontStyleW600` is identical to `fontStyleW500`. Any UI element using `W600` will render at medium weight instead of semi-bold, causing unintended visual consistency issues.

---

## 8. Code Quality & Architecture Issues

### Q-1 · Package identity mismatch (`listify` vs `sellupnow`)
All Dart `import` paths use `package:listify/...` but the app bundle ID is `com.company.sellupnow` and the `GetMaterialApp` title is `'Sellupnow'`. The commented-out legacy `main()` still says `'Listify App'`. This suggests the app was rebranded from "Listify" but the internal package name was never updated, which:
- Makes code searches confusing
- Creates misleading crash reports (Crashlytics shows `listify`)
- Will cause issues if the package is ever published to pub.dev

---

### Q-2 · Dead code block in `main.dart`
**File:** `lib/main.dart`  
The original ~80-line `main()`, all its imports, and the old `MyApp` class are preserved as a large block comment at the top of the file. This creates noise and increases the risk of accidentally uncommenting stale code. It should be deleted (or moved to git history).

---

### Q-3 · Duplicate permission request on startup
**File:** `lib/main.dart`  
Permission flow:
1. `NotificationServices.init()` is called directly — this calls `messaging.requestPermission()` inside it.
2. `PermissionHandler.notificationPermissions()` is called inside `addPostFrameCallback` — this also requests notification permission.

Android 13+ users will see two overlapping permission dialogs on first launch. The two calls should be consolidated into one.

---

### Q-4 · Global mutable socket variable
**File:** `lib/socket/socket_service.dart`

```dart
io.Socket? socket;  // top-level global
```

The socket object is a bare module-level global. If `socketConnect()` is called a second time (re-login, background/foreground cycle), a new socket is created but the old one is never disposed — leaking the connection. Should be managed inside a singleton `GetxService` with proper lifecycle hooks.

---

### Q-5 · `LikeManager.update()` rebuilds all widgets
**File:** `lib/utils/like_manager.dart`

```dart
void updateLikeState(String adId, bool isLiked) {
  _likeState[adId] = isLiked;
  update();  // ← no ID → rebuilds ALL GetBuilder<LikeManager> widgets
}
```

On a home or category screen with many product cards, every like toggle rebuilds every card. Should pass the ad ID as the update tag:
```dart
update([adId]);
```
And register `GetBuilder` with `id: adId` in product card widgets.

---

### Q-6 · `Preference` class duplicates `Database` / `GetStorage`
`lib/utils/preference.dart` wraps `GetStorage` in a singleton, but `lib/utils/database.dart` also wraps `GetStorage` directly. Two parallel abstractions exist for the same store. Over time this leads to inconsistent reads if some code uses one and some uses the other.

---

## 9. Strengths

| Area | Detail |
|---|---|
| **Crashlytics coverage** | Both `FlutterError.onError` and `PlatformDispatcher.instance.onError` are wired to Crashlytics, providing complete crash capture |
| **Stripe security** | Payment intent is created server-side; the publishable key is the only thing stored client-side |
| **Firebase token refresh** | `FirebaseAccessToken.onGet()` checks expiry and refreshes when needed before API calls |
| **Cross-screen like sync** | `LikeManager` as a permanent GetX controller correctly propagates like state without prop drilling |
| **Extensive localisation** | 18 languages via GetX translations with locale persistence |
| **Route observer** | Global `RouteObserver` registered in `navigatorObservers`, enabling lifecycle-aware page tracking |
| **Location persistence** | `Database.initSelectedLocation()` restores the full location context (lat/lng, radius, bounds) across sessions |
| **AI listing assistant** | `aiListingAssistApi` endpoint integrated for AI-generated listing content |
| **Background FCM** | Handler correctly defined as a top-level function and registered before any FCM usage |
| **GetX binding pattern** | Every screen has a dedicated `Binding` class — controllers are properly lazy-loaded and disposed |

---

## 10. Issue Summary Table

| ID | Category | File | Severity | Description |
|---|---|---|---|---|
| S-1 | Security | `utils/api.dart` | 🔴 High | HTTP localhost as default base URL |
| S-2 | Security | `utils/api.dart` | 🟠 Medium | IP API called over HTTP |
| S-3 | Security | `payment/stripe/stripe_service.dart` | 🔴 High | Payment errors silently swallowed |
| S-4 | Security | `payment/razor_pay/razor_pay_service.dart` | 🔴 High | Test key hardcoded, ignores injected key |
| S-5 | Security | `payment/phone_pay/phone_pay_service.dart` | 🔴 Critical | Stub checksum + placeholder credentials |
| B-1 | Bug | `main.dart` | 🟠 Medium | `setSelectedLocationText` called without `()` |
| B-2 | Bug | `main.dart` | 🟠 Medium | `Database.init()` skipped if FCM token is null |
| B-3 | Bug | `utils/font_style.dart` | 🟡 Low | `fontStyleW600` uses W500 weight |
| Q-1 | Quality | All files | 🟠 Medium | Package name `listify` vs brand `sellupnow` |
| Q-2 | Quality | `main.dart` | 🟡 Low | 80-line dead code block comment |
| Q-3 | Quality | `main.dart` | 🟠 Medium | Duplicate notification permission requests |
| Q-4 | Quality | `socket/socket_service.dart` | 🟠 Medium | Global socket — no lifecycle/dispose |
| Q-5 | Quality | `utils/like_manager.dart` | 🟡 Low | `update()` without ID causes full tree rebuild |
| Q-6 | Quality | `utils/preference.dart` | 🟡 Low | Duplicate storage abstraction alongside `Database` |

---

## 11. Recommended Fix Order

1. **S-5** — Disable or gate PhonePe service entirely until properly implemented (guard with a feature flag or remove from routing)
2. **S-4** — Replace hardcoded Razorpay test key with `razorKeys` variable
3. **S-3** — Rethrow Stripe exceptions so callers handle failures correctly
4. **S-1** — Change default `API_BASE_URL` to empty string + add assertion
5. **B-2** — Make `Database.init()` unconditional (pass `fcmToken ?? ""`)
6. **B-1** — Add `()` to `Database.setSelectedLocationText` call
7. **S-2** — Change `ipApi` to HTTPS
8. **Q-3** — Consolidate duplicate permission request calls
9. **Q-4** — Refactor socket into a `GetxService` with proper lifecycle
10. **Q-5** — Pass `adId` to `update([adId])` in `LikeManager`
11. **Q-1** — Rename package namespace from `listify` to `sellupnow`
12. **B-3** — Fix `fontStyleW600` to use `FontWeight.w600`
13. **Q-2** — Delete dead code block comment in `main.dart`
14. **Q-6** — Consolidate `Preference` into `Database` or remove one
