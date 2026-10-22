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

        $categoryTree = [];
        foreach ($categories as $cat) {
            $name = $cat->name ?? 'Category #'.$cat->id;
            $path = [$name];
            $curr = $cat;
            while ($curr && $curr->parent_id) {
                $parent = $categories->firstWhere('id', $curr->parent_id);
                if ($parent) {
                    array_unshift($path, $parent->name ?? 'Category #'.$parent->id);
                    $curr = $parent;
                } else {
                    break;
                }
            }
            $categoryTree[] = (object) [
                'id' => $cat->id,
                'name' => implode(' > ', $path),
            ];
        }

        usort($categoryTree, fn ($a, $b) => strcmp($a->name, $b->name));

        $products = \DB::table('product_flat')
            ->select('product_id as id', 'name')
            ->where('locale', app()->getLocale())
            ->whereNotNull('name')
            ->orderBy('name')
            ->get();

        return view('affiliate::admin.rules.index', [
            'categories' => $categoryTree,
            'products' => $products,
        ]);
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
