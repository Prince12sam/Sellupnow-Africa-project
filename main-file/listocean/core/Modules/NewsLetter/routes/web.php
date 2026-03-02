<?php

use Illuminate\Support\Facades\Route;
use Modules\NewsLetter\app\Http\Controllers\Backend\AdminNewsLetterController;
use Modules\NewsLetter\app\Http\Controllers\Frontend\NewsLetterController;


// Admin routes
Route::group(['as' => 'admin.', 'prefix' => 'admin/news-letter', 'middleware' => ['auth:admin', 'setlang']], function () {
    Route::get('/', [AdminNewsLetterController::class, 'index'])->name('newsletter.index')->permission("newsletter-list");
    Route::post('/delete/{id}', [AdminNewsLetterController::class, 'delete'])->name('newsletter.delete')->permission("newsletter-delete");
    Route::post('/single', [AdminNewsLetterController::class, 'send_mail'])->name('newsletter.single.mail')->permission("newsletter-single");
    Route::get('/all', [AdminNewsLetterController::class, 'send_mail_all_index'])->name('newsletter.mail');
    Route::post('/all', [AdminNewsLetterController::class, 'send_mail_all']);
    Route::post('/new', [AdminNewsLetterController::class, 'add_new_sub'])->name('newsletter.new.add')->permission("newsletter-add");
    Route::post('/bulk-action', [AdminNewsLetterController::class, 'bulk_action'])->name('newsletter.bulk.action')->permission("newsletter-bulk-delete");
    Route::post('/newsletter/verify-mail-send', [AdminNewsLetterController::class, 'verify_mail_send'])->name('newsletter.verify.mail.send')->permission("newsletter-newsletter-verify-mail-send");
});

// user routes in frontend
Route::post('/news-letter/subscribe/by/user', [NewsLetterController::class, 'subscribe'])->name('newsletter.subscription');
Route::get('/news-letter/user/email/verify/{token}',[NewsLetterController::class, 'subscriber_verify'])->name('newsletter.subscriber.verify');
