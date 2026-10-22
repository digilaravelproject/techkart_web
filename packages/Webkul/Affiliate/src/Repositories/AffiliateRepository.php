<?php

namespace Webkul\Affiliate\Repositories;

use Illuminate\Support\Str;
use Webkul\Affiliate\Contracts\Affiliate;
use Webkul\Core\Eloquent\Repository;

class AffiliateRepository extends Repository
{
    /**
     * Specify model class name.
     */
    public function model(): string
    {
        return Affiliate::class;
    }

    /**
     * Find an active affiliate by customer ID or create a new one.
     */
    public function getOrCreateForCustomer(int $customerId): Affiliate
    {
        $affiliate = $this->findOneByField('customer_id', $customerId);

        if ($affiliate) {
            return $affiliate;
        }

        $code = $this->generateUniqueReferralCode();

        $affiliate = $this->create([
            'customer_id' => $customerId,
            'referral_code' => $code,
            'status' => 'active',
            'total_clicks' => 0,
            'total_orders' => 0,
        ]);

        app(WalletRepository::class)->create([
            'affiliate_id' => $affiliate->id,
            'available_balance' => 0.0000,
            'pending_balance' => 0.0000,
            'locked_balance' => 0.0000,
            'total_earned' => 0.0000,
            'total_withdrawn' => 0.0000,
        ]);

        return $affiliate;
    }

    /**
     * Generate a unique referral code.
     */
    public function generateUniqueReferralCode(): string
    {
        do {
            $code = 'BNZ'.strtoupper(Str::random(6));
        } while ($this->findOneByField('referral_code', $code));

        return $code;
    }

    /**
     * Increment total clicks for an affiliate.
     */
    public function incrementClicks(int $affiliateId): void
    {
        $this->model->where('id', $affiliateId)->increment('total_clicks');
    }

    /**
     * Increment total orders for an affiliate.
     */
    public function incrementOrders(int $affiliateId): void
    {
        $this->model->where('id', $affiliateId)->increment('total_orders');
    }
}
