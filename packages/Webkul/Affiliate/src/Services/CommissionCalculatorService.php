<?php

namespace Webkul\Affiliate\Services;

use Carbon\Carbon;
use Webkul\Affiliate\Contracts\Affiliate;
use Webkul\Affiliate\Repositories\CommissionRepository;
use Webkul\Affiliate\Repositories\CommissionRuleRepository;
use Webkul\Category\Models\Category;
use Webkul\Product\Models\Product;
use Webkul\Sales\Contracts\Order;
use Webkul\Sales\Contracts\OrderItem;

class CommissionCalculatorService
{
    /**
     * Create a new service instance.
     */
    public function __construct(
        protected CommissionRuleRepository $commissionRuleRepository,
        protected CommissionRepository $commissionRepository
    ) {}

    /**
     * Calculate and record commissions for an order.
     */
    public function calculateForOrder(Order $order, Affiliate $affiliate): array
    {
        if (! $this->isProgramActive()) {
            return [];
        }

        if ($this->isSelfReferral($order, $affiliate)) {
            return [];
        }

        $minSubtotal = (float) core()->getConfigData('affiliate.general.min_order_subtotal') ?: 0;

        if ($order->sub_total < $minSubtotal) {
            return [];
        }

        $clearanceDays = (int) (core()->getConfigData('affiliate.general.settings.clearance_days') ?? core()->getConfigData('affiliate.general.clearance_days') ?? 7);
        $clearsAt = Carbon::now()->addDays($clearanceDays);
        $maxOrderCap = (float) (core()->getConfigData('affiliate.general.settings.max_commission_per_order') ?? core()->getConfigData('affiliate.general.max_commission_per_order') ?: 0);

        $commissions = [];
        $totalCommission = 0;

        foreach ($order->items as $item) {
            $ruleResult = $this->resolveRuleForItem($item);

            if (! $ruleResult || $ruleResult['is_excluded'] || $ruleResult['commission_amount'] <= 0) {
                continue;
            }

            $commissions[] = [
                'order_item' => $item,
                'applied_tier' => $ruleResult['applied_tier'],
                'commission_type' => $ruleResult['commission_type'],
                'commission_rate' => $ruleResult['commission_rate'],
                'commission_amount' => $ruleResult['commission_amount'],
            ];

            $totalCommission += $ruleResult['commission_amount'];
        }

        if (empty($commissions)) {
            return [];
        }

        $scaleFactor = 1.0;

        if ($maxOrderCap > 0 && $totalCommission > $maxOrderCap) {
            $scaleFactor = $maxOrderCap / $totalCommission;
        }

        $createdRecords = [];

        foreach ($commissions as $data) {
            /** @var OrderItem $item */
            $item = $data['order_item'];
            $finalAmount = round($data['commission_amount'] * $scaleFactor, 4);

            $created = $this->commissionRepository->create([
                'affiliate_id' => $affiliate->id,
                'order_id' => $order->id,
                'order_item_id' => $item->id,
                'product_id' => $item->product_id,
                'item_price' => $item->price,
                'item_qty' => $item->qty_ordered,
                'applied_tier' => $data['applied_tier'],
                'commission_type' => $data['commission_type'],
                'commission_rate' => $data['commission_rate'],
                'commission_amount' => $finalAmount,
                'status' => 'pending',
                'clears_at' => $clearsAt,
            ]);

            $createdRecords[] = $created;
        }

        return $createdRecords;
    }

    /**
     * Resolve the active rule and commission amount for an order item.
     */
    public function resolveRuleForItem(OrderItem $item): ?array
    {
        $productId = $item->product_id;
        $productRule = $this->commissionRuleRepository->getRule('product', $productId);

        if ($productRule) {
            if ($productRule->is_excluded) {
                return ['is_excluded' => true, 'commission_amount' => 0];
            }

            return $this->formatRuleResult('product', $productRule->commission_type, (float) $productRule->commission_value, $item);
        }

        $categoryRule = $this->resolveCategoryRule($productId);

        if ($categoryRule) {
            if ($categoryRule->is_excluded) {
                return ['is_excluded' => true, 'commission_amount' => 0];
            }

            return $this->formatRuleResult('category', $categoryRule->commission_type, (float) $categoryRule->commission_value, $item);
        }

        $globalType = core()->getConfigData('affiliate.general.settings.default_commission_type') ?: (core()->getConfigData('affiliate.general.default_commission_type') ?: 'percentage');
        $globalValue = (float) (core()->getConfigData('affiliate.general.settings.default_commission_value') ?? core()->getConfigData('affiliate.general.default_commission_value') ?? 5.0);

        return $this->formatRuleResult('global', $globalType, $globalValue, $item);
    }

    /**
     * Format rule calculation result.
     */
    protected function formatRuleResult(string $tier, string $type, float $value, OrderItem $item): array
    {
        $basePrice = (float) ($item->base_total ?? ($item->price * $item->qty_ordered));

        if ($type === 'fixed') {
            $amount = $value * (int) $item->qty_ordered;
        } else {
            $amount = ($basePrice * $value) / 100;
        }

        return [
            'is_excluded' => false,
            'applied_tier' => $tier,
            'commission_type' => $type,
            'commission_rate' => $value,
            'commission_amount' => round(max(0, $amount), 4),
        ];
    }

