<?php

namespace Webkul\Affiliate\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Webkul\Affiliate\DataGrids\Admin\PayoutDataGrid;
use Webkul\Affiliate\Repositories\PayoutRepository;
use Webkul\Affiliate\Services\WalletTransactionService;

class PayoutController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        protected PayoutRepository $payoutRepository,
        protected WalletTransactionService $walletTransactionService
    ) {}

    /**
     * Display a listing of payout requests.
     */
    public function index()
    {
        if (request()->ajax()) {
            return datagrid(PayoutDataGrid::class)->process();
        }

        $pendingPayouts = $this->payoutRepository
            ->with(['affiliate.customer'])
            ->findWhereIn('status', ['requested', 'processing']);

        $pendingPayoutsPayload = $pendingPayouts->map(function ($payout) {
            $details = is_string($payout->payout_details) ? json_decode($payout->payout_details, true) : (array) $payout->payout_details;

            if (empty($details) || (empty($details['upi_id']) && empty($details['upiId']) && empty($details['account_number']))) {
                $affiliateDetails = is_string($payout->affiliate?->payout_details)
                    ? json_decode($payout->affiliate->payout_details, true)
                    : (array) ($payout->affiliate?->payout_details ?? []);

                if (! empty($affiliateDetails)) {
                    $details = $affiliateDetails;
                }
            }

            $customer = $payout->affiliate?->customer;

            return [
                'id' => $payout->id,
                'payout_number' => $payout->payout_number,
                'amount' => (float) $payout->amount,
                'amount_formatted' => '₹'.number_format((float) $payout->amount, 2),
                'method' => $payout->payout_method ?: ($payout->affiliate?->payout_method ?? 'upi'),
                'details' => [
                    'upi_id' => $details['upi_id'] ?? ($details['upiId'] ?? ($details['upi'] ?? null)),
                    'account_number' => $details['account_number'] ?? ($details['accountNumber'] ?? null),
                    'ifsc_code' => $details['ifsc_code'] ?? ($details['ifscCode'] ?? null),
                    'account_holder' => $details['account_holder'] ?? ($details['accountHolder'] ?? null),
                    'bank_name' => $details['bank_name'] ?? ($details['bankName'] ?? null),
                ],
                'customer_name' => trim(($customer?->first_name ?? '').' '.($customer?->last_name ?? '')) ?: 'N/A',
                'customer_email' => $customer?->email ?? 'N/A',
                'status' => $payout->status,
                'created_at' => $payout->created_at?->format('d M Y, h:i A'),
            ];
        })->values();

        return view('affiliate::admin.payouts.index', compact('pendingPayouts', 'pendingPayoutsPayload'));
    }

    /**
     * Complete and approve payout.
     */
    public function complete(int $id)
    {
        request()->validate([
            'transaction_reference' => 'required|string|max:100',
            'admin_notes' => 'nullable|string',
        ]);

        $payout = $this->payoutRepository->findOrFail($id);

        if ($payout->status === 'completed') {
            session()->flash('warning', 'Payout is already completed.');

            return redirect()->back();
        }

        $adminId = Auth::guard('admin')->id();

        $this->walletTransactionService->completePayout(
            $payout,
            request('transaction_reference'),
            request('admin_notes'),
            $adminId
        );

        session()->flash('success', trans('affiliate::app.admin.payouts.paid-success'));

        return redirect()->route('admin.affiliate.payouts.index');
    }

    /**
     * Reject payout request and refund balance.
     */
    public function reject(int $id)
    {
        request()->validate([
            'reason' => 'required|string|max:500',
        ]);

        $payout = $this->payoutRepository->findOrFail($id);

        if ($payout->status !== 'requested' && $payout->status !== 'processing') {
            session()->flash('warning', 'Only active requested payouts can be rejected.');

            return redirect()->back();
        }

        $adminId = Auth::guard('admin')->id();

        $this->walletTransactionService->rejectPayout(
            $payout,
            request('reason'),
            $adminId
        );

        session()->flash('success', trans('affiliate::app.admin.payouts.reject-success'));

        return redirect()->route('admin.affiliate.payouts.index');
    }
}
