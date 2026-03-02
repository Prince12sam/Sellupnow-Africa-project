<?php

use App\Http\Controllers\API\Auth\AuthController;
use App\Http\Controllers\API\Auth\ForgotPasswordController;
use App\Http\Controllers\API\AiListingAssistantController;
use App\Http\Controllers\API\WalletController;
use App\Http\Controllers\API\WithdrawController;
use App\Http\Controllers\API\SupportTicketController;
use App\Http\Controllers\API\AiRecommendationController;
use App\Http\Controllers\API\Admin\ListingAdminController;
use App\Http\Controllers\API\Admin\ListingReportAdminController;
use App\Http\Controllers\API\Admin\RoleAdminController;
use App\Http\Controllers\API\Admin\ReportReasonAdminController;
use App\Http\Controllers\API\Admin\UserAdminController;
use App\Http\Controllers\API\AdVideoController;
use App\Http\Controllers\API\AdVideoLikeController;
use App\Http\Controllers\API\AdViewController;
use App\Http\Controllers\API\BannerController;
use App\Http\Controllers\API\BlockController;
use App\Http\Controllers\API\BlogController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\ChatController;
use App\Http\Controllers\API\CountryController;
use App\Http\Controllers\API\FaqController;
use App\Http\Controllers\API\FeatureAdPackageController;
use App\Http\Controllers\API\FollowController;
use App\Http\Controllers\API\HomeController;
use App\Http\Controllers\API\IdProofController;
use App\Http\Controllers\API\ListingController;
use App\Http\Controllers\API\ListingFavoriteController;
use App\Http\Controllers\API\ListingReportController;
use App\Http\Controllers\API\MasterController;
use App\Http\Controllers\API\NotificationController;
use App\Http\Controllers\API\PaymentController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\PurchaseHistoryController;
use App\Http\Controllers\API\ReportReasonController;
use App\Http\Controllers\API\ReviewController;
use App\Http\Controllers\API\SocialAuthController;
use App\Http\Controllers\API\SubscriptionPlanController;
use App\Http\Controllers\API\TipController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\VerificationController;
use App\Http\Controllers\API\VideoViewController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/health', function () {
    return response()->json(['status' => 'ok']);
});

