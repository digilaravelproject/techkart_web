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

                if ($row->payout_method === 'upi') {
                    return '<div class="text-xs font-mono bg-gray-100 dark:bg-gray-800 p-1 rounded">UPI: '.e($details['upi_id'] ?? 'N/A').'</div>';
                }

                return '<div class="text-xs bg-gray-100 dark:bg-gray-800 p-1 rounded space-y-0.5">'.
                    '<div><b>A/C:</b> '.e($details['account_number'] ?? 'N/A').'</div>'.
                    '<div><b>IFSC:</b> '.e($details['ifsc_code'] ?? 'N/A').'</div>'.
                    '<div><b>Holder:</b> '.e($details['account_holder'] ?? 'N/A').'</div>'.
                    '</div>';
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
    }
}
