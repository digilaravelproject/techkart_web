<?php

namespace Webkul\Affiliate\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Webkul\Affiliate\Contracts\Affiliate;
use Webkul\Affiliate\Contracts\AffiliatePayout;
use Webkul\Affiliate\Contracts\AffiliateWallet;
use Webkul\Affiliate\Repositories\CommissionRepository;
use Webkul\Affiliate\Repositories\PayoutRepository;
use Webkul\Affiliate\Repositories\WalletRepository;
use Webkul\Affiliate\Repositories\WalletTransactionRepository;
use Webkul\Sales\Contracts\Order;

class WalletTransactionService
{
    /**
     * Create a new service instance.
     */
    public function __construct(
        protected WalletRepository $walletRepository,
        protected WalletTransactionRepository $transactionRepository,
        protected CommissionRepository $commissionRepository,
        protected PayoutRepository $payoutRepository
    ) {}

    /**
     * Credit pending commissions on order placement.
     */
    public function recordPendingCommission(Affiliate $affiliate, Order $order, float $amount): void
    {
        if ($amount <= 0) {
            return;
        }

        DB::transaction(function () use ($affiliate, $order, $amount) {
            /** @var AffiliateWallet $wallet */
            $wallet = $this->walletRepository->getByAffiliateId($affiliate->id);

            $wallet->pending_balance += $amount;
            $wallet->save();

            $this->transactionRepository->create([
                'affiliate_id' => $affiliate->id,
                'transaction_number' => $this->transactionRepository->generateTransactionNumber(),
                'order_id' => $order->id,
                'type' => 'credit',
                'amount' => $amount,
                'balance_after' => $wallet->available_balance,
                'status' => 'pending',
                'title' => "Referral commission for Order #{$order->increment_id}",
                'description' => "₹{$amount} pending clearance until return period closes.",
            ]);
        });
    }

    /**
     * Release all matured commissions whose clearance window has passed.
     */
    public function releaseMaturedCommissions(): int
    {
        $matured = $this->commissionRepository->findWhere([
            'status' => 'pending',
        ])->filter(function ($commission) {
            return $commission->clears_at && Carbon::now()->greaterThanOrEqualTo($commission->clears_at);
        });

        $count = 0;

        foreach ($matured as $commission) {
            DB::transaction(function () use ($commission) {
                /** @var AffiliateWallet $wallet */
                $wallet = $this->walletRepository->getByAffiliateId($commission->affiliate_id);

                $amount = (float) $commission->commission_amount;

                $wallet->pending_balance = max(0, $wallet->pending_balance - $amount);
                $wallet->available_balance += $amount;
                $wallet->total_earned += $amount;
                $wallet->save();

                $commission->status = 'approved';
                $commission->save();

                $orderId = $commission->order?->increment_id ?? $commission->order_id;

                $this->transactionRepository->create([
                    'affiliate_id' => $commission->affiliate_id,
                    'transaction_number' => $this->transactionRepository->generateTransactionNumber(),
                    'order_id' => $commission->order_id,
                    'type' => 'credit',
                    'amount' => $amount,
                    'balance_after' => $wallet->available_balance,
                    'status' => 'cleared',
                    'title' => "Commission cleared for Order #{$orderId}",
                    'description' => "₹{$amount} moved to available balance.",
                ]);
            });

            $count++;
        }

        return $count;
    }

    /**
     * Reverse pending commission if order is cancelled.
     */
    public function reverseOrderCommission(Order $order): void
    {
        $commissions = $this->commissionRepository->findWhere([
            'order_id' => $order->id,
            'status' => 'pending',
        ]);

        if ($commissions->isEmpty()) {
            return;
        }

        DB::transaction(function () use ($commissions, $order) {
            foreach ($commissions as $commission) {
                /** @var AffiliateWallet $wallet */
                $wallet = $this->walletRepository->getByAffiliateId($commission->affiliate_id);

                $amount = (float) $commission->commission_amount;
                $wallet->pending_balance = max(0, $wallet->pending_balance - $amount);
                $wallet->save();

                $commission->status = 'cancelled';
                $commission->save();

                $this->transactionRepository->create([
                    'affiliate_id' => $commission->affiliate_id,
                    'transaction_number' => $this->transactionRepository->generateTransactionNumber(),
                    'order_id' => $order->id,
                    'type' => 'debit',
                    'amount' => $amount,
                    'balance_after' => $wallet->available_balance,
                    'status' => 'reversed',
                    'title' => "Reversal for cancelled Order #{$order->increment_id}",
                    'description' => "Pending commission ₹{$amount} cancelled.",
                ]);
            }
        });
    }