    /**
     * Resolve the category rule for a product, prioritizing highest rate.
     */
    protected function resolveCategoryRule(int $productId): ?object
    {
        $product = Product::find($productId);

        if (! $product) {
            return null;
        }

        $allCategoryIds = [];
        foreach ($product->categories as $category) {
            $allCategoryIds[] = (int) $category->id;
            $curr = $category;
            while ($curr && $curr->parent_id) {
                $allCategoryIds[] = (int) $curr->parent_id;
                $curr = $curr->parent;
            }
        }
        $allCategoryIds = array_values(array_unique(array_filter($allCategoryIds)));

        if (empty($allCategoryIds)) {
            return null;
        }

        $rules = $this->commissionRuleRepository->findWhere([
            'entity_type' => 'category',
            'status' => 1,
        ])->whereIn('entity_id', $allCategoryIds);

        if ($rules->isEmpty()) {
            return null;
        }

        return $rules->sortByDesc('commission_value')->first();
    }

    /**
     * Resolve the rule and estimated commission for a product and price.
     */
    public function resolveRuleForProduct(int $productId, float $price): array
    {
        $productRule = $this->commissionRuleRepository->getRule('product', $productId);

        if ($productRule) {
            if ($productRule->is_excluded) {
                return [
                    'is_excluded' => true,
                    'applied_tier' => 'product',
                    'commission_type' => 'percentage',
                    'commission_rate' => 0.0,
                    'commission_amount' => 0.0,
                ];
            }

            $amount = $productRule->commission_type === 'fixed'
                ? (float) $productRule->commission_value
                : ($price * (float) $productRule->commission_value) / 100;

            return [
                'is_excluded' => false,
                'applied_tier' => 'product',
                'commission_type' => $productRule->commission_type,
                'commission_rate' => (float) $productRule->commission_value,
                'commission_amount' => round(max(0, $amount), 2),
            ];
        }

        $categoryRule = $this->resolveCategoryRule($productId);

        if ($categoryRule) {
            if ($categoryRule->is_excluded) {
                return [
                    'is_excluded' => true,
                    'applied_tier' => 'category',
                    'commission_type' => 'percentage',
                    'commission_rate' => 0.0,
                    'commission_amount' => 0.0,
                ];
            }

            $amount = $categoryRule->commission_type === 'fixed'
                ? (float) $categoryRule->commission_value
                : ($price * (float) $categoryRule->commission_value) / 100;

            return [
                'is_excluded' => false,
                'applied_tier' => 'category',
                'commission_type' => $categoryRule->commission_type,
                'commission_rate' => (float) $categoryRule->commission_value,
                'commission_amount' => round(max(0, $amount), 2),
            ];
        }

        $globalType = core()->getConfigData('affiliate.general.settings.default_commission_type') ?: (core()->getConfigData('affiliate.general.default_commission_type') ?: 'percentage');
        $globalValue = (float) (core()->getConfigData('affiliate.general.settings.default_commission_value') ?? core()->getConfigData('affiliate.general.default_commission_value') ?? 5.0);

        $amount = $globalType === 'fixed'
            ? $globalValue
            : ($price * $globalValue) / 100;

        return [
            'is_excluded' => false,
            'applied_tier' => 'global',
            'commission_type' => $globalType,
            'commission_rate' => $globalValue,
            'commission_amount' => round(max(0, $amount), 2),
        ];
    }

    /**
     * Check if the order is an anti-fraud self-referral.
     */
    public function isSelfReferral(Order $order, Affiliate $affiliate): bool
    {
        if ($order->customer_id && (int) $order->customer_id === (int) $affiliate->customer_id) {
            return true;
        }

        $affiliateCustomer = $affiliate->customer;

        if (! $affiliateCustomer) {
            return false;
        }

        if (strcasecmp($order->customer_email, $affiliateCustomer->email) === 0) {
            return true;
        }

        if (! empty($affiliateCustomer->phone)) {
            $shippingPhone = $order->shipping_address?->phone;
            $billingPhone = $order->billing_address?->phone;

            if ($shippingPhone && $this->normalizePhone($shippingPhone) === $this->normalizePhone($affiliateCustomer->phone)) {
                return true;
            }

            if ($billingPhone && $this->normalizePhone($billingPhone) === $this->normalizePhone($affiliateCustomer->phone)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Normalize phone numbers for comparison.
     */
    protected function normalizePhone(?string $phone): string
    {
        return preg_replace('/\D+/', '', (string) $phone);
    }

    /**
     * Check if affiliate program is globally active.
     */
    public function isProgramActive(): bool
    {
        $status = core()->getConfigData('affiliate.general.settings.status') ?? core()->getConfigData('affiliate.general.status');

        return $status === null || (bool) $status;
    }
}
