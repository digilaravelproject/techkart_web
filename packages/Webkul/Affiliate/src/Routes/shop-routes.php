<?php

use Illuminate\Support\Facades\Route;
use Webkul\Affiliate\Http\Controllers\Shop\AffiliateController;
use Webkul\Affiliate\Http\Controllers\Shop\LinkGeneratorController;
use Webkul\Affiliate\Http\Controllers\Shop\WalletController;
use Webkul\Core\Http\Middleware\NoCacheMiddleware;

Route::group(['middleware' => ['web', 'shop', 'customer', NoCacheMiddleware::class], 'prefix' => 'customer/account/affiliate'], function () {
    Route::get('', [AffiliateController::class, 'index'])->name('shop.customers.account.affiliate.index');
    Route::post('payout-settings', [WalletController::class, 'updatePayoutSettings'])->name('shop.customers.account.affiliate.payout_settings');
    Route::post('request-payout', [WalletController::class, 'requestPayout'])->name('shop.customers.account.affiliate.request_payout');
    Route::post('generate-link', [LinkGeneratorController::class, 'generate'])->name('shop.customers.account.affiliate.generate_link');
});
