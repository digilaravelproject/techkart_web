<?php

namespace Webkul\Affiliate\Listeners;

use Webkul\Affiliate\Repositories\AffiliateRepository;
use Webkul\Affiliate\Services\CommissionCalculatorService;
use Webkul\Affiliate\Services\WalletTransactionService;
use Webkul\Sales\Contracts\Order;
use Webkul\Sales\Contracts\Refund;

class OrderPlacementListener
{
    /**
     * Create a new listener instance.
     */
    public function __construct(
        protected AffiliateRepository $affiliateRepository,
        protected CommissionCalculatorService $calculatorService,
        protected WalletTransactionService $walletTransactionService
    ) {}

    /**
     * Handle order placement event.
     */
    public function handle(Order $order): void
    {
        $refCode = request()->cookie('beenzo_affiliate_ref') ?? session('beenzo_affiliate_ref');

        if (empty($refCode)) {
            return;
        }

        $affiliate = $this->affiliateRepository->findOneWhere([
            'referral_code' => $refCode,
            'status' => 'active',
        ]);

        if (! $affiliate) {
            return;
        }

        $commissions = $this->calculatorService->calculateForOrder($order, $affiliate);

        if (empty($commissions)) {
            return;
        }

        $totalCommission = 0;

        foreach ($commissions as $comm) {
            $totalCommission += (float) $comm->commission_amount;
        }

        if ($totalCommission > 0) {
            $this->walletTransactionService->recordPendingCommission($affiliate, $order, $totalCommission);
            $this->affiliateRepository->incrementOrders($affiliate->id);
        }
    }

    /**
     * Handle order cancellation event.
     */
    public function handleCancel(Order $order): void
    {
        $this->walletTransactionService->reverseOrderCommission($order);
    }

    /**
     * Handle order refund event.
     */
    public function handleRefund(Refund $refund): void
    {
        if ($refund->order) {
            $this->walletTransactionService->reverseOrderCommission($refund->order);
        }
    }
}
