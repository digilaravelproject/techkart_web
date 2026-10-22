<?php

namespace Webkul\Affiliate\Repositories;

use Webkul\Affiliate\Contracts\AffiliateCommission;
use Webkul\Core\Eloquent\Repository;

class CommissionRepository extends Repository
{
    /**
     * Specify model class name.
     */
    public function model(): string
    {
        return AffiliateCommission::class;
    }
}
