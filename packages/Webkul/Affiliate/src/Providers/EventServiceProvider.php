<?php

namespace Webkul\Affiliate\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Event::listen('customer.create.after', 'Webkul\Affiliate\Listeners\CustomerRegistrationListener@handle');

        Event::listen('checkout.order.save.after', 'Webkul\Affiliate\Listeners\OrderPlacementListener@handle');

        Event::listen('sales.order.cancel.after', 'Webkul\Affiliate\Listeners\OrderPlacementListener@handleCancel');

        Event::listen('sales.refund.save.after', 'Webkul\Affiliate\Listeners\OrderPlacementListener@handleRefund');

        Event::listen('bagisto.shop.products.view.add_to_cart.after', function ($viewRenderEventManager) {
            $viewRenderEventManager->addTemplate('affiliate::shop.products.share-earn');
        });
    }
}
