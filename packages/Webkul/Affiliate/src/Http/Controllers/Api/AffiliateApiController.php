<?php

namespace Webkul\Affiliate\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Webkul\Affiliate\Repositories\AffiliateRepository;
use Webkul\Affiliate\Repositories\CommissionRepository;
use Webkul\Affiliate\Repositories\PayoutRepository;
use Webkul\Affiliate\Repositories\WalletRepository;
use Webkul\Affiliate\Repositories\WalletTransactionRepository;
use Webkul\Affiliate\Services\CommissionCalculatorService;
use Webkul\Affiliate\Services\WalletTransactionService;
use Webkul\Customer\Repositories\CustomerRepository;
use Webkul\Product\Repositories\ProductRepository;

class AffiliateApiController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        protected AffiliateRepository $affiliateRepository,
        protected WalletRepository $walletRepository,
        protected WalletTransactionRepository $transactionRepository,
        protected CommissionRepository $commissionRepository,
        protected PayoutRepository $payoutRepository,
        protected WalletTransactionService $walletTransactionService,
        protected CommissionCalculatorService $commissionCalculatorService,
        protected ProductRepository $productRepository,
        protected CustomerRepository $customerRepository
    ) {}

    /**
     * Resolve the authenticated customer from Sanctum Bearer token or customer session.
     */
    protected function resolveCustomer()
    {
        $customer = Auth::guard('sanctum')->user()
            ?? Auth::guard('customer')->user()
            ?? Auth::guard('api')->user();

        if ($customer) {
            return $customer;
        }

        if (request()->hasHeader('X-Customer-Id')) {
            $customerId = (int) request()->header('X-Customer-Id');
            if ($customerId > 0) {
                return $this->customerRepository->find($customerId);
            }
        }

        return null;
    }

    /**
     * Return standard unauthenticated error response.
     */
    protected function unauthenticatedResponse(): JsonResponse
    {
        return new JsonResponse([
            'success' => false,
            'message' => 'Unauthenticated. Please provide a valid Bearer token.',
        ], 401);
    }

    /**
     * Get customer affiliate dashboard and wallet overview.
     */
    public function dashboard(): JsonResponse
    {
        $customer = $this->resolveCustomer();

        if (! $customer) {
            return $this->unauthenticatedResponse();
        }

        $affiliate = $this->affiliateRepository->getOrCreateForCustomer($customer->id);
        $wallet = $this->walletRepository->getByAffiliateId($affiliate->id);

        $minWithdrawal = (float) (core()->getConfigData('affiliate.general.min_withdrawal_amount') ?? 500);
        $clearanceDays = (int) (core()->getConfigData('affiliate.general.clearance_days') ?? 7);
        $defaultType = core()->getConfigData('affiliate.general.default_commission_type') ?: 'percentage';
        $defaultValue = (float) (core()->getConfigData('affiliate.general.default_commission_value') ?? 5.0);

        $conversionRate = $affiliate->total_clicks > 0
            ? round(($affiliate->total_orders / $affiliate->total_clicks) * 100, 1)
            : 0.0;

        $referralLink = url('/').'?ref='.$affiliate->referral_code;

        return new JsonResponse([
            'success' => true,
            'data' => [
                'affiliate_id' => $affiliate->id,
                'status' => $affiliate->status,
                'referral_code' => $affiliate->referral_code,
                'referral_link' => $referralLink,
                'wallet' => [
                    'available_balance' => (float) $wallet->available_balance,
                    'available_formatted' => '₹'.number_format((float) $wallet->available_balance, 2),
                    'pending_balance' => (float) $wallet->pending_balance,
                    'pending_formatted' => '₹'.number_format((float) $wallet->pending_balance, 2),
                    'locked_balance' => (float) $wallet->locked_balance,
                    'locked_formatted' => '₹'.number_format((float) $wallet->locked_balance, 2),
                    'lifetime_earned' => (float) $wallet->total_earned,
                    'lifetime_formatted' => '₹'.number_format((float) $wallet->total_earned, 2),
                    'total_withdrawn' => (float) $wallet->total_withdrawn,
                    'total_withdrawn_formatted' => '₹'.number_format((float) $wallet->total_withdrawn, 2),
                    'currency' => 'INR',
                    'currency_symbol' => '₹',
                ],
                'stats' => [
                    'total_clicks' => (int) $affiliate->total_clicks,
                    'total_orders' => (int) $affiliate->total_orders,
                    'conversion_rate' => $conversionRate,
                ],
                'payout_profile' => [
                    'method' => $affiliate->payout_method,
                    'details' => $affiliate->payout_details ?? [],
                ],
                'config' => [
                    'min_withdrawal_amount' => $minWithdrawal,
                    'clearance_days' => $clearanceDays,
                    'default_commission_type' => $defaultType,
                    'default_commission_value' => $defaultValue,
                ],
            ],
        ]);
    }

    /**
     * Register or activate affiliate account for current customer.
     */
    public function register(): JsonResponse
    {
        $customer = $this->resolveCustomer();

        if (! $customer) {
            return $this->unauthenticatedResponse();
        }

        $affiliate = $this->affiliateRepository->getOrCreateForCustomer($customer->id);

        return new JsonResponse([
            'success' => true,
            'message' => 'Affiliate account is active.',
            'data' => [
                'affiliate_id' => $affiliate->id,
                'referral_code' => $affiliate->referral_code,
                'referral_link' => url('/').'?ref='.$affiliate->referral_code,
                'status' => $affiliate->status,
            ],
        ]);
    }

    /**
     * Generate custom referral link for any store URL or product.
     */
    public function generateLink(Request $request): JsonResponse
    {
        $customer = $this->resolveCustomer();

        if (! $customer) {
            return $this->unauthenticatedResponse();
        }

        $affiliate = $this->affiliateRepository->getOrCreateForCustomer($customer->id);

        $url = $request->input('url');
        $productId = $request->input('product_id');

        if ($productId) {
            $product = $this->productRepository->find($productId);
            if ($product) {
                $url = route('shop.product_or_category.index', $product->url_key);
            }
        }

        if (! $url) {
            $url = url('/');
        }

        $separator = parse_url($url, PHP_URL_QUERY) ? '&' : '?';
        $generatedUrl = $url.$separator.'ref='.$affiliate->referral_code;

        return new JsonResponse([
            'success' => true,
            'data' => [
                'referral_code' => $affiliate->referral_code,
                'share_url' => $generatedUrl,
                'share_message' => 'Check out this product on BEENZO! Buy now: '.$generatedUrl,
            ],
        ]);
    }

    /**
     * Get product share & earn details and estimated commission.
     */
    public function productShareInfo(int $productId): JsonResponse
    {
        $product = $this->productRepository->find($productId);

        if (! $product) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Product not found.',
            ], 404);
        }

        $price = (float) $product->getTypeInstance()->getMinimalPrice();
        $rule = $this->commissionCalculatorService->resolveRuleForProduct($productId, $price);

        $customer = $this->resolveCustomer();
        $affiliate = $customer ? $this->affiliateRepository->findOneByField('customer_id', $customer->id) : null;

        $productUrl = route('shop.product_or_category.index', $product->url_key);
        $shareUrl = $productUrl;
        $referralCode = null;

        if ($affiliate) {
            $separator = parse_url($productUrl, PHP_URL_QUERY) ? '&' : '?';
            $shareUrl = $productUrl.$separator.'ref='.$affiliate->referral_code;
            $referralCode = $affiliate->referral_code;
        }

        return new JsonResponse([
            'success' => true,
            'data' => [
                'product_id' => $product->id,
                'name' => $product->name,
                'url_key' => $product->url_key,
                'price' => $price,
                'price_formatted' => '₹'.number_format($price, 2),
                'commission' => [
                    'is_excluded' => $rule['is_excluded'],
                    'type' => $rule['commission_type'],
                    'rate' => $rule['commission_rate'],
                    'estimated_amount' => $rule['commission_amount'],
                    'display_text' => $rule['commission_type'] === 'fixed'
                        ? 'Earn ₹'.number_format($rule['commission_amount'], 2)
                        : 'Earn ₹'.number_format($rule['commission_amount'], 2).' ('.$rule['commission_rate'].'%)',
                ],
                'is_affiliate' => (bool) $affiliate,
                'referral_code' => $referralCode,
                'share_url' => $shareUrl,
                'share_message' => 'Hey! Check out '.$product->name.' on BEENZO: '.$shareUrl,
            ],
        ]);
    }

    /**
     * Update customer affiliate payout method and account details.
     */
    public function updatePayoutSettings(Request $request): JsonResponse
    {
        $customer = $this->resolveCustomer();

        if (! $customer) {
            return $this->unauthenticatedResponse();
        }

        $affiliate = $this->affiliateRepository->getOrCreateForCustomer($customer->id);

        $request->validate([
            'payout_method' => 'required|in:upi,bank_transfer',
            'upi_id' => 'required_if:payout_method,upi|nullable|string|max:100',
            'account_holder' => 'required_if:payout_method,bank_transfer|nullable|string|max:150',
            'account_number' => 'required_if:payout_method,bank_transfer|nullable|string|max:50',
            'bank_name' => 'required_if:payout_method,bank_transfer|nullable|string|max:100',
            'ifsc_code' => 'required_if:payout_method,bank_transfer|nullable|string|max:20',
        ]);

        $method = $request->input('payout_method');

        $details = $method === 'upi' ? [
            'upi_id' => $request->input('upi_id'),
        ] : [
            'account_holder' => $request->input('account_holder'),
            'account_number' => $request->input('account_number'),
            'bank_name' => $request->input('bank_name'),
            'ifsc_code' => $request->input('ifsc_code'),
        ];

        $affiliate->payout_method = $method;
        $affiliate->payout_details = $details;
        $affiliate->save();

        return new JsonResponse([
            'success' => true,
            'message' => 'Payout details saved successfully.',
            'data' => [
                'payout_method' => $affiliate->payout_method,
                'payout_details' => $affiliate->payout_details,
            ],
        ]);
    }

    /**
     * Get paginated affiliate wallet passbook transactions.
     */
    public function transactions(Request $request): JsonResponse
    {
        $customer = $this->resolveCustomer();

        if (! $customer) {
            return $this->unauthenticatedResponse();
        }

        $affiliate = $this->affiliateRepository->getOrCreateForCustomer($customer->id);
        $perPage = (int) ($request->input('limit') ?? 15);

        $paginator = $this->transactionRepository
            ->scopeQuery(function ($query) use ($affiliate) {
                return $query->where('affiliate_id', $affiliate->id)
                    ->orderBy('id', 'desc');
            })
            ->paginate($perPage);

        return new JsonResponse([
            'success' => true,
            'data' => collect($paginator->items())->map(function ($item) {
                return [
                    'id' => $item->id,
                    'transaction_number' => $item->transaction_number,
                    'type' => $item->type,
                    'amount' => (float) $item->amount,
                    'amount_formatted' => '₹'.number_format((float) $item->amount, 2),
                    'balance_after' => (float) $item->balance_after,
                    'balance_after_formatted' => '₹'.number_format((float) $item->balance_after, 2),
                    'status' => $item->status,
                    'title' => $item->title,
                    'description' => $item->description,
                    'created_at' => $item->created_at?->format('Y-m-d H:i:s'),
                ];
            }),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ]);
    }

    /**
     * Get paginated earned commissions from customer referral orders.
     */
    public function commissions(Request $request): JsonResponse
    {
        $customer = $this->resolveCustomer();

        if (! $customer) {
            return $this->unauthenticatedResponse();
        }

        $affiliate = $this->affiliateRepository->getOrCreateForCustomer($customer->id);
        $perPage = (int) ($request->input('limit') ?? 15);

        $paginator = $this->commissionRepository
            ->with(['order'])
            ->scopeQuery(function ($query) use ($affiliate) {
                return $query->where('affiliate_id', $affiliate->id)
                    ->orderBy('id', 'desc');
            })
            ->paginate($perPage);

        return new JsonResponse([
            'success' => true,
            'data' => collect($paginator->items())->map(function ($c) {
                return [
                    'id' => $c->id,
                    'order_id' => $c->order_id,
                    'order_number' => $c->order?->increment_id ?? '#'.$c->order_id,
                    'item_price' => (float) $c->item_price,
                    'item_qty' => (int) $c->item_qty,
                    'commission_type' => $c->commission_type,
                    'commission_rate' => (float) $c->commission_rate,
                    'commission_amount' => (float) $c->commission_amount,
                    'commission_formatted' => '₹'.number_format((float) $c->commission_amount, 2),
                    'status' => $c->status,
                    'clears_at' => $c->clears_at?->format('Y-m-d H:i:s'),
                    'created_at' => $c->created_at?->format('Y-m-d H:i:s'),
                ];
            }),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ]);
    }

    /**
     * Submit payout withdrawal request from available wallet balance.
     */
    public function requestPayout(Request $request): JsonResponse
    {
        $customer = $this->resolveCustomer();

        if (! $customer) {
            return $this->unauthenticatedResponse();
        }

        $affiliate = $this->affiliateRepository->getOrCreateForCustomer($customer->id);
        $wallet = $this->walletRepository->getByAffiliateId($affiliate->id);

        $minAmount = (float) (core()->getConfigData('affiliate.general.min_withdrawal_amount') ?? 500);

        $request->validate([
            'amount' => 'required|numeric|min:'.$minAmount.'|max:'.$wallet->available_balance,
            'payout_method' => 'nullable|in:upi,bank_transfer',
            'upi_id' => 'nullable|string|max:100',
            'account_holder' => 'nullable|string|max:150',
            'account_number' => 'nullable|string|max:50',
            'bank_name' => 'nullable|string|max:100',
            'ifsc_code' => 'nullable|string|max:20',
        ]);

        $method = $request->input('payout_method') ?: $affiliate->payout_method;

        if (! $method) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Please provide a payout method (upi or bank_transfer).',
            ], 422);
        }

        if ($method === 'upi') {
            $upiId = $request->input('upi_id') ?: ($affiliate->payout_details['upi_id'] ?? null);
            if (! $upiId) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'UPI ID is required for UPI payout.',
                ], 422);
            }
            $details = ['upi_id' => $upiId];
        } else {
            $acc = $request->input('account_number') ?: ($affiliate->payout_details['account_number'] ?? null);
            $ifsc = $request->input('ifsc_code') ?: ($affiliate->payout_details['ifsc_code'] ?? null);
            $holder = $request->input('account_holder') ?: ($affiliate->payout_details['account_holder'] ?? null);
            $bank = $request->input('bank_name') ?: ($affiliate->payout_details['bank_name'] ?? null);

            if (! $acc || ! $ifsc || ! $holder) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Account number, IFSC code, and account holder name are required for Bank Transfer.',
                ], 422);
            }

            $details = [
                'account_number' => $acc,
                'ifsc_code' => $ifsc,
                'account_holder' => $holder,
                'bank_name' => $bank,
            ];
        }

        $amount = (float) $request->input('amount');

        try {
            $payout = $this->walletTransactionService->requestPayout($affiliate, $amount, $method, $details);
            $freshWallet = $this->walletRepository->getByAffiliateId($affiliate->id);

            return new JsonResponse([
                'success' => true,
                'message' => trans('affiliate::app.shop.account.request-success'),
                'data' => [
                    'payout_id' => $payout->id,
                    'payout_number' => $payout->payout_number,
                    'amount' => (float) $payout->amount,
                    'amount_formatted' => '₹'.number_format((float) $payout->amount, 2),
                    'method' => $payout->payout_method,
                    'status' => $payout->status,
                    'available_balance' => (float) $freshWallet->available_balance,
                    'locked_balance' => (float) $freshWallet->locked_balance,
                ],
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get customer payout withdrawal requests history.
     */
    public function payouts(Request $request): JsonResponse
    {
        $customer = $this->resolveCustomer();

        if (! $customer) {
            return $this->unauthenticatedResponse();
        }

        $affiliate = $this->affiliateRepository->getOrCreateForCustomer($customer->id);
        $perPage = (int) ($request->input('limit') ?? 15);

        $paginator = $this->payoutRepository
            ->scopeQuery(function ($query) use ($affiliate) {
                return $query->where('affiliate_id', $affiliate->id)
                    ->orderBy('id', 'desc');
            })
            ->paginate($perPage);

        return new JsonResponse([
            'success' => true,
            'data' => collect($paginator->items())->map(function ($p) {
                return [
                    'id' => $p->id,
                    'payout_number' => $p->payout_number,
                    'amount' => (float) $p->amount,
                    'amount_formatted' => '₹'.number_format((float) $p->amount, 2),
                    'payout_method' => $p->payout_method,
                    'payout_details' => is_string($p->payout_details) ? json_decode($p->payout_details, true) : (array) $p->payout_details,
                    'status' => $p->status,
                    'transaction_reference' => $p->transaction_reference,
                    'admin_notes' => $p->admin_notes,
                    'created_at' => $p->created_at?->format('Y-m-d H:i:s'),
                ];
            }),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ]);
    }

    /**
     * Track a referral link click from mobile app or web.
     */
    public function trackReferral(Request $request): JsonResponse
    {
        $code = $request->input('referral_code');

        if (! $code) {
            return new JsonResponse([
                'success' => false,
                'message' => 'referral_code is required.',
            ], 422);
        }

        $affiliate = $this->affiliateRepository->findOneByField('referral_code', $code);

        if (! $affiliate || $affiliate->status !== 'active') {
            return new JsonResponse([
                'success' => false,
                'message' => 'Invalid or inactive referral code.',
            ], 404);
        }

        $this->affiliateRepository->incrementClicks($affiliate->id);

        return new JsonResponse([
            'success' => true,
            'message' => 'Referral click recorded.',
            'data' => [
                'referral_code' => $affiliate->referral_code,
                'affiliate_id' => $affiliate->id,
            ],
        ]);
    }
}
