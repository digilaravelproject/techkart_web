<?php

namespace Webkul\Affiliate\Repositories;

use Webkul\Affiliate\Contracts\AffiliateWallet;
use Webkul\Core\Eloquent\Repository;

class WalletRepository extends Repository
{
    /**
     * Specify model class name.
     */
    public function model(): string
    {
        return AffiliateWallet::class;
    }

    /**
     * Get or create wallet for affiliate.
     */
    public function getByAffiliateId(int $affiliateId): ?AffiliateWallet
    {
        $wallet = $this->findOneByField('affiliate_id', $affiliateId);

        if (! $wallet) {
            $wallet = $this->create([
                'affiliate_id' => $affiliateId,
                'available_balance' => 0.0000,
                'pending_balance' => 0.0000,
                'locked_balance' => 0.0000,
                'total_earned' => 0.0000,
                'total_withdrawn' => 0.0000,
            ]);
        }

        return $wallet;
    }
}
