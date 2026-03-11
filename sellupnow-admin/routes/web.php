<?php

use App\Http\Controllers\Admin\Auth\LoginController as AdminLoginController;
use App\Http\Controllers\Admin\AdController;
use App\Http\Controllers\Admin\AdminAdsHubController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\BusinessSetupController;
use App\Http\Controllers\Admin\CategoryAttributeController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ChatOversightController;
use App\Http\Controllers\Admin\ContactUsController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\CountryController;
use App\Http\Controllers\Admin\CurrencyController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\CustomerNotificationController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EmployeeManageController;
use App\Http\Controllers\Admin\FirebaseController;
use App\Http\Controllers\Admin\FlashSaleController;
use App\Http\Controllers\Admin\FooterController;
use App\Http\Controllers\Admin\GlobalSearchController;
use App\Http\Controllers\Admin\GeneraleSettingController;
use App\Http\Controllers\Admin\GoogleReCaptchaController;
use App\Http\Controllers\Admin\LanguageController;
use App\Http\Controllers\Admin\ListoceanAdvertisementController as siteAdvertisementController;
use App\Http\Controllers\Admin\ListoceanCityController as siteCityController;
use App\Http\Controllers\Admin\ListoceanPageBuildersController as pageBuildersController;
use App\Http\Controllers\Admin\ListoceanPagesController as sitePagesController;
use App\Http\Controllers\Admin\ListoceanCountryController as siteCountryController;
use App\Http\Controllers\Admin\ListoceanCustomerController as siteCustomerController;
use App\Http\Controllers\Admin\ListoceanEmailTemplateController as emailTemplateController;
use App\Http\Controllers\Admin\ListoceanMapSettingsController as mapSettingsController;
use App\Http\Controllers\Admin\ListoceanNoticeController as siteNoticeController;
use App\Http\Controllers\Admin\ListoceanPageSettingsController as sitePagesettingsController;
use App\Http\Controllers\Admin\ListoceanStateController as siteStateController;
use App\Http\Controllers\Admin\ListingModerationController;
use App\Http\Controllers\Admin\ListingReportController;
use App\Http\Controllers\Admin\VideoModerationController;
use App\Http\Controllers\Admin\PromoVideoAdsController;
use App\Http\Controllers\Admin\BannerAdRequestsController;
use App\Http\Controllers\Admin\MailConfigurationController;
use App\Http\Controllers\Admin\ListoceanMembershipPlanController as membershipPlanController;
use App\Http\Controllers\Admin\ListoceanWalletController as siteWalletController;
use App\Http\Controllers\Admin\ListoceanFeaturedAdPackageController as featuredAdPackageController;
use App\Http\Controllers\Admin\ListoceanFeaturedAdReportController as featuredAdReportController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\ListoceanHomepageHeroController as homepageHeroController;
use App\Http\Controllers\Admin\ListoceanFlashSaleWidgetController as flashSaleWidgetController;
use App\Http\Controllers\Admin\ListoceanEscrowController;
use App\Http\Controllers\Admin\ListoceanReelAdPlacementController as reelAdPlacementController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\PaymentGatewayController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\PusherConfigController;
use App\Http\Controllers\Admin\ReportReasonController;
use App\Http\Controllers\Admin\ListoceanReviewController;
use App\Http\Controllers\Admin\ReviewsController;
use App\Http\Controllers\Admin\RiderController;
use App\Http\Controllers\Admin\RolePermissionController;
use App\Http\Controllers\Admin\SocialAuthController;
use App\Http\Controllers\Admin\SocialLinkController;
use App\Http\Controllers\Admin\SubscriptionPlanController;
use App\Http\Controllers\Admin\SupportController;
use App\Http\Controllers\Admin\SupportTicketController;
use App\Http\Controllers\Admin\ThemeColorController;
use App\Http\Controllers\Admin\TicketIssueTypeController;
use App\Http\Controllers\Admin\VatTaxController;
use App\Http\Controllers\Admin\VerifyManageController;
use App\Http\Controllers\Admin\ListoceanIdentityVerificationController as identityVerificationController;
use App\Http\Controllers\Admin\WhatsAppChatController;
use App\Http\Controllers\Admin\WithdrawController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->to(rtrim(env('CUSTOMER_WEB_URL', 'http://127.0.0.1:8090'), '/'));
});

Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
    ]);
});

Route::get('/warning', function () {
    return redirect()->route('admin.login');
})->name('warning');

Route::get('/generale-setting', function () {
    return redirect()->route('admin.generale-setting.index');
});

// Convenience aliases (users often omit the /admin prefix)
Route::get('/content/reel-ad-placements', function () {
    return redirect()->route('admin.reelAdPlacement.index');
});

