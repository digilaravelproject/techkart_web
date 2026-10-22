<?php

namespace Webkul\Affiliate\DataGrids\Admin;

use Illuminate\Support\Facades\DB;
use Webkul\DataGrid\DataGrid;

class AffiliateDataGrid extends DataGrid
{
    /**
     * Prepare query builder.
     */
    public function prepareQueryBuilder()
    {
        $tablePrefix = DB::getTablePrefix();

        $queryBuilder = DB::table('affiliates')
            ->leftJoin('customers', 'affiliates.customer_id', '=', 'customers.id')
            ->leftJoin('affiliate_wallets', 'affiliates.id', '=', 'affiliate_wallets.affiliate_id')
            ->select(
                'affiliates.id',
                DB::raw("CONCAT({$tablePrefix}customers.first_name, ' ', {$tablePrefix}customers.last_name) as customer_name"),
                'customers.email as customer_email',
                'affiliates.referral_code',
                'affiliates.total_clicks',
                'affiliates.total_orders',
                'affiliates.status',
                'affiliates.created_at',
                DB::raw("COALESCE({$tablePrefix}affiliate_wallets.available_balance, 0) as available_balance"),
                DB::raw("COALESCE({$tablePrefix}affiliate_wallets.pending_balance, 0) as pending_balance"),
                DB::raw("COALESCE({$tablePrefix}affiliate_wallets.total_earned, 0) as total_earned")
            );

        $this->addFilter('id', 'affiliates.id');
        $this->addFilter('customer_name', DB::raw("CONCAT({$tablePrefix}customers.first_name, ' ', {$tablePrefix}customers.last_name)"));
        $this->addFilter('customer_email', 'customers.email');
        $this->addFilter('referral_code', 'affiliates.referral_code');
        $this->addFilter('status', 'affiliates.status');
        $this->addFilter('created_at', 'affiliates.created_at');

        return $queryBuilder;
    }

    /**
     * Add columns.
     */
    public function prepareColumns()
    {
        $this->addColumn([
            'index' => 'id',
            'label' => trans('affiliate::app.admin.promoters.id'),
            'type' => 'integer',
            'searchable' => true,
            'sortable' => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index' => 'customer_name',
            'label' => trans('affiliate::app.admin.promoters.customer'),
            'type' => 'string',
            'searchable' => true,
            'sortable' => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index' => 'customer_email',
            'label' => trans('affiliate::app.admin.promoters.email'),
            'type' => 'string',
            'searchable' => true,
            'sortable' => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index' => 'referral_code',
            'label' => trans('affiliate::app.admin.promoters.referral-code'),
            'type' => 'string',
            'searchable' => true,
            'sortable' => true,
            'filterable' => true,
            'closure' => function ($row) {
                return '<span class="badge badge-md badge-info font-mono font-bold">'.$row->referral_code.'</span>';
            },
        ]);

        $this->addColumn([
            'index' => 'available_balance',
            'label' => trans('affiliate::app.admin.promoters.available-bal'),
            'type' => 'string',
            'searchable' => false,
            'sortable' => true,
            'filterable' => false,
            'closure' => function ($row) {
                return '<span class="font-bold text-emerald-600 dark:text-emerald-400">₹'.number_format((float) $row->available_balance, 2).'</span>';
            },
        ]);

        $this->addColumn([
            'index' => 'pending_balance',
            'label' => trans('affiliate::app.admin.promoters.pending-bal'),
            'type' => 'string',
            'searchable' => false,
            'sortable' => true,
            'filterable' => false,
            'closure' => function ($row) {
                return '<span class="text-amber-600 dark:text-amber-400">₹'.number_format((float) $row->pending_balance, 2).'</span>';
            },
        ]);

        $this->addColumn([
            'index' => 'total_earned',
            'label' => trans('affiliate::app.admin.promoters.total-earned'),
            'type' => 'string',
            'searchable' => false,
            'sortable' => true,
            'filterable' => false,
            'closure' => function ($row) {
                return '₹'.number_format((float) $row->total_earned, 2);
            },
        ]);

        $this->addColumn([
            'index' => 'total_clicks',
            'label' => trans('affiliate::app.admin.promoters.total-clicks'),
            'type' => 'integer',
            'searchable' => false,
            'sortable' => true,
            'filterable' => false,
        ]);

        $this->addColumn([
            'index' => 'total_orders',
            'label' => trans('affiliate::app.admin.promoters.total-orders'),
            'type' => 'integer',
            'searchable' => false,
            'sortable' => true,
            'filterable' => false,
        ]);

        $this->addColumn([
            'index' => 'status',
            'label' => trans('affiliate::app.admin.promoters.status'),
            'type' => 'string',
            'searchable' => true,
            'sortable' => true,
            'filterable' => true,
            'closure' => function ($row) {
                if ($row->status === 'active') {
                    return '<span class="badge badge-md badge-success">Active</span>';
                }

                return '<span class="badge badge-md badge-danger">Suspended</span>';
            },
        ]);

        $this->addColumn([
            'index' => 'created_at',
            'label' => trans('affiliate::app.admin.promoters.created-at'),
            'type' => 'date',
            'searchable' => true,
            'sortable' => true,
            'filterable' => true,
        ]);
    }
}
