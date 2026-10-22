<?php

namespace Webkul\Affiliate\Repositories;

use Illuminate\Support\Str;
use Webkul\Affiliate\Contracts\AffiliatePayout;
use Webkul\Core\Eloquent\Repository;

class PayoutRepository extends Repository
{
    /**
     * Specify model class name.
     */
    public function model(): string
    {
        return AffiliatePayout::class;
    }

    /**
     * Generate unique payout number.
     */
    public function generatePayoutNumber(): string
    {
        do {
            $number = 'PO-'.date('Ymd').'-'.strtoupper(Str::random(6));
        } while ($this->findOneByField('payout_number', $number));

        return $number;
    }
}
