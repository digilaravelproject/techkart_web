<?php

namespace Webkul\Affiliate\Providers;

use Konekt\Concord\BaseModuleServiceProvider;
use Webkul\Affiliate\Models\Affiliate;
use Webkul\Affiliate\Models\AffiliateCommission;
use Webkul\Affiliate\Models\AffiliateCommissionRule;
use Webkul\Affiliate\Models\AffiliatePayout;
use Webkul\Affiliate\Models\AffiliateWallet;
use Webkul\Affiliate\Models\AffiliateWalletTransaction;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    /**
     * Define the models provided by this module.
     *
     * @var array
     */
    protected $models = [
        Affiliate::class,
        AffiliateWallet::class,
        AffiliateWalletTransaction::class,
        AffiliateCommissionRule::class,
        AffiliateCommission::class,
        AffiliatePayout::class,
    ];
}
