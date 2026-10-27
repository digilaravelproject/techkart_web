<?php

namespace Webkul\Affiliate\DataGrids\Admin;

use Illuminate\Support\Facades\DB;
use Webkul\DataGrid\DataGrid;

class PayoutDataGrid extends DataGrid
{
    /**
     * Prepare query builder.
     */
    public function prepareQueryBuilder()
    {
        $tablePrefix = DB::getTablePrefix();

        $queryBuilder = DB::table('affiliate_payouts as ap')
            ->leftJoin('affiliates', 'ap.affiliate_id', '=', 'affiliates.id')
            ->leftJoin('customers', 'affiliates.customer_id', '=', 'customers.id')
            ->select(
                'ap.id',
                'ap.payout_number',
                'ap.amount',
                'ap.payout_method',
                'ap.payout_details',
                'ap.status',
                'ap.transaction_reference',
                'ap.created_at',
                'affiliates.payout_details as affiliate_payout_details',
                'affiliates.payout_method as affiliate_payout_method',
                DB::raw("CONCAT({$tablePrefix}customers.first_name, ' ', {$tablePrefix}customers.last_name) as customer_name"),
                'customers.email as customer_email'
            );

        $this->addFilter('id', 'ap.id');
        $this->addFilter('payout_number', 'ap.payout_number');
        $this->addFilter('status', 'ap.status');
        $this->addFilter('created_at', 'ap.created_at');

        return $queryBuilder;
    }

