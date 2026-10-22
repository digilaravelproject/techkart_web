<?php

namespace Webkul\Affiliate\Repositories;

use Illuminate\Support\Str;
use Webkul\Affiliate\Contracts\AffiliateWalletTransaction;
use Webkul\Core\Eloquent\Repository;

class WalletTransactionRepository extends Repository
{
    /**
     * Specify model class name.
     */
    public function model(): string
    {
        return AffiliateWalletTransaction::class;
    }

    /**
     * Generate unique transaction number.
     */
    public function generateTransactionNumber(): string
    {
        do {
            $number = 'TXN-'.date('Ymd').'-'.strtoupper(Str::random(6));
        } while ($this->findOneByField('transaction_number', $number));

        return $number;
    }
}
