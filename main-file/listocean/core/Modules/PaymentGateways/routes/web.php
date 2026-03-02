<?php

use Illuminate\Support\Facades\Route;
use Modules\PaymentGateways\app\Http\Controllers\PaymentGatewaysController;

 Route::group(['prefix' => 'admin/payment-settings/payment', 'middleware' => ['auth:admin', 'setlang']], function () {
    Route::controller(PaymentGatewaysController::class)->name('admin.payment.settings.')->group(function () {
        Route::get('/paypal', 'paypal_settings')->name('paypal');
        Route::get('/paytm', 'paytm_settings')->name('paytm');
        Route::get('/stripe', 'stripe_settings')->name('stripe');
        Route::get('/razorpay', 'razorpay_settings')->name('razorpay');
        Route::get('/paystack', 'paystack_settings')->name('paystack');
        Route::get('/mollie', 'mollie_settings')->name('mollie');
        Route::get('/midtrans', 'midtrans_settings')->name('midtrans');
        Route::get('/cashfree', 'cashfree_settings')->name('cashfree');
        Route::get('/instamojo', 'instamojo_settings')->name('instamojo');
        Route::get('/marcadopago', 'marcadopago_settings')->name('marcadopago');
        Route::get('/zitopay', 'zitopay_settings')->name('zitopay');
        Route::get('/squareup', 'squareup_settings')->name('squareup');
        Route::get('/cinetpay', 'cinetpay_settings')->name('cinetpay');
        Route::get('/paytabs', 'paytabs_settings')->name('paytabs');
        Route::get('/billplz', 'billplz_settings')->name('billplz');
        Route::get('/toyyibpay', 'toyyibpay_settings')->name('toyyibpay');
        Route::get('/flutterwave', 'flutterwave_settings')->name('flutterwave');
        Route::get('/payfast', 'payfast_settings')->name('payfast');
        Route::get('/manual-payment', 'manual_payment_settings')->name('manual_payment');
        Route::get('/cash-on-delivery', 'cod_settings')->name('cod');
        Route::post('/update', 'update_payment_settings')->name('update');
    });
});


/*------------------ PAYMENT GATEWAY SETTINGS MANAGE --------------*/
Route::group(['prefix' => 'admin/payment-gateway', 'middleware' => ['auth:admin', 'setlang']], function () {
    Route::get('/currency-settings',[PaymentGatewaysController::class, 'paymentGatewaySettings'])->name('admin.payment.gateway.currency.settings')->permission('payment-currency-settings');
    Route::post('/currency-settings',[PaymentGatewaysController::class, 'paymentGatewaySettingsUpdate']);
});
