<?php

namespace Webkul\Affiliate\Http\Controllers\Shop;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Webkul\Affiliate\Repositories\AffiliateRepository;
use Webkul\Affiliate\Repositories\WalletRepository;
use Webkul\Affiliate\Repositories\WalletTransactionRepository;

class AffiliateController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        protected AffiliateRepository $affiliateRepository,
        protected WalletRepository $walletRepository,
        protected WalletTransactionRepository $transactionRepository
    ) {}

    /**
     * Display customer affiliate dashboard and wallet passbook.
     */
    public function index()
    {
        $customer = Auth::guard('customer')->user();

        $affiliate = $this->affiliateRepository->getOrCreateForCustomer($customer->id);

        $wallet = $this->walletRepository->getByAffiliateId($affiliate->id);

        $transactions = $this->transactionRepository
            ->scopeQuery(function ($query) use ($affiliate) {
                return $query->where('affiliate_id', $affiliate->id)
                    ->orderBy('id', 'desc');
            })
            ->paginate(15);

        $minWithdrawal = (float) (core()->getConfigData('affiliate.general.min_withdrawal_amount') ?? 500);

        $conversionRate = $affiliate->total_clicks > 0
            ? round(($affiliate->total_orders / $affiliate->total_clicks) * 100, 1)
            : 0.0;

        $referralLink = url('/').'?ref='.$affiliate->referral_code;

        return view('affiliate::shop.account.index', compact(
            'customer',
            'affiliate',
            'wallet',
            'transactions',
            'minWithdrawal',
            'conversionRate',
            'referralLink'
        ));
    }
}
