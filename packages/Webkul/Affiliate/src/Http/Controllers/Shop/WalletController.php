<?php

namespace Webkul\Affiliate\Http\Controllers\Shop;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Webkul\Affiliate\Repositories\AffiliateRepository;
use Webkul\Affiliate\Repositories\WalletRepository;
use Webkul\Affiliate\Services\WalletTransactionService;

class WalletController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        protected AffiliateRepository $affiliateRepository,
        protected WalletRepository $walletRepository,
        protected WalletTransactionService $walletTransactionService
    ) {}

    /**
     * Submit payout withdrawal request.
     */
    public function requestPayout()
    {
        $customer = Auth::guard('customer')->user();

        $affiliate = $this->affiliateRepository->getOrCreateForCustomer($customer->id);

        $wallet = $this->walletRepository->getByAffiliateId($affiliate->id);

        $minAmount = (float) (core()->getConfigData('affiliate.general.settings.min_withdrawal_amount') ?? core()->getConfigData('affiliate.general.min_withdrawal_amount') ?? 500);

        request()->validate([
            'amount' => 'required|numeric|min:'.$minAmount.'|max:'.$wallet->available_balance,
            'payout_method' => 'required|in:upi,bank_transfer',
            'upi_id' => 'required_if:payout_method,upi|nullable|string|max:100',
            'account_holder' => 'required_if:payout_method,bank_transfer|nullable|string|max:150',
            'account_number' => 'required_if:payout_method,bank_transfer|nullable|string|max:50',
            'bank_name' => 'required_if:payout_method,bank_transfer|nullable|string|max:100',
            'ifsc_code' => 'required_if:payout_method,bank_transfer|nullable|string|max:20',
        ]);

        $method = request('payout_method');
        $amount = (float) request('amount');

        $details = $method === 'upi' ? [
            'upi_id' => request('upi_id'),
        ] : [
            'account_holder' => request('account_holder'),
            'account_number' => request('account_number'),
            'bank_name' => request('bank_name'),
            'ifsc_code' => request('ifsc_code'),
        ];

        try {
            $affiliate->payout_method = $method;
            $affiliate->payout_details = $details;
            $affiliate->save();

            $this->walletTransactionService->requestPayout($affiliate, $amount, $method, $details);

            session()->flash('success', trans('affiliate::app.shop.account.request-success'));
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }

        return redirect()->route('shop.customers.account.affiliate.index');
    }

    /**
     * Update customer payout settings from web.
     */
    public function updatePayoutSettings()
    {
        $customer = Auth::guard('customer')->user();

        $affiliate = $this->affiliateRepository->getOrCreateForCustomer($customer->id);

        request()->validate([
            'payout_method' => 'required|in:upi,bank_transfer',
            'upi_id' => 'required_if:payout_method,upi|nullable|string|max:100',
            'account_holder' => 'required_if:payout_method,bank_transfer|nullable|string|max:150',
            'account_number' => 'required_if:payout_method,bank_transfer|nullable|string|max:50',
            'bank_name' => 'required_if:payout_method,bank_transfer|nullable|string|max:100',
            'ifsc_code' => 'required_if:payout_method,bank_transfer|nullable|string|max:20',
        ]);

        $method = request('payout_method');

        $details = $method === 'upi' ? [
            'upi_id' => request('upi_id'),
        ] : [
            'account_holder' => request('account_holder'),
            'account_number' => request('account_number'),
            'bank_name' => request('bank_name'),
            'ifsc_code' => request('ifsc_code'),
        ];

        $affiliate->payout_method = $method;
        $affiliate->payout_details = $details;
        $affiliate->save();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Payout details saved successfully.',
                'payout_method' => $method,
                'payout_details' => $details,
            ]);
        }

        session()->flash('success', 'Payout settings saved successfully.');

        return redirect()->route('shop.customers.account.affiliate.index');
    }
}
