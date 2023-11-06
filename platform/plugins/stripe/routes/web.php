<?php

use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'Botble\Stripe\Http\Controllers', 'middleware' => ['web', 'core']], function () {
    Route::get('payment/stripe/success', 'StripeController@success')->name('payments.stripe.success');
    Route::get('payment/stripe/error', 'StripeController@error')->name('payments.stripe.error');
    Route::get('payment/stripe/connect-link', 'StripeController@connect')->name('payments.stripe.connect-link');
});
