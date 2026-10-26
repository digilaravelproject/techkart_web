<?php

use Illuminate\Support\Facades\Route;
use Webkul\Affiliate\Http\Controllers\Api\AffiliateApiController;

$registerRoutes = function () {
    Route::controller(AffiliateApiController::class)->group(function () {
        Route::get('dashboard', 'dashboard')->name('dashboard');
        Route::post('register', 'register')->name('register');
        Route::post('generate-link', 'generateLink')->name('generate-link');
        Route::get('product-share-info/{productId}', 'productShareInfo')->name('product-share-info');
        Route::post('payout-settings', 'updatePayoutSettings')->name('payout-settings');
        Route::get('wallet/transactions', 'transactions')->name('wallet.transactions');
        Route::get('commissions', 'commissions')->name('commissions');
        Route::post('payout-request', 'requestPayout')->name('payout-request');
        Route::get('payouts', 'payouts')->name('payouts');
        Route::post('track-referral', 'trackReferral')->name('track-referral');
    });
};

Route::group(['prefix' => 'api/v1/affiliate', 'as' => 'api.v1.affiliate.'], $registerRoutes);
Route::group(['prefix' => 'api/affiliate', 'as' => 'api.affiliate.'], $registerRoutes);

