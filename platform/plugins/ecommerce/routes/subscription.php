<?php

use Botble\Base\Facades\BaseHelper;
use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'Botble\Ecommerce\Http\Controllers', 'middleware' => ['web', 'core']], function () {
    Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => 'auth'], function () {
        Route::group(['prefix' => 'subscriptions', 'as' => 'subscriptions.'], function () {
            Route::get('/', [
                'as' => 'index',
                'uses' => 'SubscriptionController@index',
                'permission' => 'orders.index',
            ]);

            Route::get('view/{id}/{vendor_id}', [
                'as' => 'view',
                'uses' => 'SubscriptionController@getViewSubscription',
                'permission' => 'orders.view',
            ]);

            Route::get('cancel/{id}/{vendor_id}', [
                'as' => 'cancel',
                'uses' => 'SubscriptionController@getCancelSubscription',
                'permission' => 'orders.edit',
            ]);
        });

    });
});
