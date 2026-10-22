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

        return view('affiliate::admin.payouts.index');
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
