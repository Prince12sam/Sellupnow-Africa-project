<?php

use App\Http\Controllers\Frontend\AiRecommendationController;
use App\Http\Controllers\Backend\AdminProfileController;
use App\Http\Controllers\Common\GetCategoryController;
use App\Http\Controllers\Common\NewTagAddController;
use App\Http\Controllers\Frontend\CategoryWiseListingController;
use App\Http\Controllers\Frontend\FrontendAdvertisementController;
use App\Http\Controllers\Frontend\FrontendSearchController;
use App\Http\Controllers\Frontend\ListingReportController;
use App\Http\Controllers\Frontend\SocialLoginController;
use App\Http\Controllers\Frontend\TrendingVideoController;
use App\Http\Controllers\Frontend\ReelController;
use App\Http\Controllers\Frontend\User\GuestMediaUploadController;
use App\Http\Controllers\Frontend\User\ListingController;
use App\Http\Controllers\Frontend\User\ListingFavoriteController;
use App\Http\Controllers\Frontend\UserReviewController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use \App\Http\Controllers\Frontend\User\MediaUploadController;
use \App\Http\Controllers\Frontend\FrontendListingController;
use \App\Http\Controllers\Frontend\FrontendUserProfileController;

require_once __DIR__ . '/user.php';

Route::any('/_ignition/{any?}', function () {
    abort(404);
})->where('any', '.*');

Route::any('/update/{any?}', function () {
    abort(404);
})->where('any', '.*');

Route::any('/admin/{any?}', function () {
    abort(404);
})->where('any', '.*');

