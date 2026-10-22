<?php

namespace Webkul\Affiliate\Listeners;

use Webkul\Affiliate\Repositories\AffiliateRepository;
use Webkul\Customer\Contracts\Customer;

class CustomerRegistrationListener
{
    /**
     * Create a new listener instance.
     */
    public function __construct(
        protected AffiliateRepository $affiliateRepository
    ) {}

    /**
     * Handle customer registration event.
     */
    public function handle(Customer $customer): void
    {
        $this->affiliateRepository->getOrCreateForCustomer($customer->id);
    }
}