Route::prefix('admin')->name('admin.')->group(function () {
    // Admin landing: go to login for guests or dashboard for authenticated users.
    Route::get('/', function () {
        if (auth()->check()) {
            return redirect()->route('admin.dashboard.index');
        }

        return redirect()->route('admin.login');
    })->name('index');

    Route::middleware('guest')->group(function () {
        Route::get('/login', [AdminLoginController::class, 'index'])->name('login');
        Route::post('/login', [AdminLoginController::class, 'login'])->middleware('throttle:5,1')->name('login.submit');
    });

    Route::middleware(['auth', 'checkPermission'])->group(function () {
        Route::post('/logout', [AdminLoginController::class, 'logout'])->name('logout');

        // Backward-compatible alias for gallery modal filemanager URL used by legacy scripts.
        Route::get('/laravel-filemanager/{any?}', function ($any = null) {
            $target = '/filemanager' . ($any ? '/' . ltrim((string) $any, '/') : '');
            $query = request()->getQueryString();

            return redirect($query ? ($target . '?' . $query) : $target);
        })->where('any', '.*');

        // Header global search (menu + Customer Web entities)
        Route::get('/global-search', [GlobalSearchController::class, 'search'])->name('globalSearch');

        // Website (customer web) homepage hero background (PageBuilder HeaderStyleOne)
        Route::get('/website/homepage-hero', [homepageHeroController::class, 'edit'])->name('homepageHero.edit');
        Route::post('/website/homepage-hero', [homepageHeroController::class, 'update'])->name('homepageHero.update');

        // Website (customer web) flash sale widget placements
        Route::get('/website/flash-sale-widget', [flashSaleWidgetController::class, 'edit'])->name('flashSaleWidget.edit');
        Route::post('/website/flash-sale-widget', [flashSaleWidgetController::class, 'update'])->name('flashSaleWidget.update');

        // Listocean (customer web) reels ad placements (per reel overlay targeting)
        Route::get('/content/reel-ad-placements', [reelAdPlacementController::class, 'index'])->name('reelAdPlacement.index');
        Route::post('/content/reel-ad-placements', [reelAdPlacementController::class, 'store'])->name('reelAdPlacement.store');
        Route::post('/content/reel-ad-placements/{id}', [reelAdPlacementController::class, 'update'])->name('reelAdPlacement.update')->whereNumber('id');
        Route::post('/content/reel-ad-placements/{id}/delete', [reelAdPlacementController::class, 'destroy'])->name('reelAdPlacement.destroy')->whereNumber('id');

        Route::prefix('dashboard')->name('dashboard.')->group(function () {
            Route::get('/', [DashboardController::class, 'index'])->name('index');
            Route::get('/statistics', [DashboardController::class, 'orderStatistics'])->name('statistics');
            Route::get('/listing-statistics', [DashboardController::class, 'listingStatistics'])->name('listingStatistics');
            Route::get('/notification', [NotificationController::class, 'show'])->name('notification');
        });

        Route::prefix('category')->name('category.')->group(function () {
            Route::get('/', [CategoryController::class, 'index'])->name('index');
            Route::get('/create', [CategoryController::class, 'create'])->name('create');
            Route::post('/', [CategoryController::class, 'store'])->name('store');
            Route::get('/show', [CategoryController::class, 'show'])->name('show');
            Route::post('/menu-update', [CategoryController::class, 'menuUpdate'])->name('menu.update');
            Route::post('/{category}', [CategoryController::class, 'update'])->name('update');
            Route::get('/{category}/toggle', [CategoryController::class, 'statusToggle'])->name('toggle');
            Route::match(['post', 'delete'], '/{category}/delete', [CategoryController::class, 'destroy'])->name('destroy');
            Route::delete('/{category}/delete', [CategoryController::class, 'destroy']);
        });

        Route::prefix('category-attribute')->name('categoryAttribute.')->group(function () {
            Route::get('/', [CategoryAttributeController::class, 'index'])->name('index');
            Route::get('/create', [CategoryAttributeController::class, 'index'])->name('create');
            Route::post('/', [CategoryAttributeController::class, 'store'])->name('store');
            Route::get('/show', [CategoryAttributeController::class, 'show'])->name('show');
            Route::post('/menu-update', [CategoryAttributeController::class, 'menuUpdate'])->name('menu.update');
            Route::post('/{categoryAttribute}', [CategoryAttributeController::class, 'update'])->name('update');
            Route::get('/{categoryAttribute}/toggle', [CategoryAttributeController::class, 'statusToggle'])->name('toggle');
            Route::match(['post', 'delete'], '/{categoryAttribute}/delete', [CategoryAttributeController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('country')->name('country.')->group(function () {
            Route::get('/', [CountryController::class, 'index'])->name('index');
            Route::get('/create', [CountryController::class, 'index'])->name('create');
            Route::post('/', [CountryController::class, 'store'])->name('store');
            Route::put('/{country}', [CountryController::class, 'update'])->name('update');
            Route::match(['post', 'delete'], '/{country}/remove', [CountryController::class, 'destroy'])->name('delete');
            Route::match(['post', 'delete'], '/{country}/delete', [CountryController::class, 'destroy'])->name('destroy');
        });

        // Listocean (customer web) identity verification requests
        // Canonical short URLs
        Route::prefix('identity-verification')->name('identityVerification.')->group(function () {
            Route::get('/', [identityVerificationController::class, 'index'])->name('index');
            Route::get('/{requestId}', [identityVerificationController::class, 'show'])->name('show')->whereNumber('requestId');
            Route::post('/{requestId}/approve', [identityVerificationController::class, 'approve'])->name('approve')->whereNumber('requestId');
            Route::post('/{requestId}/decline', [identityVerificationController::class, 'decline'])->name('decline')->whereNumber('requestId');
        });

        // Backward-compatible redirects (old longer URLs)
        Route::get('listocean-identity-verification', function () {
            return redirect()->route('admin.identityVerification.index');
        });
        Route::get('listocean-identity-verification/{requestId}', function (int $requestId) {
            return redirect()->route('admin.identityVerification.show', ['requestId' => $requestId]);
        })->whereNumber('requestId');

        // Common typo/short-hand support
        Route::get('lidentity-verification', function () {
            return redirect()->route('admin.identityVerification.index');
        });

        // Website (customer web) location management for listing forms
        Route::prefix('site-country')->name('siteCountry.')->group(function () {
            Route::get('/', [siteCountryController::class, 'index'])->name('index');
            Route::post('/', [siteCountryController::class, 'store'])->name('store');
            Route::put('/{id}', [siteCountryController::class, 'update'])->name('update')->whereNumber('id');
            Route::post('/{id}/reimport', [siteCountryController::class, 'reimport'])->name('reimport')->whereNumber('id');
            Route::match(['post', 'delete'], '/{id}/delete', [siteCountryController::class, 'destroy'])->name('destroy')->whereNumber('id');
        });

        Route::prefix('site-state')->name('siteState.')->group(function () {
            Route::get('/', [siteStateController::class, 'index'])->name('index');
            Route::post('/', [siteStateController::class, 'store'])->name('store');
            Route::put('/{id}', [siteStateController::class, 'update'])->name('update')->whereNumber('id');
            Route::match(['post', 'delete'], '/{id}/delete', [siteStateController::class, 'destroy'])->name('destroy')->whereNumber('id');
        });

        Route::prefix('site-city')->name('siteCity.')->group(function () {
            Route::get('/', [siteCityController::class, 'index'])->name('index');
            Route::post('/', [siteCityController::class, 'store'])->name('store');
            Route::put('/{id}', [siteCityController::class, 'update'])->name('update')->whereNumber('id');
            Route::match(['post', 'delete'], '/{id}/delete', [siteCityController::class, 'destroy'])->name('destroy')->whereNumber('id');
        });

        Route::prefix('blog')->name('blog.')->group(function () {
            Route::get('/', [BlogController::class, 'index'])->name('index');
            Route::get('/create', [BlogController::class, 'create'])->name('create');
            Route::post('/', [BlogController::class, 'store'])->name('store');
            Route::get('/{blog}/edit', [BlogController::class, 'edit'])->name('edit');
            Route::put('/{blog}', [BlogController::class, 'update'])->name('update');
            Route::match(['post', 'delete'], '/{blog}/remove', [BlogController::class, 'destroy'])->name('delete');
            Route::get('/{blog}/toggle', [BlogController::class, 'statusToggle'])->name('toggle');
            Route::match(['post', 'delete'], '/{blog}/delete', [BlogController::class, 'destroy'])->name('destroy');
            Route::post('/generate-ai-data', [BlogController::class, 'generateAIData'])->name('generate.AI.data');
        });

        Route::prefix('banner')->name('banner.')->group(function () {
            Route::get('/', [BannerController::class, 'index'])->name('index');
            Route::get('/create', [BannerController::class, 'create'])->name('create');
            Route::post('/', [BannerController::class, 'store'])->name('store');
            Route::get('/{banner}/edit', [BannerController::class, 'edit'])->name('edit');
            Route::put('/{banner}', [BannerController::class, 'update'])->name('update');
            Route::get('/{banner}/toggle', [BannerController::class, 'statusToggle'])->name('toggle');
            Route::match(['post', 'delete'], '/{banner}/delete', [BannerController::class, 'destroy'])->name('destroy');
        });

        

        Route::prefix('ad')->name('ad.')->group(function () {
            Route::get('/', [AdController::class, 'index'])->name('index');
            Route::get('/create', [AdController::class, 'create'])->name('create');
            Route::post('/', [AdController::class, 'store'])->name('store');
            Route::get('/{ad}/edit', [AdController::class, 'edit'])->name('edit');
            Route::put('/{ad}', [AdController::class, 'update'])->name('update');
            Route::get('/{ad}/toggle', [AdController::class, 'statusToggle'])->name('toggle');
            Route::match(['post', 'delete'], '/{ad}/delete', [AdController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('brand')->name('brand.')->group(function () {
            Route::get('/', [BrandController::class, 'index'])->name('index');
            Route::get('/create', [BrandController::class, 'index'])->name('create');
            Route::post('/', [BrandController::class, 'store'])->name('store');
            Route::get('/{brand}/edit', [BrandController::class, 'index'])->name('edit');
            Route::match(['post', 'put', 'patch'], '/{brand}', [BrandController::class, 'update'])->name('update');
            Route::get('/{brand}/toggle', [BrandController::class, 'statusToggle'])->name('toggle');
        });

        Route::prefix('business-setting')->name('business-setting.')->group(function () {
            Route::get('/', [BusinessSetupController::class, 'index'])->name('index');
            Route::post('/update', [BusinessSetupController::class, 'update'])->name('update');
            Route::get('/withdraw', [BusinessSetupController::class, 'withdraw'])->name('withdraw');
            Route::post('/withdraw/update', [BusinessSetupController::class, 'withdrawUpdate'])->name('withdraw.update');
        });

        Route::get('/business-setup/header', [BusinessSetupController::class, 'index'])->name('business-setup.header');

        Route::prefix('coupon')->name('coupon.')->group(function () {
            Route::get('/', [CouponController::class, 'index'])->name('index');
            Route::get('/create', [CouponController::class, 'create'])->name('create');
            Route::post('/', [CouponController::class, 'store'])->name('store');
            Route::get('/{coupon}/edit', [CouponController::class, 'edit'])->name('edit');
            Route::match(['post', 'put', 'patch'], '/{coupon}', [CouponController::class, 'update'])->name('update');
            Route::get('/{coupon}/toggle', [CouponController::class, 'statusToggle'])->name('toggle');
            Route::match(['post', 'delete'], '/{coupon}/delete', [CouponController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('currency')->name('currency.')->group(function () {
            Route::get('/', [CurrencyController::class, 'index'])->name('index');
            Route::get('/create', [CurrencyController::class, 'create'])->name('create');
            Route::post('/', [CurrencyController::class, 'store'])->name('store');
            Route::get('/{currency}/edit', [CurrencyController::class, 'edit'])->name('edit');
            Route::match(['post', 'put', 'patch'], '/{currency}', [CurrencyController::class, 'update'])->name('update');
            Route::match(['post', 'delete'], '/{currency}/delete', [CurrencyController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('customer')->name('customer.')->group(function () {
            Route::get('/', [CustomerController::class, 'index'])->name('index');
            Route::get('/create', [CustomerController::class, 'create'])->name('create');
            Route::post('/', [CustomerController::class, 'store'])->name('store');
            Route::get('/{customer}/edit', [CustomerController::class, 'edit'])->name('edit');
            Route::match(['post', 'put', 'patch'], '/{customer}', [CustomerController::class, 'update'])->name('update');
            Route::post('/{customer}/reset-password', [CustomerController::class, 'resetPassword'])->name('reset-password');
            Route::match(['post', 'delete'], '/{customer}/delete', [CustomerController::class, 'destroy'])->name('destroy');
        });

        // ListOcean customer-web user management (create/view/edit/reset password)
        Route::prefix('customer-web')->name('siteCustomer.')->group(function () {
            Route::get('/create', [siteCustomerController::class, 'create'])->name('create');
            Route::post('/', [siteCustomerController::class, 'store'])->name('store');
            Route::get('/{id}', [siteCustomerController::class, 'show'])->name('show')->whereNumber('id');
            Route::get('/{id}/edit', [siteCustomerController::class, 'edit'])->name('edit')->whereNumber('id');
            Route::match(['post', 'put', 'patch'], '/{id}', [siteCustomerController::class, 'update'])->name('update')->whereNumber('id');
            Route::post('/{id}/reset-password', [siteCustomerController::class, 'resetPassword'])->name('reset-password')->whereNumber('id');
            Route::post('/{id}/subscription', [siteCustomerController::class, 'updateSubscription'])->name('subscription.update')->whereNumber('id');
        });

        // ListOcean membership plans (paid membership catalog)
        Route::prefix('membership-plans')->name('membershipPlan.')->group(function () {
            Route::get('/', [membershipPlanController::class, 'index'])->name('index');
            Route::get('/create', [membershipPlanController::class, 'create'])->name('create');
            Route::post('/', [membershipPlanController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [membershipPlanController::class, 'edit'])->name('edit')->whereNumber('id');
            Route::match(['post', 'put', 'patch'], '/{id}', [membershipPlanController::class, 'update'])->name('update')->whereNumber('id');
            Route::delete('/{id}', [membershipPlanController::class, 'destroy'])->name('destroy')->whereNumber('id');
        });

        // Admin-managed membership features catalog
        Route::prefix('membership-features')->name('membershipFeature.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\MembershipFeatureController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\Admin\MembershipFeatureController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\Admin\MembershipFeatureController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [App\Http\Controllers\Admin\MembershipFeatureController::class, 'edit'])->name('edit')->whereNumber('id');
            Route::match(['post','put','patch'], '/{id}', [App\Http\Controllers\Admin\MembershipFeatureController::class, 'update'])->name('update')->whereNumber('id');
            Route::match(['post', 'delete'], '/{id}/delete', [App\Http\Controllers\Admin\MembershipFeatureController::class, 'destroy'])->name('destroy')->whereNumber('id');
        });

        // Customer Web wallet management (manual credit/debit + ledger)
        Route::prefix('customer-web-wallet')->name('siteWallet.')->group(function () {
            Route::get('/', [siteWalletController::class, 'index'])->name('index');
            Route::post('/adjust', [siteWalletController::class, 'adjust'])->name('adjust');
        });

        Route::prefix('featured-ad-packages')->name('featuredAdPackage.')->group(function () {
            Route::get('/', [featuredAdPackageController::class, 'index'])->name('index');
            Route::get('/create', [featuredAdPackageController::class, 'create'])->name('create');
            Route::post('/', [featuredAdPackageController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [featuredAdPackageController::class, 'edit'])->name('edit')->whereNumber('id');
            Route::match(['post', 'put', 'patch'], '/{id}', [featuredAdPackageController::class, 'update'])->name('update')->whereNumber('id');
            Route::match(['post', 'delete'], '/{id}/delete', [featuredAdPackageController::class, 'destroy'])->name('destroy')->whereNumber('id');
        });

        // Commission rules admin
        Route::prefix('commission-rules')->name('commissionRules.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\CommissionRuleController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\CommissionRuleController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\CommissionRuleController::class, 'store'])->name('store');
            Route::get('/{commissionRule}/edit', [\App\Http\Controllers\Admin\CommissionRuleController::class, 'edit'])->name('edit')->whereNumber('commissionRule');
            Route::match(['post','put','patch'], '/{commissionRule}', [\App\Http\Controllers\Admin\CommissionRuleController::class, 'update'])->name('update')->whereNumber('commissionRule');
            Route::match(['post', 'delete'], '/{commissionRule}/delete', [\App\Http\Controllers\Admin\CommissionRuleController::class, 'destroy'])->name('destroy')->whereNumber('commissionRule');
        });

        // Boosts admin
        Route::prefix('boosts')->name('boosts.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\BoostController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\BoostController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\BoostController::class, 'store'])->name('store');
            Route::get('/{boost}/edit', [\App\Http\Controllers\Admin\BoostController::class, 'edit'])->name('edit')->whereNumber('boost');
            Route::match(['post','put','patch'], '/{boost}', [\App\Http\Controllers\Admin\BoostController::class, 'update'])->name('update')->whereNumber('boost');
            Route::match(['post', 'delete'], '/{boost}/delete', [\App\Http\Controllers\Admin\BoostController::class, 'destroy'])->name('destroy')->whereNumber('boost');
        });

        // Helper endpoint for listing autocomplete used by boosts UI
        Route::get('/listings/search', [\App\Http\Controllers\Admin\ListingSearchController::class, 'search'])->name('listings.search');

        // Advertiser portal (scaffold)
        Route::prefix('advertiser-portal')->name('advertiserPortal.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\AdvertiserPortalController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\AdvertiserPortalController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\AdvertiserPortalController::class, 'store'])->name('store');
            Route::get('/purchases', [\App\Http\Controllers\Admin\AdvertiserPortalController::class, 'purchases'])->name('purchases');
        });

        Route::prefix('featured-ad-reports')->name('featuredAdReports.')->group(function () {
            Route::get('/purchases', [featuredAdReportController::class, 'purchases'])->name('purchases');
            Route::get('/activations', [featuredAdReportController::class, 'activations'])->name('activations');
        });

        Route::prefix('customer-notification')->name('customerNotification.')->group(function () {
            Route::get('/', [CustomerNotificationController::class, 'index'])->name('index');
            Route::post('/send', [CustomerNotificationController::class, 'send'])->name('send');
            Route::post('/filter', [CustomerNotificationController::class, 'filter'])->name('filter');
        });

        // Safety tips admin (manage popup content + color shown on listing pages)
        Route::get('/safety-tips', [\App\Http\Controllers\Admin\SafetyTipsController::class, 'edit'])->name('safetyTips.edit');
        Route::post('/safety-tips', [\App\Http\Controllers\Admin\SafetyTipsController::class, 'update'])->name('safetyTips.update');

        // ── FAQ (Q&A items served via mobile API) ────────────────────────────
        Route::prefix('faqs')->name('faq.')->group(function () {
            Route::get('/',                                    [FaqController::class, 'index'])        ->name('index');
            Route::get('/create',                             [FaqController::class, 'create'])       ->name('create');
            Route::post('/',                                   [FaqController::class, 'store'])        ->name('store');
            Route::get('/{faq}/edit',                         [FaqController::class, 'edit'])         ->name('edit');
            Route::match(['put','patch','post'], '/{faq}',    [FaqController::class, 'update'])       ->name('update');
            Route::delete('/{faq}',                           [FaqController::class, 'destroy'])      ->name('destroy');
            Route::post('/{faq}/toggle',                      [FaqController::class, 'toggleStatus'])->name('toggle');
            Route::post('/sort',                              [FaqController::class, 'sort'])         ->name('sort');
        });

        // Delivery charge routes removed (not used in this admin flow)

        Route::prefix('ticket-issue-types')->name('ticketIssueTypes.')->group(function () {
            Route::get('/', [TicketIssueTypeController::class, 'index'])->name('index');
            Route::post('/', [TicketIssueTypeController::class, 'store'])->name('store');
            Route::match(['post', 'put', 'patch'], '/{ticketIssueType}', [TicketIssueTypeController::class, 'update'])->name('update');
            Route::get('/{ticketIssueType}/toggle', [TicketIssueTypeController::class, 'toggleStatus'])->name('toggle');
            Route::match(['post', 'delete'], '/{ticketIssueType}/delete', [TicketIssueTypeController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('employee')->name('employee.')->group(function () {
            Route::get('/', [EmployeeManageController::class, 'index'])->name('index');
            Route::get('/create', [EmployeeManageController::class, 'create'])->name('create');
            Route::post('/', [EmployeeManageController::class, 'store'])->name('store');
            Route::post('/{employee}/reset-password', [EmployeeManageController::class, 'resetPassword'])->name('reset-password');
            Route::get('/{employee}/permission', [EmployeeManageController::class, 'permission'])->name('permission');
            Route::post('/{employee}/permission', [EmployeeManageController::class, 'updatePermission'])->name('permission.update');
            Route::match(['post', 'delete'], '/{employee}/delete', [EmployeeManageController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('firebase')->name('firebase.')->group(function () {
            Route::get('/', [FirebaseController::class, 'index'])->name('index');
            Route::post('/update', [FirebaseController::class, 'update'])->name('update');
        });

        Route::prefix('flash-sale')->name('flashSale.')->group(function () {
            Route::get('/', [FlashSaleController::class, 'index'])->name('index');
            Route::get('/create', [FlashSaleController::class, 'create'])->name('create');
            Route::post('/', [FlashSaleController::class, 'store'])->name('store');
            Route::get('/{flashSale}/edit', [FlashSaleController::class, 'edit'])->name('edit');
            Route::match(['post', 'put', 'patch'], '/{flashSale}', [FlashSaleController::class, 'update'])->name('update');
            Route::get('/{flashSale}/toggle', [FlashSaleController::class, 'statusToggle'])->name('toggle');
            Route::get('/{flashSale}/product', [FlashSaleController::class, 'show'])->name('product');
            Route::match(['post', 'delete'], '/{flashSale}/delete', [FlashSaleController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('footer')->name('footer.')->group(function () {
            Route::get('/', [FooterController::class, 'index'])->name('index');
            Route::match(['post', 'put', 'patch'], '/{footer}', [FooterController::class, 'update'])->name('update')->whereNumber('footer');
            Route::match(['post', 'put', 'patch'], '/item/{footerItem}', [FooterController::class, 'updateItem'])->name('update.item')->whereNumber('footerItem');
            Route::post('/section-sort', [FooterController::class, 'sectionSort'])->name('sectionSort');
            Route::post('/item-sort', [FooterController::class, 'itemSort'])->name('itemSort');
            Route::post('/added-new', [FooterController::class, 'addedNew'])->name('addedNew');
            Route::post('/disabled', [FooterController::class, 'disabled'])->name('disabled');
            Route::match(['post', 'delete'], '/{footerItem}/delete', [FooterController::class, 'destroy'])->name('destroy')->whereNumber('footerItem');

            // Website frontend footer management (single-admin bridge)
            Route::get('/website-logos', [FooterController::class, 'index'])->name('website-logos.get');
            Route::post('/website-logos', [FooterController::class, 'updateListoceanLogos'])->name('website-logos');

            Route::get('/website-content', [FooterController::class, 'index'])->name('website-content.get');
            Route::post('/website-content', [FooterController::class, 'updateListoceanFooterContent'])->name('website-content');
        });

        Route::prefix('generale-setting')->name('generale-setting.')->group(function () {
            Route::get('/', [GeneraleSettingController::class, 'index'])->name('index');
            Route::post('/update', [GeneraleSettingController::class, 'update'])->name('update');
            Route::post('/website-general-settings', [GeneraleSettingController::class, 'updateListoceanGeneralSettings'])->name('website-general-settings');
            Route::post('/run-update-script', [GeneraleSettingController::class, 'updateCommand'])->name('update.command');
        });

        Route::prefix('ai-prompt')->name('aiPrompt.')->group(function () {
            Route::get('/', [GeneraleSettingController::class, 'aiPromptIndex'])->name('index');
            Route::post('/update', [GeneraleSettingController::class, 'aiPromptUpdate'])->name('update');
            Route::get('/configure', [GeneraleSettingController::class, 'aiPromptConfigure'])->name('configure');
            Route::post('/configure/update', [GeneraleSettingController::class, 'aiPromptConfigureUpdate'])->name('configure.update');

            // ListOcean Listing AI Assistant settings + logs
            Route::post('/listing-assistant/update', [GeneraleSettingController::class, 'aiListingAssistantUpdate'])->name('listingAssistant.update');

            // Buyer-side AI recommendations settings + logs
            Route::post('/recommendations/update', [GeneraleSettingController::class, 'aiRecommendationsUpdate'])->name('recommendations.update');

            // Customer Web frontend AI popup widget settings
            Route::post('/frontend-chat/update', [GeneraleSettingController::class, 'aiFrontendChatUpdate'])->name('frontendChat.update');

            // Knowledge Base (PDF uploads)
            Route::post('/knowledge-base/upload', [GeneraleSettingController::class, 'aiKnowledgeBaseUpload'])->name('knowledgeBase.upload');
            Route::post('/knowledge-base/{id}/delete', [GeneraleSettingController::class, 'aiKnowledgeBaseDelete'])->whereNumber('id')->name('knowledgeBase.delete');
            Route::post('/knowledge-base/clear', [GeneraleSettingController::class, 'aiKnowledgeBaseClear'])->name('knowledgeBase.clear');
            Route::post('/knowledge-base/{id}/toggle', [GeneraleSettingController::class, 'aiKnowledgeBaseToggle'])->whereNumber('id')->name('knowledgeBase.toggle');
            Route::get('/knowledge-base/{id}/preview', [GeneraleSettingController::class, 'aiKnowledgeBasePreview'])->whereNumber('id')->name('knowledgeBase.preview');

            // Escrow settings (platform protection)
            Route::post('/escrow/update', [GeneraleSettingController::class, 'escrowSettingsUpdate'])->name('escrow.update');
        });

        Route::prefix('google-recaptcha')->name('googleReCaptcha.')->group(function () {
            Route::get('/', [GoogleReCaptchaController::class, 'index'])->name('index');
            Route::post('/update', [GoogleReCaptchaController::class, 'update'])->name('update');
            Route::post('/resync', [GoogleReCaptchaController::class, 'resync'])->name('resync');
        });

        Route::prefix('language')->name('language.')->group(function () {
            Route::get('/', [LanguageController::class, 'index'])->name('index');
            Route::get('/create', [LanguageController::class, 'create'])->name('create');
            Route::post('/', [LanguageController::class, 'store'])->name('store');
            Route::get('/{language}/edit', [LanguageController::class, 'edit'])->name('edit');
            Route::match(['post', 'put', 'patch'], '/{language}', [LanguageController::class, 'update'])->name('update');
            Route::post('/{language}/export', [LanguageController::class, 'export'])->name('export');
            Route::post('/{language}/import', [LanguageController::class, 'import'])->name('import');
            Route::get('/{language}/set-default', [LanguageController::class, 'setDefault'])->name('setDefault');
            Route::match(['post', 'delete'], '/{language}/delete', [LanguageController::class, 'delete'])->name('delete');
            Route::match(['post', 'delete'], '/{language}/destroy', [LanguageController::class, 'delete'])->name('destroy');
        });

        Route::prefix('legal-page')->name('legalPage.')->group(function () {
            Route::get('/', [PageController::class, 'index'])->name('index');
            Route::get('/{slug}/edit', [PageController::class, 'edit'])->name('edit');
            Route::match(['post', 'put', 'patch'], '/{slug}', [PageController::class, 'update'])->name('update');
        });

        Route::prefix('mail-config')->name('mailConfig.')->group(function () {
            Route::get('/', [MailConfigurationController::class, 'index'])->name('index');
            Route::post('/update', [MailConfigurationController::class, 'update'])->name('update');
            Route::post('/send-test-mail', [MailConfigurationController::class, 'sendTestMail'])->name('sendTestMail');
        });

        // ── ListOcean – Map Settings ──────────────────────────────────────────
        Route::prefix('map-settings')->name('mapSettings.')->group(function () {
            Route::get('/', [mapSettingsController::class, 'index'])->name('index');
            Route::match(['post', 'put'], '/', [mapSettingsController::class, 'update'])->name('update');
        });

        // ── ListOcean – Email Templates ──────────────────────────────────────
        Route::prefix('email-templates')->name('emailTemplate.')->group(function () {
            Route::get('/', [emailTemplateController::class, 'index'])->name('index');
            Route::match(['get', 'post'], '/register', [emailTemplateController::class, 'register'])->name('register');
            Route::match(['get', 'post'], '/email-verify', [emailTemplateController::class, 'emailVerify'])->name('emailVerify');
            Route::match(['get', 'post'], '/identity-verification', [emailTemplateController::class, 'identityVerification'])->name('identityVerification');
            Route::match(['get', 'post'], '/wallet-deposit', [emailTemplateController::class, 'walletDeposit'])->name('walletDeposit');
            Route::match(['get', 'post'], '/listing-approval', [emailTemplateController::class, 'listingApproval'])->name('listingApproval');
            Route::match(['get', 'post'], '/listing-publish', [emailTemplateController::class, 'listingPublish'])->name('listingPublish');
            Route::match(['get', 'post'], '/listing-unpublished', [emailTemplateController::class, 'listingUnpublished'])->name('listingUnpublished');
            Route::match(['get', 'post'], '/guest-add-listing', [emailTemplateController::class, 'guestAddListing'])->name('guestAddListing');
            Route::match(['get', 'post'], '/guest-approve-listing', [emailTemplateController::class, 'guestApproveListing'])->name('guestApproveListing');
            Route::match(['get', 'post'], '/guest-publish-listing', [emailTemplateController::class, 'guestPublishListing'])->name('guestPublishListing');
        });

        // ── ListOcean – Notices ──────────────────────────────────────────────
        Route::prefix('site-notices')->name('siteNotice.')->group(function () {
            Route::get('/', [siteNoticeController::class, 'index'])->name('index');
            Route::get('/create', [siteNoticeController::class, 'create'])->name('create');
            Route::post('/', [siteNoticeController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [siteNoticeController::class, 'edit'])->name('edit')->whereNumber('id');
            Route::match(['post', 'put', 'patch'], '/{id}', [siteNoticeController::class, 'update'])->name('update')->whereNumber('id');
            Route::get('/{id}/toggle', [siteNoticeController::class, 'toggleStatus'])->name('toggle')->whereNumber('id');
            Route::match(['post', 'delete'], '/{id}/delete', [siteNoticeController::class, 'destroy'])->name('destroy')->whereNumber('id');
        });

        // ── ListOcean – Page Settings ────────────────────────────────────────
        Route::prefix('page-settings')->name('pageSettings.')->group(function () {
            Route::match(['get', 'post'], '/login-register', [sitePagesettingsController::class, 'loginRegister'])->name('loginRegister');
            Route::match(['get', 'post'], '/listing-create', [sitePagesettingsController::class, 'listingCreate'])->name('listingCreate');
            Route::match(['get', 'post'], '/listing-details', [sitePagesettingsController::class, 'listingDetails'])->name('listingDetails');
            Route::match(['get', 'post'], '/guest-listing', [sitePagesettingsController::class, 'guestListing'])->name('guestListing');
            Route::match(['get', 'post'], '/user-public-profile', [sitePagesettingsController::class, 'userPublicProfile'])->name('userPublicProfile');
        });

        // ── Ads Hub (unified overview) ────────────────────────────────────────
        Route::get('/ads-hub', [AdminAdsHubController::class, 'index'])->name('adsHub.index');

        // ── ListOcean – Advertisements ───────────────────────────────────────
        Route::prefix('site-advertisements')->name('siteAdvertisement.')->group(function () {
            Route::get('/', [siteAdvertisementController::class, 'index'])->name('index');
            Route::get('/create', [siteAdvertisementController::class, 'create'])->name('create');
            Route::post('/', [siteAdvertisementController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [siteAdvertisementController::class, 'edit'])->name('edit')->whereNumber('id');
            Route::match(['post', 'put', 'patch'], '/{id}', [siteAdvertisementController::class, 'update'])->name('update')->whereNumber('id');
            Route::get('/{id}/toggle', [siteAdvertisementController::class, 'toggleStatus'])->name('toggle')->whereNumber('id');
            Route::delete('/{id}', [siteAdvertisementController::class, 'destroy'])->name('destroy')->whereNumber('id');
        });

        // ── ListOcean – Pages (privacy-policy, terms, faq, about, contact…) ──
        Route::prefix('site-pages')->name('sitePages.')->group(function () {
            Route::get('/', [sitePagesController::class, 'index'])->name('index');
            Route::get('/create', [sitePagesController::class, 'create'])->name('create');
            Route::post('/', [sitePagesController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [sitePagesController::class, 'edit'])->name('edit')->whereNumber('id');
            Route::get('/by-slug/{slug}', [sitePagesController::class, 'editBySlug'])->name('editBySlug');
            Route::match(['put', 'patch', 'post'], '/{id}', [sitePagesController::class, 'update'])->name('update')->whereNumber('id');
            Route::delete('/{id}', [sitePagesController::class, 'destroy'])->name('destroy')->whereNumber('id');
        });

        // ── ListOcean – Page Builders (edit visual content blocks per page) ──
        Route::prefix('page-builders')->name('pageBuilders.')->group(function () {
            Route::get('/page/{page_id}', [pageBuildersController::class, 'page'])->name('page')->whereNumber('page_id');
            Route::get('/{pb_id}/edit', [pageBuildersController::class, 'edit'])->name('edit')->whereNumber('pb_id');
            Route::match(['put', 'patch', 'post'], '/{pb_id}', [pageBuildersController::class, 'update'])->name('update')->whereNumber('pb_id');
        });

        Route::prefix('menu')->name('menu.')->group(function () {
            Route::get('/', [MenuController::class, 'index'])->name('index');
            Route::post('/', [MenuController::class, 'store'])->name('store');
            Route::match(['post', 'put', 'patch'], '/{menu}', [MenuController::class, 'update'])->name('update');
            Route::match(['post', 'delete'], '/{menu}/remove', [MenuController::class, 'remove'])->name('remove');
            Route::match(['post', 'delete'], '/{menu}/delete', [MenuController::class, 'destroy'])->name('destroy');
            Route::post('/sort', [MenuController::class, 'sort'])->name('sort');
            Route::post('/drag', [MenuController::class, 'drag'])->name('drag');
        });

        Route::prefix('pusher')->name('pusher.')->group(function () {
            Route::get('/', [PusherConfigController::class, 'index'])->name('index');
            Route::post('/update', [PusherConfigController::class, 'update'])->name('update');
        });

        Route::prefix('contact-us')->name('contactUs.')->group(function () {
            Route::get('/', [ContactUsController::class, 'index'])->name('index');
            Route::post('/{contactUs}', [ContactUsController::class, 'update'])->name('update');
        });

        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('/', [ProfileController::class, 'index'])->name('index');
            Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
            Route::post('/update', [ProfileController::class, 'update'])->name('update');
            Route::get('/change-password', [ProfileController::class, 'changePassword'])->name('change-password');
            Route::post('/update-password', [ProfileController::class, 'updatePassword'])->name('update-password');
        });

        Route::prefix('order')->name('order.')->group(function () {
            Route::get('/{status?}', [OrderController::class, 'index'])->name('index');
            Route::get('/show/{order}', [OrderController::class, 'show'])->name('show');
            Route::get('/{order}/status-change', [OrderController::class, 'statusChange'])->name('status.change');
            Route::get('/{order}/payment-status-toggle', [OrderController::class, 'paymentStatusToggle'])->name('payment.status.toggle');
            Route::get('/{order}/download-invoice', [OrderController::class, 'downloadInvoice'])->name('download-invoice');
            Route::get('/{order}/payment-slip', [OrderController::class, 'paymentSlip'])->name('payment-slip');
        });

        Route::prefix('chat-oversight')->name('chatOversight.')->group(function () {
            Route::get('/', [ChatOversightController::class, 'index'])->name('index');
            Route::get('/{shopUser}', [ChatOversightController::class, 'show'])->name('show');
            Route::post('/{shopUser}/mark-seen', [ChatOversightController::class, 'markSeen'])->name('markSeen');
        });

        Route::prefix('page')->name('page.')->group(function () {
            Route::get('/', [PageController::class, 'index'])->name('index');
            Route::get('/create', [PageController::class, 'create'])->name('create');
            Route::post('/', [PageController::class, 'store'])->name('store');
            Route::get('/{page}/show', [PageController::class, 'show'])->name('show');
            Route::get('/{page}/edit', [PageController::class, 'edit'])->name('edit');
            Route::match(['post', 'put', 'patch'], '/{page}', [PageController::class, 'update'])->name('update');
            Route::match(['post', 'delete'], '/{page}/delete', [PageController::class, 'destroy'])->name('destroy');
            Route::post('/generate-ai-data', [PageController::class, 'generateAIData'])->name('generate.AI.data');
        });

        Route::prefix('payment-gateway')->name('paymentGateway.')->group(function () {
            Route::get('/', [PaymentGatewayController::class, 'index'])->name('index');
            Route::match(['post', 'put', 'patch'], '/{paymentGateway}', [PaymentGatewayController::class, 'update'])->name('update');
            Route::get('/{paymentGateway}/toggle', [PaymentGatewayController::class, 'toggle'])->name('toggle');

            // Website (customer web) Paystack settings
            Route::post('/website/paystack', [PaymentGatewayController::class, 'updateListoceanPaystack'])->name('website.paystack.update');
        });

        Route::prefix('product')->name('product.')->group(function () {
            Route::get('/', [ProductController::class, 'index'])->name('index');
            Route::get('/grid-view', [ProductController::class, 'index'])->name('gridView');
            Route::get('/list-view', [ProductController::class, 'index'])->name('listView');
            Route::get('/{product}', [ProductController::class, 'show'])->name('show');
            Route::get('/{product}/approve', [ProductController::class, 'approve'])->name('approve');
            Route::match(['post', 'delete'], '/{product}/delete', [ProductController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('pwa-setting')->name('pwaSetting.')->group(function () {
            Route::get('/', [GeneraleSettingController::class, 'index'])->name('index');
            Route::post('/update', [GeneraleSettingController::class, 'update'])->name('update');
        });

        // Legacy shop-product review routes (admin DB — currently empty, kept for compatibility)
        Route::prefix('review')->name('review.')->group(function () {
            Route::get('/', [ReviewsController::class, 'index'])->name('index');
            Route::get('/{review}/toggle', [ReviewsController::class, 'toggleReview'])->name('toggle');
        });

        // Listocean user-to-user reviews (the real review system)
        Route::prefix('listocean-reviews')->name('listocean-review.')->group(function () {
            Route::get('/', [ListoceanReviewController::class, 'index'])->name('index');
            Route::get('/{id}/toggle', [ListoceanReviewController::class, 'toggleStatus'])->name('toggle')->whereNumber('id');
            Route::match(['post', 'delete'], '/{id}/delete', [ListoceanReviewController::class, 'destroy'])->name('destroy')->whereNumber('id');
        });

        Route::prefix('rider')->name('rider.')->group(function () {
            Route::get('/{status?}', [RiderController::class, 'index'])->name('index');
            Route::get('/create', [RiderController::class, 'create'])->name('create');
            Route::post('/', [RiderController::class, 'store'])->name('store');
            Route::get('/show/{rider}', [RiderController::class, 'show'])->name('show');
            Route::get('/{rider}/edit', [RiderController::class, 'edit'])->name('edit');
            Route::match(['post', 'put', 'patch'], '/{rider}', [RiderController::class, 'update'])->name('update');
            Route::get('/{rider}/toggle', [RiderController::class, 'statusToggle'])->name('toggle');
            Route::post('/assign-order/{order}', [RiderController::class, 'assignOrder'])->name('assign.order');
        });

        Route::prefix('role')->name('role.')->group(function () {
            Route::get('/', [RolePermissionController::class, 'index'])->name('index');
            Route::get('/create', [RolePermissionController::class, 'index'])->name('create');
            Route::post('/store', [RolePermissionController::class, 'store'])->name('store');
            Route::get('/{role}/edit', [RolePermissionController::class, 'index'])->name('edit');
            Route::match(['post', 'put', 'patch'], '/{role}', [RolePermissionController::class, 'update'])->name('update');
            Route::match(['post', 'delete'], '/{role}/delete', [RolePermissionController::class, 'destroy'])->name('destroy');
            Route::get('/permission/{role}', [RolePermissionController::class, 'rolePermission'])->name('permission');
            Route::post('/permission/{role}', [RolePermissionController::class, 'updateRolePermission'])->name('permission.update');
        });


        Route::prefix('social-auth')->name('socialAuth.')->group(function () {
            Route::get('/', [SocialAuthController::class, 'index'])->name('index');
            Route::match(['post', 'put', 'patch'], '/{socialAuth}', [SocialAuthController::class, 'update'])->name('update');
            Route::get('/{socialAuth}/toggle', [SocialAuthController::class, 'toggle'])->name('toggle');
        });

        Route::prefix('social-link')->name('socialLink.')->group(function () {
            Route::get('/', function () {
                return redirect()->route('admin.socialAuth.index');
            })->name('index');
            Route::match(['post', 'put', 'patch'], '/{socialLink}', function () {
                return redirect()->route('admin.socialAuth.index');
            })->name('update');
            Route::get('/{socialLink}/toggle', function () {
                return redirect()->route('admin.socialAuth.index');
            })->name('toggle');
        });

        Route::prefix('subscription-plan')->name('subscription-plan.')->group(function () {
            Route::get('/', [SubscriptionPlanController::class, 'index'])->name('index');
            Route::get('/create', [SubscriptionPlanController::class, 'create'])->name('create');
            Route::post('/', [SubscriptionPlanController::class, 'store'])->name('store');
            Route::get('/{subscriptionPlan}/edit', [SubscriptionPlanController::class, 'edit'])->name('edit');
            Route::match(['post', 'put', 'patch'], '/{subscriptionPlan}', [SubscriptionPlanController::class, 'update'])->name('update');
            Route::match(['post', 'delete'], '/{subscriptionPlan}/delete', [SubscriptionPlanController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('support')->name('support.')->group(function () {
            Route::get('/', [SupportController::class, 'index'])->name('index');
            Route::match(['post', 'delete'], '/{support}/delete', [SupportController::class, 'delete'])->name('delete');
        });

        Route::prefix('listing-report')->name('listingReport.')->group(function () {
            Route::get('/', [ListingReportController::class, 'index'])->name('index');
            // Use a raw id instead of route-model binding because we moderate Listocean reports via the secondary DB connection.
            Route::get('/{id}', [ListingReportController::class, 'show'])->name('show')->whereNumber('id');
            Route::post('/{id}/status', [ListingReportController::class, 'updateStatus'])->name('status')->whereNumber('id');
            Route::match(['post', 'delete'], '/{id}/delete', [ListingReportController::class, 'destroy'])->name('delete')->whereNumber('id');
        });

        Route::prefix('listing-moderation')->name('listingModeration.')->group(function () {
            Route::get('/', [ListingModerationController::class, 'index'])->name('index');
            // Use a raw id instead of route-model binding because we moderate Listocean listings via the secondary DB connection.
            Route::get('/{id}', [ListingModerationController::class, 'show'])->name('show')->whereNumber('id');
            Route::post('/{id}/status', [ListingModerationController::class, 'updateStatus'])->name('status')->whereNumber('id');
            Route::post('/{id}/publish', [ListingModerationController::class, 'updatePublishStatus'])->name('publish')->whereNumber('id');
            Route::post('/{id}/featured', [ListingModerationController::class, 'updateFeaturedStatus'])->name('featured')->whereNumber('id');
            Route::match(['post', 'delete'], '/{id}/delete', [ListingModerationController::class, 'destroy'])->name('delete')->whereNumber('id');
        });

        Route::prefix('video-moderation')->name('videoModeration.')->group(function () {
            Route::get('/', [VideoModerationController::class, 'index'])->name('index');
            Route::get('/create', [VideoModerationController::class, 'create'])->name('create');
            Route::post('/', [VideoModerationController::class, 'store'])->name('store');
            Route::get('/{id}', [VideoModerationController::class, 'show'])->name('show')->whereNumber('id');
            Route::get('/{id}/edit', [VideoModerationController::class, 'edit'])->name('edit')->whereNumber('id');
            Route::post('/{id}', [VideoModerationController::class, 'update'])->name('update')->whereNumber('id');
            Route::post('/{id}/approve', [VideoModerationController::class, 'updateApproval'])->name('approve')->whereNumber('id');
            Route::post('/{id}/remove-video', [VideoModerationController::class, 'removeVideo'])->name('removeVideo')->whereNumber('id');
        });

        Route::prefix('promo-video-ads')->name('promoVideoAds.')->group(function () {
            Route::get('/', [PromoVideoAdsController::class, 'index'])->name('index');
            Route::get('/create', [PromoVideoAdsController::class, 'create'])->name('create');
            Route::post('/', [PromoVideoAdsController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [PromoVideoAdsController::class, 'edit'])->name('edit')->whereNumber('id');
            Route::post('/{id}', [PromoVideoAdsController::class, 'update'])->name('update')->whereNumber('id');
            Route::post('/{id}/moderate', [PromoVideoAdsController::class, 'quickModerate'])->name('moderate')->whereNumber('id');
        });

        Route::prefix('banner-ad-requests')->name('bannerAdRequests.')->group(function () {
            Route::get('/', [BannerAdRequestsController::class, 'index'])->name('index');
            Route::get('/{id}/edit', [BannerAdRequestsController::class, 'edit'])->name('edit')->whereNumber('id');
            Route::post('/{id}', [BannerAdRequestsController::class, 'update'])->name('update')->whereNumber('id');
            Route::post('/{id}/approve', [BannerAdRequestsController::class, 'approve'])->name('approve')->whereNumber('id');
            Route::post('/{id}/deactivate', [BannerAdRequestsController::class, 'deactivate'])->name('deactivate')->whereNumber('id');
        });

        // ListOcean escrow transaction admin queue
        Route::prefix('escrow')->name('escrow.')->group(function () {
            // Escrow settings page (moved from general settings)
            Route::get('/settings', [ListoceanEscrowController::class, 'settings'])->name('settings');
            Route::post('/settings/update', [ListoceanEscrowController::class, 'updateSettings'])->name('settings.update');

            Route::get('/', [ListoceanEscrowController::class, 'index'])->name('index');
            Route::get('/{id}', [ListoceanEscrowController::class, 'show'])->name('show')->whereNumber('id');
            Route::post('/{id}/release', [ListoceanEscrowController::class, 'adminRelease'])->name('release')->whereNumber('id');
            Route::post('/{id}/refund', [ListoceanEscrowController::class, 'adminRefund'])->name('refund')->whereNumber('id');
            Route::post('/{id}/dispute', [ListoceanEscrowController::class, 'adminDispute'])->name('dispute')->whereNumber('id');
        });

        Route::prefix('report-reason')->name('reportReason.')->group(function () {
            Route::get('/', [ReportReasonController::class, 'index'])->name('index');
            Route::post('/', [ReportReasonController::class, 'store'])->name('store');
            // Use raw ids because we manage Listocean report reasons via the secondary DB connection.
            Route::match(['post', 'put', 'patch'], '/{id}', [ReportReasonController::class, 'update'])->name('update')->whereNumber('id');
            Route::get('/{id}/toggle', [ReportReasonController::class, 'toggle'])->name('toggle')->whereNumber('id');
            Route::match(['post', 'delete'], '/{id}/delete', [ReportReasonController::class, 'destroy'])->name('delete')->whereNumber('id');
        });

        Route::prefix('support-ticket')->name('supportTicket.')->group(function () {
            Route::get('/{status?}', [SupportTicketController::class, 'index'])->name('index');
            Route::get('/show/{supportTicket}', [SupportTicketController::class, 'show'])->name('show');
            Route::post('/{supportTicket}/set-scheduled', [SupportTicketController::class, 'setScheduled'])->name('setScheduled');
            Route::post('/{supportTicket}/send-message', [SupportTicketController::class, 'sendMessage'])->name('sendMessage');
            Route::get('/{supportTicket}/messages', [SupportTicketController::class, 'fetchMessages'])->name('fetchMessages');
            Route::get('/{supportTicket}/status/{status}', [SupportTicketController::class, 'updateStatus'])->name('updateStatus');
            Route::get('/{supportTicket}/chat-toggle', [SupportTicketController::class, 'chatToggle'])->name('chatToggle');
            Route::get('/pin/{message}', [SupportTicketController::class, 'pinMessage'])->name('pinMessage');
        });

        Route::prefix('theme-color')->name('themeColor.')->group(function () {
            Route::get('/', [ThemeColorController::class, 'index'])->name('index');
            Route::post('/update', [ThemeColorController::class, 'update'])->name('update');
            Route::post('/change', [ThemeColorController::class, 'change'])->name('change');
            Route::post('/category-palette', [ThemeColorController::class, 'storeCategoryPalette'])->name('categoryPalette.store');
            Route::post('/category-palette/{categoryThemeColor}/default', [ThemeColorController::class, 'setDefaultCategoryPalette'])->name('categoryPalette.default');
            Route::post('/header-footer-palette', [ThemeColorController::class, 'storeHeaderFooterPalette'])->name('headerFooterPalette.store');
            Route::post('/header-footer-palette/{headerFooterThemeColor}/default', [ThemeColorController::class, 'setDefaultHeaderFooterPalette'])->name('headerFooterPalette.default');
            Route::delete('/category-palette/{categoryThemeColor}', [ThemeColorController::class, 'destroyCategoryPalette'])->name('categoryPalette.destroy');
            Route::delete('/header-footer-palette/{headerFooterThemeColor}', [ThemeColorController::class, 'destroyHeaderFooterPalette'])->name('headerFooterPalette.destroy');
            Route::get('/status/{homeTheme}', [ThemeColorController::class, 'themeStatus'])->name('status');
        });

        Route::prefix('offer-banner')->name('offerBanner.')->group(function () {
            Route::get('/{homeTheme}', [ThemeColorController::class, 'offerBannerIndex'])->name('index');
            Route::get('/edit/{offerBanner}', [ThemeColorController::class, 'offerBannerEdit'])->name('edit');
            Route::match(['post', 'put', 'patch'], '/{offerBanner}', [ThemeColorController::class, 'offerBannerUpdate'])->name('update');
        });

        Route::prefix('ticket-issue-type')->name('ticketIssueType.')->group(function () {
            Route::get('/', [TicketIssueTypeController::class, 'index'])->name('index');
            Route::get('/create', [TicketIssueTypeController::class, 'index'])->name('create');
            Route::post('/', [TicketIssueTypeController::class, 'store'])->name('store');
            Route::get('/{ticketIssueType}/edit', [TicketIssueTypeController::class, 'index'])->name('edit');
            Route::match(['post', 'put', 'patch'], '/{ticketIssueType}', [TicketIssueTypeController::class, 'update'])->name('update');
            Route::get('/{ticketIssueType}/toggle', [TicketIssueTypeController::class, 'toggleStatus'])->name('toggle');
            Route::match(['post', 'delete'], '/{ticketIssueType}/delete', [TicketIssueTypeController::class, 'destroy'])->name('delete');
        });

        Route::prefix('vat-tax')->name('vatTax.')->group(function () {
            Route::get('/', [VatTaxController::class, 'index'])->name('index');
            Route::get('/create', [VatTaxController::class, 'index'])->name('create');
            Route::post('/', [VatTaxController::class, 'store'])->name('store');
            Route::get('/{vatTax}/edit', [VatTaxController::class, 'index'])->name('edit');
            Route::match(['post', 'put', 'patch'], '/{vatTax}', [VatTaxController::class, 'update'])->name('update');
            Route::get('/{vatTax}/toggle', [VatTaxController::class, 'toggle'])->name('toggle');
            Route::match(['post', 'delete'], '/{vatTax}/delete', [VatTaxController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('verification')->name('verification.')->group(function () {
            Route::get('/', [VerifyManageController::class, 'index'])->name('index');
            Route::post('/update', [VerifyManageController::class, 'update'])->name('update');
        });

        Route::prefix('whats-app-chat')->name('whatsAppChat.')->group(function () {
            Route::get('/', [WhatsAppChatController::class, 'index'])->name('index');
            Route::get('/phone-list', [WhatsAppChatController::class, 'phoneList'])->name('phoneList');
            Route::post('/send-message', [WhatsAppChatController::class, 'sendMessage'])->name('sendMessage');
            Route::get('/message-show/{phone_number}', [WhatsAppChatController::class, 'messageShow'])->name('messageShow');
            Route::get('/incoming-message', [WhatsAppChatController::class, 'incomingMessage'])->name('incomingMessage');
        });

        Route::get('/whats-app/user-list', [WhatsAppChatController::class, 'phoneList'])->name('whatsApp.userlist');

        Route::prefix('withdraw')->name('withdraw.')->group(function () {
            Route::get('/{status?}', [WithdrawController::class, 'index'])->name('index');
            Route::get('/show/{withdraw}', [WithdrawController::class, 'show'])->name('show');
            Route::post('/{withdraw}', [WithdrawController::class, 'update'])->name('update');
        });

        Route::prefix('notification')->name('notification.')->group(function () {
            Route::get('/show', [NotificationController::class, 'show'])->name('show');
            Route::get('/read/{notification}', [NotificationController::class, 'markAsRead'])->name('read');
            Route::get('/read-all', [NotificationController::class, 'markAllAsRead'])->name('readAll');
            Route::match(['post', 'delete'], '/delete/{notification}', [NotificationController::class, 'destroy'])->name('destroy');
        });

        Route::get('/new-notification', [NotificationController::class, 'index'])->name('new.notification');
    });
});

Route::prefix('shop')->name('shop.')->group(function () {
    // Shop portal removed — all management is done through /admin
    Route::get('/login', function () {
        abort(404);
    })->name('login');

    Route::get('/dashboard', function () {
        return redirect()->route('admin.dashboard.index');
    })->name('dashboard.index');

    Route::post('/logout', [AdminLoginController::class, 'logout'])->middleware('auth')->name('logout');
});

// Marketplace / Addons removed from admin flow.
// Keep named routes as redirects because some legacy layout views still call route('marketplace.*').
Route::get('/marketplace', function () {
    return redirect()->route('admin.dashboard.index');
})->name('marketplace.index');

Route::get('/marketplace/upgrade', function () {
    return redirect()->route('admin.dashboard.index');
})->name('marketplace.upgrade');

Route::get('/marketplace/addons', function () {
    return redirect()->route('admin.dashboard.index');
})->name('marketplace.addons');

Route::get('/marketplace/{any}', function () {
    return redirect()->route('admin.dashboard.index');
})->where('any', '.*');
