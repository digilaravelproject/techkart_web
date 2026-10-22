<?php

use Illuminate\Support\Facades\Route;
use Webkul\Affiliate\Http\Controllers\Admin\AffiliateController;
use Webkul\Affiliate\Http\Controllers\Admin\CommissionRuleController;
use Webkul\Affiliate\Http\Controllers\Admin\PayoutController;
use Webkul\Core\Http\Middleware\NoCacheMiddleware;

Route::group(['middleware' => ['web', 'admin', NoCacheMiddleware::class], 'prefix' => config('app.admin_url')], function () {
    Route::prefix('affiliate')->group(function () {
        Route::controller(AffiliateController::class)->group(function () {
            Route::get('', 'index')->name('admin.affiliate.index');
        });

        Route::controller(CommissionRuleController::class)->prefix('rules')->group(function () {
            Route::get('', 'index')->name('admin.affiliate.rules.index');
            Route::post('', 'store')->name('admin.affiliate.rules.store');
            Route::delete('{id}', 'destroy')->name('admin.affiliate.rules.delete');
        });

        Route::controller(PayoutController::class)->prefix('payouts')->group(function () {
            Route::get('', 'index')->name('admin.affiliate.payouts.index');
            Route::post('{id}/complete', 'complete')->name('admin.affiliate.payouts.complete');
            Route::post('{id}/reject', 'reject')->name('admin.affiliate.payouts.reject');
        });
    });
});