Route::group(['middleware' => ['globalVariable', 'maintains_mode','setlang']], function () {
    // user login
    Route::controller(LoginController::class)->group(function(){
        Route::get('login', 'userLogin')->name('user.login');
        Route::post('login', 'userLogin')->middleware('throttle:5,1')->name('user.login.post');
        Route::match(['get', 'post'], 'forget-password', 'forgetPassword')->name('user.forgot.password');
        Route::match(['get', 'post'], 'password-reset-otp', 'passwordResetOtp')->name('user.forgot.password.otp');
        Route::post('password-reset', 'passwordReset')->middleware('throttle:5,1')->name('user.forgot.password.reset');
        Route::get('password-reset', 'passwordReset')->name('user.forgot.password.reset.get');
    });

    // user social login
    Route::controller(SocialLoginController::class)->group(function(){
        Route::get('facebook/callback', 'facebook_callback')->name('facebook.callback');
        Route::get('facebook/redirect', 'facebook_redirect')->name('login.facebook.redirect');
        Route::get('google/callback', 'google_callback')->name('google.callback');
        Route::get('google/redirect', 'google_redirect')->name('login.google.redirect');
    });

   // user registration
    Route::controller(RegisterController::class)->group(function(){
        Route::post('user-name-availability','userNameAvailability')->name('user.name.availability');
        Route::post('email-availability','emailAvailability')->name('user.email.availability');
        Route::post('phone-number-availability','phoneNumberAvailability')->name('user.phone.number.availability');
        Route::match(['get','post'],'user-register','userRegister')->name('user.register');
        Route::match(['get', 'post'], 'email-verify', 'emailVerify')->name('email.verify')->middleware('auth:web');
        Route::get('resend-verify-code-again', 'resendCode')->name('resend.verify.code')->middleware('auth:web');
    });

    Route::group(['middleware' => ['setlang', 'globalVariable']], function () {
        // public routes for user and admin
        Route::controller(\App\Http\Controllers\Common\AdminUserController::class)->group(function(){
            Route::post('get-state','get_country_state')->name('au.state.all');
            Route::post('get-city','get_state_city')->name('au.city.all');
            Route::post('get-subcategory','get_subcategory')->name('au.subcategory.all');
        });

        Route::post('/add-new-tag',  [NewTagAddController::class, 'addNewTag'])->name('add.new.tag');
        // get category, subcategory, child category for select
        Route::post('get-subcategory',[GetCategoryController::class, 'get_sub_category'])->name('get.subcategory');
        Route::post('get-child-category',[GetCategoryController::class, 'get_child_category'])->name('get.subcategory.with.child.category');
    });


    // listings
    Route::group(['prefix' => 'listing'], function () {
        Route::get('/{slug?}', [FrontendListingController::class, 'frontendListingDetails'])->name('frontend.listing.details');
        Route::post('/load-more-relevant', [FrontendListingController::class, 'loadMoreListing'])->name('frontend.listing.load-more-relevant');
        Route::post('/ai-recommendations', [AiRecommendationController::class, 'recommend'])->name('frontend.ai.recommendations')->middleware('throttle:20,1');

        // category, subcategory & child wise listing
        Route::controller(CategoryWiseListingController::class)->group(function(){
            Route::get('category/{slug?}', 'showListingsByCategory')->name('frontend.show.listing.by.category');
            Route::get('sub-category/{slug?}', 'showListingsBySubCategory')->name('frontend.show.listing.by.subcategory');
            Route::get('child-category/{slug?}', 'showListingsByChildCategory')->name('frontend.show.listing.by.child.category');
            Route::post('/load-more/subs-category', 'loadMoreSubCategories')->name('frontend.listing.load.more.subcategories');
            Route::post('/load-more/child-category', 'loadMoreChildCategories')->name('frontend.listing.load.more.child.categories');
        });
    });
    // frontend user profile view
    Route::group(['prefix' => 'profile'], function () {
        Route::get('/{slug?}', [FrontendUserProfileController::class, 'frontendUserProfileView'])->name('about.user.profile');
    });
    // frontend custom form builders
    Route::post('submit-custom-form', [\App\Http\Controllers\Frontend\FrontendFormController::class, 'custom_form_builder_message'])->name('frontend.form.builder.custom.submit');

    Route::get('/explore', [TrendingVideoController::class, 'explore'])->name('frontend.trending.videos');

    // reels feed — must come BEFORE the /{slug} catch-all
    Route::get('/reels', [ReelController::class, 'index'])->name('reels.index');
    Route::get('/reels/load', [ReelController::class, 'load'])->name('reels.load');
    Route::get('/reels/{id}', [TrendingVideoController::class, 'watch'])->whereNumber('id')->name('frontend.reel.watch');
    Route::get('/reels/{id}/comments', [TrendingVideoController::class, 'comments'])->whereNumber('id')->name('frontend.reel.comments');
    Route::post('/reels/{id}/comments', [TrendingVideoController::class, 'storeComment'])->whereNumber('id')->middleware('auth:web')->name('frontend.reel.comment.store');
    Route::redirect('/membership', '/explore', 301);

    //dynamic single page
    Route::controller(App\Http\Controllers\Frontend\FrontendController::class)->group(function(){
        Route::get('/','home_page')->name('homepage');
        Route::get('/{slug}', 'dynamic_single_page')->name('frontend.dynamic.page');
    });
    // listing favorite
    Route::post('favorite/listing-add-remove',[ListingFavoriteController::class, 'listingFavoriteAddRemove'])->name('listing.favorite.add.remove');

    // listing report
    Route::post('listing/report-add',[ListingReportController::class, 'listingReportAdd'])->name('listing.report.add');
    // user review
    Route::post('user/review-add',[UserReviewController::class, 'listingReviewAdd'])->name('user.review.add');

    // any listing search in frontend page or other page route
    Route::get('/home-search/listings', [FrontendSearchController::class, 'home_search'])->name('frontend.home.search');
    Route::post('/get-state-by-country', [FrontendSearchController::class, 'getState'])->name('user.country.state');
    Route::get('/get-state-by-country-ajax-search', [FrontendSearchController::class, 'getStateAjaxSearch'])->name('user.country.state.ajax.search');
    Route::post('/get-city-by-city', [FrontendSearchController::class, 'getCity'])->name('user.state.city');


    // guest listing add without login
    Route::controller(\App\Http\Controllers\Frontend\GuestListingController::class)->group(function () {
        Route::group(['prefix'=>'listing'],function(){
            Route::match(['get','post'],'/guest/add-listing','guestAddListing')->name('guest.add.listing');
            Route::post('/guest/request-check','guestRequestCheck')->name('guest.request.check');
        });
    });

    // advertisement click and impression count route
    Route::get('/home/advertisement/click/store',[FrontendAdvertisementController::class, 'home_advertisement_click_store'])->name('frontend.home.advertisement.click.store');
    Route::get('/home/advertisement/impression/store',[FrontendAdvertisementController::class, 'home_advertisement_impression_store'])->name('frontend.home.advertisement.impression.store');

    // media upload routes for User
    Route::group(['middleware'=>['auth','inactiveuser']],function(){
        Route::group(['namespace'=>'User'],function(){
            Route::post('/media-upload/all',[MediaUploadController::class, 'allUploadMediaFile'])->name('web.upload.media.file.all');
            Route::post('/media-upload',[MediaUploadController::class, 'uploadMediaFile'])->name('web.upload.media.file');
            Route::post('/media-upload/alt',[MediaUploadController::class, 'altChangeUploadMediaFile'])->name('web.upload.media.file.alt.change');
            Route::post('/media-upload/delete',[MediaUploadController::class, 'deleteUploadMediaFile'])->name('web.upload.media.file.delete');
            Route::post('/media-upload/loadmore', [MediaUploadController::class, 'getImageForLoadmore'])->name('web.upload.media.file.loadmore');
        });
    });

  // guest media upload routes for Guest
    Route::group(['middleware'=>['guest_media_upload_check']],function(){
        Route::group(['namespace'=>'Guest'],function(){
            Route::post('/guest/media-upload/all',[GuestMediaUploadController::class, 'allUploadMediaFile'])->name('web.guest.upload.media.file.all');
            Route::post('/guest/media-upload',[GuestMediaUploadController::class, 'uploadMediaFile'])->name('web.guest.upload.media.file');
            Route::post('/guest/media-upload/alt',[GuestMediaUploadController::class, 'altChangeUploadMediaFile'])->name('web.guest.upload.media.file.alt.change');
            Route::post('/guest/media-upload/delete',[GuestMediaUploadController::class, 'deleteUploadMediaFile'])->name('web.guest.upload.media.file.delete');
            Route::post('/guest/media-upload/loadmore', [GuestMediaUploadController::class, 'getImageForLoadmore'])->name('web.guest.upload.media.file.loadmore');
        });
    });
});