    /**
     * Request payout withdrawal.
     */
    public function requestPayout(Affiliate $affiliate, float $amount, string $method, array $details): AffiliatePayout
    {
        return DB::transaction(function () use ($affiliate, $amount, $method, $details) {
            /** @var AffiliateWallet $wallet */
            $wallet = $this->walletRepository->getByAffiliateId($affiliate->id);

            if ($wallet->available_balance < $amount) {
                throw new \InvalidArgumentException('Insufficient available wallet balance.');
            }

            $wallet->available_balance -= $amount;
            $wallet->locked_balance += $amount;
            $wallet->save();

            $payout = $this->payoutRepository->create([
                'affiliate_id' => $affiliate->id,
                'payout_number' => $this->payoutRepository->generatePayoutNumber(),
                'amount' => $amount,
                'payout_method' => $method,
                'payout_details' => $details,
                'status' => 'requested',
            ]);

            $this->transactionRepository->create([
                'affiliate_id' => $affiliate->id,
                'transaction_number' => $this->transactionRepository->generateTransactionNumber(),
                'payout_id' => $payout->id,
                'type' => 'debit',
                'amount' => $amount,
                'balance_after' => $wallet->available_balance,
                'status' => 'locked',
                'title' => "Withdrawal Request {$payout->payout_number}",
                'description' => "₹{$amount} locked pending admin processing via {$method}.",
            ]);

            return $payout;
        });
    }

    /**
     * Complete and approve payout withdrawal with bank UTR.
     */
    public function completePayout(AffiliatePayout $payout, string $reference, ?string $notes = null, ?int $adminId = null): void
    {
        DB::transaction(function () use ($payout, $reference, $notes, $adminId) {
            /** @var AffiliateWallet $wallet */
            $wallet = $this->walletRepository->getByAffiliateId($payout->affiliate_id);

            $amount = (float) $payout->amount;
            $wallet->locked_balance = max(0, $wallet->locked_balance - $amount);
            $wallet->total_withdrawn += $amount;
            $wallet->save();

            $payout->update([
                'status' => 'completed',
                'transaction_reference' => $reference,
                'admin_notes' => $notes,
                'processed_by' => $adminId,
                'processed_at' => Carbon::now(),
            ]);

            $this->transactionRepository->create([
                'affiliate_id' => $payout->affiliate_id,
                'transaction_number' => $this->transactionRepository->generateTransactionNumber(),
                'payout_id' => $payout->id,
                'type' => 'debit',
                'amount' => $amount,
                'balance_after' => $wallet->available_balance,
                'status' => 'completed',
                'title' => "Payout Paid {$payout->payout_number}",
                'description' => "Ref/UTR: {$reference}. Funds transferred successfully.",
            ]);
        });
    }

    /**
     * Reject payout withdrawal and refund money back to available balance.
     */
    public function rejectPayout(AffiliatePayout $payout, string $reason, ?int $adminId = null): void
    {
        DB::transaction(function () use ($payout, $reason, $adminId) {
            /** @var AffiliateWallet $wallet */
            $wallet = $this->walletRepository->getByAffiliateId($payout->affiliate_id);

            $amount = (float) $payout->amount;
            $wallet->locked_balance = max(0, $wallet->locked_balance - $amount);
            $wallet->available_balance += $amount;
            $wallet->save();

            $payout->update([
                'status' => 'rejected',
                'admin_notes' => $reason,
                'processed_by' => $adminId,
                'processed_at' => Carbon::now(),
            ]);

            $this->transactionRepository->create([
                'affiliate_id' => $payout->affiliate_id,
                'transaction_number' => $this->transactionRepository->generateTransactionNumber(),
                'payout_id' => $payout->id,
                'type' => 'credit',
                'amount' => $amount,
                'balance_after' => $wallet->available_balance,
                'status' => 'reversed',
                'title' => "Payout Rejected {$payout->payout_number}",
                'description' => "Funds returned to available balance. Reason: {$reason}",
            ]);
        });
    }
}
