<?php

namespace Webkul\Affiliate\Providers;

use Illuminate\Support\ServiceProvider;
use Webkul\Affiliate\Console\Commands\ReleasePendingCommissions;
use Webkul\Affiliate\Http\Middleware\TrackAffiliateReferral;

class AffiliateServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->registerConfig();
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->app['router']->pushMiddlewareToGroup('web', TrackAffiliateReferral::class);

        $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');

        $this->loadTranslationsFrom(__DIR__.'/../Resources/lang', 'affiliate');

        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'affiliate');

        $this->loadRoutesFrom(__DIR__.'/../Routes/admin-routes.php');

        $this->loadRoutesFrom(__DIR__.'/../Routes/shop-routes.php');

        $this->loadRoutesFrom(__DIR__.'/../Routes/api-routes.php');

        if ($this->app->runningInConsole()) {
            $this->commands([
                ReleasePendingCommissions::class,
            ]);
        }

        $this->app->register(EventServiceProvider::class);
    }

    /**
     * Register package configurations.
     */
    protected function registerConfig(): void
    {
        $this->mergeConfigFrom(
            dirname(__DIR__).'/Config/admin-menu.php',
            'menu.admin'
        );

        $this->mergeConfigFrom(
            dirname(__DIR__).'/Config/customer-menu.php',
            'menu.customer'
        );

        $this->mergeConfigFrom(
            dirname(__DIR__).'/Config/acl.php',
            'acl'
        );

        $this->mergeConfigFrom(
            dirname(__DIR__).'/Config/system.php',
            'core'
        );
    }
}