    /**
     * Add columns.
     */
    public function prepareColumns()
    {
        $this->addColumn([
            'index' => 'id',
            'label' => 'ID',
            'type' => 'integer',
            'searchable' => true,
            'sortable' => true,
            'filterable' => true,
            'closure' => function ($row) {
                return '<span class="font-bold text-gray-700 dark:text-gray-300">#'.$row->id.'</span>';
            },
        ]);

        $this->addColumn([
            'index' => 'payout_number',
            'label' => trans('affiliate::app.admin.payouts.payout-number'),
            'type' => 'string',
            'searchable' => true,
            'sortable' => true,
            'filterable' => true,
            'closure' => function ($row) {
                return '<span class="font-mono font-bold text-gray-800 dark:text-white">'.$row->payout_number.'</span>';
            },
        ]);

        $this->addColumn([
            'index' => 'customer_name',
            'label' => trans('affiliate::app.admin.payouts.customer'),
            'type' => 'string',
            'searchable' => true,
            'sortable' => true,
            'filterable' => true,
            'closure' => function ($row) {
                return '<div><p class="font-medium">'.e($row->customer_name).'</p><p class="text-xs text-gray-500">'.e($row->customer_email).'</p></div>';
            },
        ]);

        $this->addColumn([
            'index' => 'amount',
            'label' => trans('affiliate::app.admin.payouts.amount'),
            'type' => 'string',
            'searchable' => false,
            'sortable' => true,
            'filterable' => false,
            'closure' => function ($row) {
                return '<span class="font-bold text-emerald-600 dark:text-emerald-400">₹'.number_format((float) $row->amount, 2).'</span>';
            },
        ]);

        $this->addColumn([
            'index' => 'payout_method',
            'label' => trans('affiliate::app.admin.payouts.method'),
            'type' => 'string',
            'searchable' => true,
            'sortable' => true,
            'filterable' => true,
            'closure' => function ($row) {
                return '<span class="badge badge-md '.($row->payout_method === 'upi' ? 'badge-primary' : 'badge-info').'">'.strtoupper(str_replace('_', ' ', $row->payout_method)).'</span>';
            },
        ]);

        $this->addColumn([
            'index' => 'payout_details',
            'label' => trans('affiliate::app.admin.payouts.details'),
            'type' => 'string',
            'searchable' => false,
            'sortable' => false,
            'filterable' => false,
            'closure' => function ($row) {
                $details = is_string($row->payout_details) ? json_decode($row->payout_details, true) : (array) $row->payout_details;

                if (empty($details) || (empty($details['upi_id']) && empty($details['upiId']) && empty($details['account_number']))) {
                    $affDetails = is_string($row->affiliate_payout_details ?? null)
                        ? json_decode($row->affiliate_payout_details, true)
                        : (array) ($row->affiliate_payout_details ?? []);

                    if (! empty($affDetails)) {
                        $details = $affDetails;
                    }
                }

                $method = $row->payout_method ?: ($row->affiliate_payout_method ?? 'upi');

                if ($method === 'upi') {
                    $upiId = $details['upi_id'] ?? ($details['upiId'] ?? ($details['upi'] ?? null));

                    if ($upiId) {
                        return '<div class="text-xs font-mono font-bold text-blue-700 dark:text-blue-300 bg-blue-50 dark:bg-blue-950/60 px-2.5 py-1.5 rounded-lg border border-blue-200 dark:border-blue-800 inline-flex items-center gap-1.5 shadow-sm">'
                            .'<span>UPI: ' . e($upiId) . '</span>'
                            .'</div>';
                    }

                    return '<span class="text-xs text-amber-600 dark:text-amber-400 font-semibold bg-amber-50 dark:bg-amber-950/50 px-2 py-1 rounded">UPI ID Pending</span>';
                }

                $acc = $details['account_number'] ?? ($details['accountNumber'] ?? 'N/A');
                $ifsc = $details['ifsc_code'] ?? ($details['ifscCode'] ?? 'N/A');
                $holder = $details['account_holder'] ?? ($details['accountHolder'] ?? 'N/A');
                $bank = $details['bank_name'] ?? ($details['bankName'] ?? null);

                return '<div class="text-xs bg-gray-100 dark:bg-gray-800 p-2 rounded-lg space-y-0.5 border border-gray-200 dark:border-gray-700">'
                    .'<div><b>A/C:</b> ' . e($acc) . '</div>'
                    .'<div><b>IFSC:</b> ' . e($ifsc) . '</div>'
                    .'<div><b>Holder:</b> ' . e($holder) . '</div>'
                    .($bank ? '<div><b>Bank:</b> ' . e($bank) . '</div>' : '')
                    .'</div>';
            },
        ]);

        $this->addColumn([
            'index' => 'status',
            'label' => trans('affiliate::app.admin.payouts.status'),
            'type' => 'string',
            'searchable' => true,
            'sortable' => true,
            'filterable' => true,
            'closure' => function ($row) {
                $badges = [
                    'requested' => 'badge-warning',
                    'processing' => 'badge-info',
                    'completed' => 'badge-success',
                    'rejected' => 'badge-danger',
                ];

                $badgeClass = $badges[$row->status] ?? 'badge-secondary';

                return '<span class="badge badge-md '.$badgeClass.'">'.ucfirst($row->status).'</span>';
            },
        ]);

        $this->addColumn([
            'index' => 'created_at',
            'label' => trans('affiliate::app.admin.payouts.date'),
            'type' => 'date',
            'searchable' => true,
            'sortable' => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index' => 'operations',
            'label' => trans('affiliate::app.admin.payouts.actions'),
            'type' => 'string',
            'searchable' => false,
            'sortable' => false,
            'filterable' => false,
            'closure' => function ($row) {
                if ($row->status === 'requested' || $row->status === 'processing') {
                    return '<button type="button" class="cursor-pointer inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 shadow transition hover:scale-105" onclick="openPayoutModal('.$row->id.')">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        Process / Approve
                    </button>';
                }

                if ($row->status === 'completed') {
                    return '<div class="text-xs text-emerald-600 dark:text-emerald-400 font-bold">✓ Approved & Paid'.($row->transaction_reference ? '<span class="block text-gray-500 font-mono text-[11px] font-normal">UTR: '.e($row->transaction_reference).'</span>' : '').'</div>';
                }

                if ($row->status === 'rejected') {
                    return '<span class="text-xs text-red-500 font-bold">✕ Rejected</span>';
                }

                return '<span class="text-xs text-gray-400">—</span>';
            },
        ]);
    }

    /**
     * Prepare actions.
     */
    public function prepareActions()
    {
        $this->addAction([
            'icon' => 'icon-edit text-2xl',
            'title' => 'Process / Approve Payout',
            'method' => 'GET',
            'url' => function ($row) {
                return 'javascript:openPayoutModal('.$row->id.');';
            },
        ]);
    }
}