Route::prefix('v1')->group(function () {
    Route::get('master', [MasterController::class, 'index']);
    Route::get('home', [HomeController::class, 'index']);
    Route::get('home/popular-products', [HomeController::class, 'popularProducts']);
    Route::get('banners', [BannerController::class, 'index']);
    Route::get('countries', [CountryController::class, 'index']);
    Route::get('categories', [CategoryController::class, 'index']);
    Route::get('categories/attributes', [CategoryController::class, 'getCategoryAttributes']);
    Route::get('blogs', [BlogController::class, 'index']);
    Route::get('blogs/{slugOrId}', [BlogController::class, 'show']);
    Route::get('products', [ProductController::class, 'index']);
    Route::get('products/show', [ProductController::class, 'show']);
    Route::get('reviews', [ReviewController::class, 'index']);

    Route::post('auth/register', [AuthController::class, 'register'])->middleware('throttle:10,1');
    Route::post('auth/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
    Route::post('auth/social/login', [SocialAuthController::class, 'login'])->middleware('throttle:10,1');
    Route::post('auth/forgot-password/resend-otp', [ForgotPasswordController::class, 'resendOTP'])->middleware('throttle:5,1');
    Route::post('auth/forgot-password/verify-otp', [ForgotPasswordController::class, 'verifyOtp'])->middleware('throttle:10,1');
    Route::post('auth/forgot-password/reset-password', [ForgotPasswordController::class, 'resetPassword'])->middleware('throttle:5,1');

    Route::middleware('firebase.auth')->group(function () {
        Route::post('auth/logout', [AuthController::class, 'logout']);

        Route::get('user/profile', [UserController::class, 'index']);
        Route::post('user/profile', [UserController::class, 'update']);
        Route::post('user/change-password', [UserController::class, 'changePassword']);

        Route::post('products/favorite', [ProductController::class, 'addFavorite']);
        Route::get('products/favorites', [ProductController::class, 'favoriteProducts']);
        Route::post('reviews', [ProductController::class, 'storeReview']);

        Route::get('chat/shops', [ChatController::class, 'getShops']);
        Route::get('chat/messages', [ChatController::class, 'getMessage']);
        Route::post('chat/messages', [ChatController::class, 'sendMessage']);
        Route::get('chat/unread', [ChatController::class, 'unreadMessages']);
    });

    Route::prefix('admin')->middleware(['auth:api', 'role:root|admin'])->group(function () {
        Route::prefix('users')->group(function () {
            Route::get('/', [UserAdminController::class, 'index']);
            Route::post('/', [UserAdminController::class, 'store']);
            Route::put('/{id}', [UserAdminController::class, 'update']);
            Route::delete('/{id}', [UserAdminController::class, 'destroy']);
            Route::post('/{id}/permissions', [UserAdminController::class, 'updatePermissions']);
            Route::post('/{id}/reset-password', [UserAdminController::class, 'resetPassword']);
        });

        Route::prefix('roles')->group(function () {
            Route::get('/', [RoleAdminController::class, 'index']);
            Route::post('/', [RoleAdminController::class, 'store']);
            Route::put('/{id}', [RoleAdminController::class, 'update']);
            Route::post('/{id}/permissions', [RoleAdminController::class, 'updatePermissions']);
            Route::delete('/{id}', [RoleAdminController::class, 'destroy']);
        });

        Route::prefix('listings')->group(function () {
            Route::get('/', [ListingAdminController::class, 'index']);
            Route::get('/{id}', [ListingAdminController::class, 'show']);
            Route::put('/{id}', [ListingAdminController::class, 'update']);
            Route::patch('/{id}/publish', [ListingAdminController::class, 'updatePublishStatus']);
            Route::delete('/{id}', [ListingAdminController::class, 'destroy']);
        });

        Route::prefix('report-reasons')->group(function () {
            Route::get('/', [ReportReasonAdminController::class, 'index']);
            Route::post('/', [ReportReasonAdminController::class, 'store']);
            Route::put('/{id}', [ReportReasonAdminController::class, 'update']);
            Route::delete('/{id}', [ReportReasonAdminController::class, 'destroy']);
        });

        Route::prefix('listing-reports')->group(function () {
            Route::get('/', [ListingReportAdminController::class, 'index']);
            Route::get('/{id}', [ListingReportAdminController::class, 'show']);
            Route::put('/{id}', [ListingReportAdminController::class, 'update']);
            Route::delete('/{id}', [ListingReportAdminController::class, 'destroy']);
        });
    });
});

Route::prefix('client')->group(function () {
    Route::prefix('setting')->group(function () {
        Route::get('retrieveSystemConfig', [MasterController::class, 'index']);
    });

    Route::prefix('banner')->group(function () {
        Route::get('retrieveBannerList', [BannerController::class, 'index']);
    });

    Route::prefix('country')->group(function () {
        Route::get('fetchCountryList', [CountryController::class, 'index']);
        Route::get('fetchStatesByCountry', [CountryController::class, 'fetchStatesByCountry']);
        Route::get('fetchCitiesByState', [CountryController::class, 'fetchCitiesByState']);
    });

    Route::prefix('category')->group(function () {
        Route::get('retrieveCategoryList', [CategoryController::class, 'index']);
        Route::get('fetchSubcategoriesByParent', [CategoryController::class, 'index']);
        Route::get('getHierarchicalCategories', [CategoryController::class, 'index']);
    });

    Route::prefix('blog')->group(function () {
        Route::get('retrieveBlogList', [BlogController::class, 'index']);
        Route::get('retrieveBlogPost', [BlogController::class, 'show']);
        Route::get('retrieveTrendingBlogPosts', [BlogController::class, 'index']);
    });

    Route::prefix('adListing')->group(function () {
        Route::get('fetchAdListingRecords', [ListingController::class, 'index']);
        Route::get('fetchAdDetailsById', [ListingController::class, 'show']);
        Route::get('fetchPopularAdListingRecords', [ListingController::class, 'popular']);
        Route::get('fetchCategoryWiseAdListings', [ListingController::class, 'categoryWise']);
        Route::get('fetchMostLikedAdListings', [ListingController::class, 'mostLiked']);
        Route::post('report', [ListingReportController::class, 'store']);
    });

    Route::prefix('review')->group(function () {
        Route::get('retrieveReview', [ReviewController::class, 'index']);
        Route::post('giveReview', [ProductController::class, 'storeReview'])->middleware('firebase.auth');
    });

    Route::prefix('chatTopic')->middleware('firebase.auth')->group(function () {
        Route::get('getChatList', [ChatController::class, 'getShops']);
    });

    Route::prefix('chat')->middleware('firebase.auth')->group(function () {
        Route::get('getChatHistory', [ChatController::class, 'getMessage']);
        Route::post('sendChatMessage', [ChatController::class, 'sendMessage']);
    });

    Route::prefix('user')->group(function () {
        Route::get('verifyUserExistence', function (Request $request) {
            $query = User::query();

            if ($request->filled('phone')) {
                $query->where('phone', $request->query('phone'));
            }

            if ($request->filled('email')) {
                $query->orWhere('email', $request->query('email'));
            }

            $exists = $query->exists();

            return response()->json([
                'message' => 'user existence check',
                'data' => [
                    'exists' => $exists,
                ],
            ]);
        });

        Route::post('loginOrSignupUser', [AuthController::class, 'login'])->middleware('throttle:10,1');
        Route::post('initiatePasswordReset', [ForgotPasswordController::class, 'resendOTP'])->middleware('throttle:5,1');
        Route::post('verifyPasswordResetOtp', [ForgotPasswordController::class, 'verifyOtp'])->middleware('throttle:10,1');
        Route::post('resetPassword', [ForgotPasswordController::class, 'resetPassword'])->middleware('throttle:5,1');

        Route::middleware('firebase.auth')->group(function () {
            Route::get('fetchUserProfile', [UserController::class, 'index']);
            Route::post('updateProfileInfo', [UserController::class, 'update']);
            Route::post('changePassword', [UserController::class, 'changePassword']);
        });
    });

    Route::prefix('adLike')->middleware('firebase.auth')->group(function () {
        Route::post('toggleAdLike', [ListingFavoriteController::class, 'toggle']);
        Route::get('fetchLikedAdListingRecords', [ListingFavoriteController::class, 'index']);
    });

    Route::prefix('aiListing')->middleware(['firebase.auth', 'throttle:ai-listing-assistant'])->group(function () {
        Route::post('suggestListingContent', [AiListingAssistantController::class, 'suggest']);
    });

    Route::prefix('aiRecommend')->middleware(['firebase.auth', 'throttle:ai-recommendations'])->group(function () {
        Route::post('rankListings', [AiRecommendationController::class, 'rank']);
    });

    // ── Location aliases (Flutter expects city/ and state/ prefixes) ──────────
    Route::prefix('city')->group(function () {
        Route::get('fetchCityList', [CountryController::class, 'fetchCitiesByState']);
    });
    Route::prefix('state')->group(function () {
        Route::get('fetchStateList', [CountryController::class, 'fetchStatesByCountry']);
    });

    // ── Category attributes ───────────────────────────────────────────────────
    Route::prefix('attributes')->group(function () {
        Route::get('fetchCategoryAttributes', [CategoryController::class, 'getCategoryAttributes']);
    });

    // ── Ad Listing CRUD + extra queries ──────────────────────────────────────
    Route::prefix('adListing')->middleware('firebase.auth')->group(function () {
        Route::post('createAdListing',            [ListingController::class, 'store']);
        Route::post('updateAdListing',            [ListingController::class, 'update']);
        Route::post('removeAdListing',            [ListingController::class, 'destroy']);
        Route::post('promoteAds',                 [ListingController::class, 'promoteAds']);
        Route::get('getSellerProductsBasicInfo',  [ListingController::class, 'sellerProductsBasicInfo']);
    });
    Route::prefix('adListing')->group(function () {
        Route::get('fetchAdsByRelatedCategory',   [ListingController::class, 'relatedByCategory']);
        Route::get('fetchAuctionAdListings',      [ListingController::class, 'auctionListings']);
        Route::get('getAdListingsOfSeller',       [ListingController::class, 'sellerListings']);
    });

    // ── Ad Likes extras ───────────────────────────────────────────────────────
    Route::prefix('adLike')->group(function () {
        Route::get('getLikesForAd', [ListingFavoriteController::class, 'likesForAd']);
    });

    // ── Ad Views ──────────────────────────────────────────────────────────────
    Route::prefix('adView')->group(function () {
        Route::post('recordAdView', [AdViewController::class, 'record']);
        Route::get('getAdViews',    [AdViewController::class, 'getViews']);
    });

    // ── Ad Videos ─────────────────────────────────────────────────────────────
    Route::prefix('adVideo')->group(function () {
        Route::get('getAdVideos',              [AdVideoController::class, 'index']);
        Route::get('getAdVideosOfSeller',      [AdVideoController::class, 'ofSeller']);
    });
    Route::prefix('adVideo')->middleware('firebase.auth')->group(function () {
        Route::post('uploadAdVideo',   [AdVideoController::class, 'store']);
        Route::post('updateAdVideo',   [AdVideoController::class, 'update']);
        Route::post('deleteAdVideo',   [AdVideoController::class, 'destroy']);
    });
    Route::prefix('adVideoLike')->middleware('firebase.auth')->group(function () {
        Route::post('toggleAdVideoLike', [AdVideoLikeController::class, 'toggle']);
    });

    // ── Video Views ───────────────────────────────────────────────────────────
    Route::prefix('videoView')->middleware('firebase.auth')->group(function () {
        Route::post('recordVideoView', [VideoViewController::class, 'record']);
    });

    // ── Auction Bids ─────────────────────────────────────────────────────────
    Route::prefix('auctionBid')->middleware('firebase.auth')->group(function () {
        Route::post('placeManualBid', [ListingController::class, 'placeBid']);
    });

    // ── Block ────────────────────────────────────────────────────────────────
    Route::prefix('block')->middleware('firebase.auth')->group(function () {
        Route::post('toggleBlockUser', [BlockController::class, 'toggle']);
        Route::get('getBlockedUsers',  [BlockController::class, 'index']);
    });

    // ── Follow ───────────────────────────────────────────────────────────────
    Route::prefix('follow')->middleware('firebase.auth')->group(function () {
        Route::post('toggleFollowStatus',  [FollowController::class, 'toggle']);
        Route::get('getSocialConnections', [FollowController::class, 'connections']);
    });

    // ── Reports ───────────────────────────────────────────────────────────────
    Route::prefix('report')->middleware('firebase.auth')->group(function () {
        Route::post('reportAd',      [ListingReportController::class, 'store']);
        Route::post('reportUser',    [ListingReportController::class, 'reportUser']);
        Route::post('reportAdVideo', [ListingReportController::class, 'reportAdVideo']);
    });
    Route::prefix('reportReason')->group(function () {
        Route::get('fetchReportReasons', [ReportReasonController::class, 'index']);
    });

    // ── FAQ ───────────────────────────────────────────────────────────────────
    Route::prefix('faq')->group(function () {
        Route::get('retrieveFAQList', [FaqController::class, 'index']);
    });

    // ── Safety Tips ───────────────────────────────────────────────────────────
    Route::prefix('tip')->group(function () {
        Route::get('listHelpfulHints', [TipController::class, 'index']);
    });

    // ── Subscription Plans ────────────────────────────────────────────────────
    Route::prefix('subscriptionPlan')->group(function () {
        Route::get('fetchSubscriptionPlans', [SubscriptionPlanController::class, 'index']);
    });

    // ── Featured Ad Packages ──────────────────────────────────────────────────
    Route::prefix('featureAdPackage')->group(function () {
        Route::get('fetchFeaturedAdPackages', [FeatureAdPackageController::class, 'index']);
    });

    // ── Purchase History ──────────────────────────────────────────────────────
    Route::prefix('purchaseHistory')->middleware('firebase.auth')->group(function () {
        Route::post('createPurchaseHistory', [PurchaseHistoryController::class, 'store']);
        Route::get('getPurchaseHistory',     [PurchaseHistoryController::class, 'index']);
    });

    // ── Payments (Paystack / PayPal) ──────────────────────────────────────────
    Route::prefix('paystack')->middleware('firebase.auth')->group(function () {
        Route::post('initialize-package-payment', [PaymentController::class, 'paystackInit']);
        Route::get('verify-package-payment',      [PaymentController::class, 'paystackVerify']);
    });
    Route::prefix('paypal')->middleware('firebase.auth')->group(function () {
        Route::post('create-order',   [PaymentController::class, 'paypalCreateOrder']);
        Route::post('capture-order',  [PaymentController::class, 'paypalCaptureOrder']);
    });

    // ── Identity Verification ─────────────────────────────────────────────────
    Route::prefix('idProof')->group(function () {
        Route::get('listIdProofs', [IdProofController::class, 'index']);
    });
    Route::prefix('verification')->middleware('firebase.auth')->group(function () {
        Route::post('submitUserVerification', [VerificationController::class, 'store']);
    });

    // ── Notifications ─────────────────────────────────────────────────────────
    Route::prefix('notification')->middleware('firebase.auth')->group(function () {
        Route::get('getMyNotifications',     [NotificationController::class, 'index']);
        Route::post('clearMyNotifications',  [NotificationController::class, 'clear']);
    });

    // ── User extras ───────────────────────────────────────────────────────────
    Route::prefix('user')->middleware('firebase.auth')->group(function () {
        Route::post('deactivateAccount',     [UserController::class, 'deactivateAccount']);
        Route::post('manageUserPermission',  [UserController::class, 'managePermission']);
    });

    // ── Wallet ────────────────────────────────────────────────────────────────
    Route::prefix('wallet')->middleware('firebase.auth')->group(function () {
        Route::get('getBalance',       [WalletController::class, 'getBalance']);
        Route::get('getTransactions',  [WalletController::class, 'getTransactions']);
    });

    // ── Withdraw Requests ─────────────────────────────────────────────────────
    Route::prefix('withdraw')->middleware('firebase.auth')->group(function () {
        Route::get('getWithdrawRequests',    [WithdrawController::class, 'index']);
        Route::post('submitWithdrawRequest', [WithdrawController::class, 'store']);
    });

    // ── Support Tickets ───────────────────────────────────────────────────────
    Route::prefix('support')->middleware('firebase.auth')->group(function () {
        Route::get('getTickets',          [SupportTicketController::class, 'index']);
        Route::post('createTicket',       [SupportTicketController::class, 'store']);
        Route::get('getTicket/{id}',      [SupportTicketController::class, 'showById']);
        Route::post('replyTicket/{id}',   [SupportTicketController::class, 'addReply']);
    });
});
