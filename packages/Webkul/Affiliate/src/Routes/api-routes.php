<?php

use Illuminate\Support\Facades\Route;
use Webkul\Affiliate\Http\Controllers\Api\AffiliateApiController;

$registerRoutes = function () {
    Route::controller(AffiliateApiController::class)->group(function () {
        Route::get('dashboard', 'dashboard')->name('api.affiliate.dashboard');
        Route::post('register', 'register')->name('api.affiliate.register');
        Route::post('generate-link', 'generateLink')->name('api.affiliate.generate-link');
        Route::get('product-share-info/{productId}', 'productShareInfo')->name('api.affiliate.product-share-info');
        Route::post('payout-settings', 'updatePayoutSettings')->name('api.affiliate.payout-settings');
        Route::get('wallet/transactions', 'transactions')->name('api.affiliate.wallet.transactions');
        Route::get('commissions', 'commissions')->name('api.affiliate.commissions');
        Route::post('payout-request', 'requestPayout')->name('api.affiliate.payout-request');
        Route::get('payouts', 'payouts')->name('api.affiliate.payouts');
        Route::post('track-referral', 'trackReferral')->name('api.affiliate.track-referral');
    });
};

Route::group(['prefix' => 'api/v1/affiliate'], $registerRoutes);
Route::group(['prefix' => 'api/affiliate'], $registerRoutes);
