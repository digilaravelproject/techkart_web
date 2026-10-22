<?php

namespace Webkul\Affiliate\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Webkul\Affiliate\DataGrids\Admin\AffiliateDataGrid;

class AffiliateController extends Controller
{
    /**
     * Display a listing of affiliates / promoters.
     */
    public function index()
    {
        if (request()->ajax()) {
            return datagrid(AffiliateDataGrid::class)->process();
        }

        return view('affiliate::admin.promoters.index');
    }
}
