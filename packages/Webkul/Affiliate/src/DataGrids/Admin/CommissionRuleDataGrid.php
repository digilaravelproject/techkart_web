<?php

namespace Webkul\Affiliate\DataGrids\Admin;

use Illuminate\Support\Facades\DB;
use Webkul\DataGrid\DataGrid;

class CommissionRuleDataGrid extends DataGrid
{
    /**
     * Prepare query builder.
     */
    public function prepareQueryBuilder()
    {
        $tablePrefix = DB::getTablePrefix();

        $queryBuilder = DB::table('affiliate_commission_rules as acr')
            ->leftJoin('category_translations as ct', function ($join) {
                $join->on('acr.entity_id', '=', 'ct.category_id')
                    ->where('acr.entity_type', '=', 'category')
                    ->where('ct.locale', '=', app()->getLocale());
            })
            ->leftJoin('product_flat as pf', function ($join) {
                $join->on('acr.entity_id', '=', 'pf.product_id')
                    ->where('acr.entity_type', '=', 'product')
                    ->where('pf.locale', '=', app()->getLocale());
            })
            ->select(
                'acr.id',
                'acr.entity_type',
                'acr.entity_id',
                'acr.commission_type',
                'acr.commission_value',
                'acr.is_excluded',
                'acr.status',
                'acr.created_at',
                DB::raw("CASE WHEN {$tablePrefix}acr.entity_type = 'category' THEN COALESCE({$tablePrefix}ct.name, CONCAT('Category #', {$tablePrefix}acr.entity_id)) ELSE COALESCE({$tablePrefix}pf.name, CONCAT('Product #', {$tablePrefix}acr.entity_id)) END as entity_name")
            );

        $this->addFilter('id', 'acr.id');
        $this->addFilter('entity_type', 'acr.entity_type');
        $this->addFilter('commission_type', 'acr.commission_type');
        $this->addFilter('status', 'acr.status');

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
            'index' => 'entity_type',
            'label' => trans('affiliate::app.admin.rules.entity-type'),
            'type' => 'string',
            'searchable' => true,
            'sortable' => true,
            'filterable' => true,
            'closure' => function ($row) {
                return '<span class="badge badge-md '.($row->entity_type === 'category' ? 'badge-info' : 'badge-warning').'">'.ucfirst($row->entity_type).'</span>';
            },
        ]);

        $this->addColumn([
            'index' => 'entity_name',
            'label' => trans('affiliate::app.admin.rules.entity-name'),
            'type' => 'string',
            'searchable' => true,
            'sortable' => false,
            'filterable' => false,
        ]);

        $this->addColumn([
            'index' => 'commission_type',
            'label' => trans('affiliate::app.admin.rules.commission-type'),
            'type' => 'string',
            'searchable' => true,
            'sortable' => true,
            'filterable' => true,
            'closure' => function ($row) {
                return ucfirst($row->commission_type);
            },
        ]);

        $this->addColumn([
            'index' => 'commission_value',
            'label' => trans('affiliate::app.admin.rules.commission-value'),
            'type' => 'string',
            'searchable' => false,
            'sortable' => true,
            'filterable' => false,
            'closure' => function ($row) {
                if ($row->is_excluded) {
                    return '<span class="text-red-500 font-bold">0% (Excluded)</span>';
                }

                if (strtolower($row->commission_type) === 'percentage') {
                    return '<span class="font-bold text-emerald-600">'.((float) $row->commission_value).'%</span>';
                }

                return '<span class="font-bold text-emerald-600">₹'.number_format((float) $row->commission_value, 2).'</span>';
            },
        ]);

        $this->addColumn([
            'index' => 'status',
            'label' => trans('affiliate::app.admin.rules.status'),
            'type' => 'boolean',
            'searchable' => true,
            'sortable' => true,
            'filterable' => true,
            'closure' => function ($row) {
                return $row->status
                    ? '<span class="badge badge-md badge-success">Active</span>'
                    : '<span class="badge badge-md badge-danger">Inactive</span>';
            },
        ]);
    }

    /**
     * Prepare actions.
     */
    public function prepareActions()
    {
        $this->addAction([
            'icon' => 'icon-delete',
            'title' => trans('admin::app.datagrid.delete'),
            'method' => 'DELETE',
            'url' => function ($row) {
                return route('admin.affiliate.rules.delete', $row->id);
            },
        ]);
    }
}
