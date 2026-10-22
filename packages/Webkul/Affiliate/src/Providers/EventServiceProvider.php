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

        Event::listen('sales.order.update-status.after', 'Webkul\Affiliate\Listeners\OrderPlacementListener@handleStatusUpdate');

        Event::listen('bagisto.shop.products.view.additional_actions.before', function ($viewRenderEventManager) {
            $viewRenderEventManager->addTemplate('affiliate::shop.products.share-earn');
        });
    }
}
