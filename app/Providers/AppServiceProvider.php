<?php

namespace App\Providers;

use Barryvdh\Debugbar\Facades\Debugbar;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\ParallelTesting;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $allowedIPs = array_map('trim', explode(',', config('app.debug_allowed_ips', '')));

        $allowedIPs = array_filter($allowedIPs);

        if (empty($allowedIPs)) {
            return;
        }

        if (in_array(Request::ip(), $allowedIPs)) {
            Debugbar::enable();
        } else {
            Debugbar::disable();
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ParallelTesting::setUpTestDatabase(function (string $database, int $token) {
            Artisan::call('db:seed');
        });

        if (
            config('app.force_https')
            || app()->environment('production')
            || str_starts_with((string) config('app.url'), 'https://')
            || request()->isSecure()
            || request()->header('X-Forwarded-Proto') === 'https'
            || request()->header('X-Forwarded-Ssl') === 'on'
            || request()->server('HTTP_X_FORWARDED_PROTO') === 'https'
            || request()->server('HTTP_X_FORWARDED_SSL') === 'on'
            || request()->server('HTTPS') === 'on'
            || str_contains((string) request()->getHost(), 'benzoo.in')
        ) {
            URL::forceScheme('https');
            request()->server->set('HTTPS', 'on');
            $_SERVER['HTTPS'] = 'on';
        }
    }
}
