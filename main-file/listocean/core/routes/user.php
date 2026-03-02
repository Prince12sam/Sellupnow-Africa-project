<?php

use App\Http\Controllers\Frontend\User\AiListingAssistantController;
use App\Http\Controllers\Frontend\User\ListingFavoriteController;
use App\Http\Controllers\Frontend\User\NotificationController;
use App\Http\Controllers\Frontend\User\DashboardController;
use App\Http\Controllers\Frontend\User\UserController;
use App\Http\Controllers\Frontend\User\AccountSettingController;
use App\Http\Controllers\Frontend\User\ListingController;
use App\Http\Controllers\Frontend\User\WalletController;
use App\Http\Controllers\Frontend\User\MembershipController;
use App\Http\Controllers\Frontend\User\FeaturedAdController;
use App\Http\Controllers\Frontend\User\EscrowController;
use App\Http\Controllers\Frontend\User\BannerAdController;
use App\Http\Controllers\Frontend\User\UserVideoController;

// client
Route::group(['prefix'=>'user','as'=>'user.'],function() {

    Route::group(['middleware'=>['auth','globalVariable', 'maintains_mode','setlang']],function(){
        Route::controller(UserController::class)->group(function () {
            Route::get('profile/logout','logout')->name('logout');
        });
    });

      Route::group(['middleware'=>['auth','userEmailVerify', 'globalVariable', 'maintains_mode','setlang']],function(){
        Route::controller(UserController::class)->group(function () {
            Route::get('profile/settings','profile')->name('profile');
            Route::post('profile/edit-profile','edit_profile')->name('profile.edit');
            Route::match(['get','post'],'profile/identity-verification','identity_verification')->name('identity.verification');
            Route::post('profile/check-password','check_password')->name('password.check');
            Route::match(['get','post'],'profile/change-password','change_password')->name('password');
            Route::get('my-reviews','myReviews')->name('my.reviews');
            Route::get('blocked-users','blockedUsers')->name('blocked.users');
            Route::post('block/{id}','blockUser')->name('block.user');
            Route::delete('unblock/{id}','unblockUser')->name('unblock.user');
        });

        // User video uploads (ad_videos)
        Route::controller(UserVideoController::class)->group(function () {
            Route::prefix('my-videos')->group(function () {
                Route::get('/',         'index')->name('my.videos');
                Route::get('/create',   'create')->name('my.videos.create');
                Route::post('/',        'store')->name('my.videos.store')->middleware('throttle:5,1');
                Route::get('/{id}/edit',    'edit')->name('my.videos.edit');
                Route::post('/{id}/update', 'update')->name('my.videos.update')->middleware('throttle:10,1');
                Route::post('/{id}/delete', 'destroy')->name('my.videos.destroy');
            });
        });

       // user account settings
        Route::controller(AccountSettingController::class)->group(function () {
            Route::match(['get','post'],'/account-settings','userAccountSetting')->name('account.settings');
            Route::post('/account-deactive','accountDeactive')->name('account.deactive');
            Route::get('/account-deactive/cancel/{id}','accountDeactiveCancel')->name('account.deactive.cancel');
            Route::post('account/delete','accountDelete')->name('account.delete');
        });

        // notifications
        Route::controller(NotificationController::class)->group(function () {
            Route::group(['prefix'=>'notification'],function(){
                Route::get('list','index')->name('notification.index');
                Route::post('read','read_notification')->name('notification.read');
            });
        });

        //dashboard
        Route::controller(DashboardController::class)->group(function () {
            Route::group(['prefix'=>'dashboard'],function(){
                Route::get('info','dashboard')->name('dashboard');
                Route::get('analytics','analytics')->name('analytics');
            });
        });

          // add listing
          Route::controller(ListingController::class)->group(function () {
              Route::group(['prefix'=>'listing'],function(){
                  Route::get('all','allListing')->name('all.listing');
                  Route::match(['get','post'],'/add','addListing')->name('add.listing');
                  Route::match(['get','post'],'/edit/{id?}','editListing')->name('edit.listing');
                  Route::post('delete/{id?}','deleteListing')->name('delete.listing');
                  Route::post('published-on-off/{id}', 'listingPublishedStatus')->name('listing.published.status');
                  Route::post('boost/{id}', 'boostListing')->name('listing.boost');
              });
          });

          // SmartAI Listing Assistant — AJAX endpoint for title/description suggestions
          Route::post('ai/listing-suggest', [AiListingAssistantController::class, 'suggest'])
              ->name('ai.listing.suggest')
              ->middleware('throttle:30,1');

          //seller profile verify
          Route::post('user-profile-verify', [AccountSettingController::class, 'userProfileVerify'])->name('profile.verify');

          // wallet
          Route::controller(WalletController::class)->group(function () {
              Route::prefix('wallet')->group(function () {
                  Route::get('/',                  'index')->name('wallet.index');
                  Route::get('/topup',             'topupForm')->name('wallet.topup');
                  Route::post('/topup',            'topupSubmit')->name('wallet.topup.submit');
                  Route::get('/paystack/callback', 'paystackCallback')->name('wallet.paystack.callback');
              });
          });

          // membership plans
          Route::controller(MembershipController::class)->group(function () {
              Route::prefix('membership')->group(function () {
                  Route::get('/',          'plans')->name('membership.plans');
                  Route::post('/subscribe','subscribe')->name('membership.subscribe')->middleware('throttle:5,1');
                  Route::post('/cancel',   'cancel')->name('membership.cancel')->middleware('throttle:10,1');
              });
          });

          // featured ads
          Route::controller(FeaturedAdController::class)->group(function () {
              Route::prefix('featured-ads')->group(function () {
                  Route::get('/',          'index')->name('featuredAds.index');
                  Route::get('/packages',  'packages')->name('featuredAds.packages');
                  Route::post('/purchase', 'purchase')->name('featuredAds.purchase');
              });
          });

          // escrow
          Route::controller(EscrowController::class)->group(function () {
              Route::prefix('escrow')->group(function () {
                  Route::get('/start/{slug}',   'start')->name('escrow.start');
                  Route::post('/checkout',      'checkout')->name('escrow.checkout');
                  Route::get('/orders',         'orders')->name('escrow.orders');
                  Route::get('/{id}',           'detail')->name('escrow.detail');
                  Route::post('/{id}/accept',   'accept')->name('escrow.accept');
                  Route::post('/{id}/deliver',  'deliver')->name('escrow.deliver');
                  Route::post('/{id}/confirm',  'confirm')->name('escrow.confirm');
                  Route::post('/{id}/dispute',  'dispute')->name('escrow.dispute');
              });
          });

          // banner ads
          Route::controller(BannerAdController::class)->group(function () {
              Route::prefix('banner-ads')->group(function () {
                  Route::get('/',        'index')->name('banner-ads.index');
                  Route::get('/request', 'create')->name('banner-ads.create');
                  Route::post('/request','store')->name('banner-ads.store');
              });
          });

    });


      // user  listing favorite items
      Route::group(['middleware'=>['globalVariable', 'maintains_mode','setlang']],function(){
        Route::controller(ListingFavoriteController::class)->group(function () {
            Route::get('favorite/listing/all','ListingFavoriteAll')->name('listing.favorite.all');
        });
    });

});
