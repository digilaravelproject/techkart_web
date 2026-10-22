<?php

namespace Webkul\Affiliate\Http\Controllers\Admin;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Webkul\Affiliate\DataGrids\Admin\CommissionRuleDataGrid;
use Webkul\Affiliate\Repositories\CommissionRuleRepository;
use Webkul\Category\Repositories\CategoryRepository;

class CommissionRuleController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        protected CommissionRuleRepository $commissionRuleRepository,
        protected CategoryRepository $categoryRepository
    ) {}

    /**
     * Display listing of commission rules.
     */
    public function index()
    {
        if (request()->ajax()) {
            return datagrid(CommissionRuleDataGrid::class)->process();
        }

        $categories = $this->categoryRepository->all();

        return view('affiliate::admin.rules.index', compact('categories'));
    }

    /**
     * Store a newly created commission rule.
     */
    public function store()
    {
        $entityId = request('entity_id') ?: (request('entity_type') === 'category' ? request('category_entity_id') : request('product_entity_id'));

        request()->merge(['entity_id' => $entityId]);

        request()->validate([
            'entity_type' => 'required|in:category,product',
            'entity_id' => 'required|integer',
            'commission_type' => 'required|in:percentage,fixed',
            'commission_value' => 'required|numeric|min:0',
            'is_excluded' => 'nullable|boolean',
            'status' => 'nullable|boolean',
        ]);

        $data = [
            'entity_type' => request('entity_type'),
            'entity_id' => (int) request('entity_id'),
            'commission_type' => request('commission_type'),
            'commission_value' => (float) request('commission_value'),
            'is_excluded' => (bool) request('is_excluded', false),
            'status' => (bool) request('status', true),
        ];

        $existing = $this->commissionRuleRepository->findOneWhere([
            'entity_type' => $data['entity_type'],
            'entity_id' => $data['entity_id'],
        ]);

        if ($existing) {
            $existing->update($data);
        } else {
            $this->commissionRuleRepository->create($data);
        }

        session()->flash('success', trans('affiliate::app.admin.rules.create-success'));

        return redirect()->route('admin.affiliate.rules.index');
    }

    /**
     * Remove the specified commission rule.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->commissionRuleRepository->delete($id);

            return new JsonResponse([
                'message' => trans('affiliate::app.admin.rules.delete-success'),
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
