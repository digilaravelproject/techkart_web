<?php

namespace Webkul\Affiliate\Services;

use Illuminate\Support\Facades\Auth;
use Webkul\Affiliate\Repositories\AffiliateRepository;
use Webkul\Affiliate\Repositories\CommissionRepository;
use Webkul\Affiliate\Repositories\PayoutRepository;
use Webkul\Affiliate\Repositories\WalletRepository;
use Webkul\Affiliate\Repositories\WalletTransactionRepository;
use Webkul\Customer\Repositories\CustomerRepository;
use Webkul\Product\Repositories\ProductRepository;

class AffiliateGraphQLService
{
    /**
     * Create a new service instance.
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
    public function resolveCustomer()
    {
        $customer = null;

        try {
            $customer = Auth::guard('sanctum')->user();
        } catch (\Throwable $e) {
        }

        if (! $customer) {
            try {
                $customer = Auth::guard('customer')->user();
            } catch (\Throwable $e) {
            }
        }

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
     * Get customer affiliate dashboard data.
     */
    public function getDashboard(): array
    {
        $customer = $this->resolveCustomer();

        if (! $customer) {
            throw new \Exception('Unauthenticated. Please provide a valid Bearer token.');
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

        $pDetails = is_string($affiliate->payout_details) ? json_decode($affiliate->payout_details, true) : (array) $affiliate->payout_details;

        return [
            'affiliateId' => $affiliate->id,
            'affiliate_id' => $affiliate->id,
            'status' => $affiliate->status,
            'referralCode' => $affiliate->referral_code,
            'referral_code' => $affiliate->referral_code,
            'referralLink' => $referralLink,
            'referral_link' => $referralLink,
            'wallet' => [
                'availableBalance' => (float) $wallet->available_balance,
                'available_balance' => (float) $wallet->available_balance,
                'availableFormatted' => '₹'.number_format((float) $wallet->available_balance, 2),
                'available_formatted' => '₹'.number_format((float) $wallet->available_balance, 2),
                'pendingBalance' => (float) $wallet->pending_balance,
                'pending_balance' => (float) $wallet->pending_balance,
                'pendingFormatted' => '₹'.number_format((float) $wallet->pending_balance, 2),
                'pending_formatted' => '₹'.number_format((float) $wallet->pending_balance, 2),
                'lockedBalance' => (float) $wallet->locked_balance,
                'locked_balance' => (float) $wallet->locked_balance,
                'lockedFormatted' => '₹'.number_format((float) $wallet->locked_balance, 2),
                'locked_formatted' => '₹'.number_format((float) $wallet->locked_balance, 2),
                'lifetimeEarned' => (float) $wallet->total_earned,
                'lifetime_earned' => (float) $wallet->total_earned,
                'lifetimeFormatted' => '₹'.number_format((float) $wallet->total_earned, 2),
                'lifetime_formatted' => '₹'.number_format((float) $wallet->total_earned, 2),
                'totalWithdrawn' => (float) $wallet->total_withdrawn,
                'total_withdrawn' => (float) $wallet->total_withdrawn,
                'currency' => 'INR',
                'currencySymbol' => '₹',
                'currency_symbol' => '₹',
            ],
            'stats' => [
                'totalClicks' => (int) $affiliate->total_clicks,
                'total_clicks' => (int) $affiliate->total_clicks,
                'totalOrders' => (int) $affiliate->total_orders,
                'total_orders' => (int) $affiliate->total_orders,
                'conversionRate' => $conversionRate,
                'conversion_rate' => $conversionRate,
            ],
            'payoutProfile' => [
                'method' => $affiliate->payout_method,
                'payout_method' => $affiliate->payout_method,
                'upiId' => $pDetails['upi_id'] ?? null,
                'upi_id' => $pDetails['upi_id'] ?? null,
                'accountNumber' => $pDetails['account_number'] ?? null,
                'account_number' => $pDetails['account_number'] ?? null,
                'ifscCode' => $pDetails['ifsc_code'] ?? null,
                'ifsc_code' => $pDetails['ifsc_code'] ?? null,
                'accountHolder' => $pDetails['account_holder'] ?? null,
                'account_holder' => $pDetails['account_holder'] ?? null,
                'bankName' => $pDetails['bank_name'] ?? null,
                'bank_name' => $pDetails['bank_name'] ?? null,
            ],
            'config' => [
                'minWithdrawalAmount' => $minWithdrawal,
                'min_withdrawal_amount' => $minWithdrawal,
                'clearanceDays' => $clearanceDays,
                'clearance_days' => $clearanceDays,
                'defaultCommissionType' => $defaultType,
                'defaultCommissionValue' => $defaultValue,
            ],
        ];
    }

    /**
     * Get product share & earn information.
     */
    public function getProductShareInfo(int $productId): array
    {
        $product = $this->productRepository->find($productId);

        if (! $product) {
            throw new \Exception('Product not found with ID: '.$productId);
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

        return [
            'productId' => $product->id,
            'product_id' => $product->id,
            'name' => $product->name,
            'urlKey' => $product->url_key,
            'url_key' => $product->url_key,
            'price' => $price,
            'priceFormatted' => '₹'.number_format($price, 2),
            'price_formatted' => '₹'.number_format($price, 2),
            'commission' => [
                'isExcluded' => $rule['is_excluded'],
                'is_excluded' => $rule['is_excluded'],
                'type' => $rule['commission_type'],
                'commission_type' => $rule['commission_type'],
                'rate' => $rule['commission_rate'],
                'commission_rate' => $rule['commission_rate'],
                'estimatedAmount' => $rule['commission_amount'],
                'estimated_amount' => $rule['commission_amount'],
                'displayText' => $rule['commission_type'] === 'fixed'
                    ? 'Earn ₹'.number_format($rule['commission_amount'], 2)
                    : 'Earn ₹'.number_format($rule['commission_amount'], 2).' ('.$rule['commission_rate'].'%)',
                'display_text' => $rule['commission_type'] === 'fixed'
                    ? 'Earn ₹'.number_format($rule['commission_amount'], 2)
                    : 'Earn ₹'.number_format($rule['commission_amount'], 2).' ('.$rule['commission_rate'].'%)',
            ],
            'isAffiliate' => (bool) $affiliate,
            'is_affiliate' => (bool) $affiliate,
            'referralCode' => $referralCode,
            'referral_code' => $referralCode,
            'shareUrl' => $shareUrl,
            'share_url' => $shareUrl,
            'shareMessage' => 'Hey! Check out '.$product->name.' on BEENZO: '.$shareUrl,
            'share_message' => 'Hey! Check out '.$product->name.' on BEENZO: '.$shareUrl,
        ];
    }

    /**
     * Get paginated wallet passbook transactions.
     */
    public function getTransactions(int $page = 1, int $limit = 15): array
    {
        $customer = $this->resolveCustomer();

        if (! $customer) {
            throw new \Exception('Unauthenticated. Please provide a valid Bearer token.');
        }

        $affiliate = $this->affiliateRepository->getOrCreateForCustomer($customer->id);

        $paginator = $this->transactionRepository
            ->getModel()
            ->where('affiliate_id', $affiliate->id)
            ->orderBy('id', 'desc')
            ->paginate($limit, ['*'], 'page', $page);

        return [
            'data' => collect($paginator->items())->map(function ($item) {
                return [
                    'id' => $item->id,
                    'transactionNumber' => $item->transaction_number,
                    'transaction_number' => $item->transaction_number,
                    'type' => $item->type,
                    'amount' => (float) $item->amount,
                    'amountFormatted' => '₹'.number_format((float) $item->amount, 2),
                    'amount_formatted' => '₹'.number_format((float) $item->amount, 2),
                    'balanceAfter' => (float) $item->balance_after,
                    'balance_after' => (float) $item->balance_after,
                    'balanceAfterFormatted' => '₹'.number_format((float) $item->balance_after, 2),
                    'balance_after_formatted' => '₹'.number_format((float) $item->balance_after, 2),
                    'status' => $item->status,
                    'title' => $item->title,
                    'description' => $item->description,
                    'createdAt' => $item->created_at?->format('Y-m-d H:i:s'),
                    'created_at' => $item->created_at?->format('Y-m-d H:i:s'),
                ];
            })->all(),
            'meta' => [
                'currentPage' => $paginator->currentPage(),
                'current_page' => $paginator->currentPage(),
                'lastPage' => $paginator->lastPage(),
                'last_page' => $paginator->lastPage(),
                'perPage' => $paginator->perPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ];
    }

    /**
     * Get paginated commissions from referral orders.
     */
    public function getCommissions(int $page = 1, int $limit = 15): array
    {
        $customer = $this->resolveCustomer();

        if (! $customer) {
            throw new \Exception('Unauthenticated. Please provide a valid Bearer token.');
        }

        $affiliate = $this->affiliateRepository->getOrCreateForCustomer($customer->id);

        $paginator = $this->commissionRepository
            ->getModel()
            ->with(['order'])
            ->where('affiliate_id', $affiliate->id)
            ->orderBy('id', 'desc')
            ->paginate($limit, ['*'], 'page', $page);

        return [
            'data' => collect($paginator->items())->map(function ($c) {
                return [
                    'id' => $c->id,
                    'orderId' => $c->order_id,
                    'order_id' => $c->order_id,
                    'orderNumber' => $c->order?->increment_id ?? '#'.$c->order_id,
                    'order_number' => $c->order?->increment_id ?? '#'.$c->order_id,
                    'itemPrice' => (float) $c->item_price,
                    'item_price' => (float) $c->item_price,
                    'itemQty' => (int) $c->item_qty,
                    'item_qty' => (int) $c->item_qty,
                    'commissionType' => $c->commission_type,
                    'commission_type' => $c->commission_type,
                    'commissionRate' => (float) $c->commission_rate,
                    'commission_rate' => (float) $c->commission_rate,
                    'commissionAmount' => (float) $c->commission_amount,
                    'commission_amount' => (float) $c->commission_amount,
                    'commissionFormatted' => '₹'.number_format((float) $c->commission_amount, 2),
                    'commission_formatted' => '₹'.number_format((float) $c->commission_amount, 2),
                    'status' => $c->status,
                    'clearsAt' => $c->clears_at?->format('Y-m-d H:i:s'),
                    'clears_at' => $c->clears_at?->format('Y-m-d H:i:s'),
                    'createdAt' => $c->created_at?->format('Y-m-d H:i:s'),
                    'created_at' => $c->created_at?->format('Y-m-d H:i:s'),
                ];
            })->all(),
            'meta' => [
                'currentPage' => $paginator->currentPage(),
                'current_page' => $paginator->currentPage(),
                'lastPage' => $paginator->lastPage(),
                'last_page' => $paginator->lastPage(),
                'perPage' => $paginator->perPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ];
    }

    /**
     * Get paginated customer payout withdrawal requests.
     */
    public function getPayouts(int $page = 1, int $limit = 15): array
    {
        $customer = $this->resolveCustomer();

        if (! $customer) {
            throw new \Exception('Unauthenticated. Please provide a valid Bearer token.');
        }

        $affiliate = $this->affiliateRepository->getOrCreateForCustomer($customer->id);

        $paginator = $this->payoutRepository
            ->getModel()
            ->where('affiliate_id', $affiliate->id)
            ->orderBy('id', 'desc')
            ->paginate($limit, ['*'], 'page', $page);

        return [
            'data' => collect($paginator->items())->map(function ($p) {
                return [
                    'id' => $p->id,
                    'payoutNumber' => $p->payout_number,
                    'payout_number' => $p->payout_number,
                    'amount' => (float) $p->amount,
                    'amountFormatted' => '₹'.number_format((float) $p->amount, 2),
                    'amount_formatted' => '₹'.number_format((float) $p->amount, 2),
                    'payoutMethod' => $p->payout_method,
                    'payout_method' => $p->payout_method,
                    'status' => $p->status,
                    'transactionReference' => $p->transaction_reference,
                    'transaction_reference' => $p->transaction_reference,
                    'adminNotes' => $p->admin_notes,
                    'admin_notes' => $p->admin_notes,
                    'createdAt' => $p->created_at?->format('Y-m-d H:i:s'),
                    'created_at' => $p->created_at?->format('Y-m-d H:i:s'),
                ];
            })->all(),
            'meta' => [
                'currentPage' => $paginator->currentPage(),
                'current_page' => $paginator->currentPage(),
                'lastPage' => $paginator->lastPage(),
                'last_page' => $paginator->lastPage(),
                'perPage' => $paginator->perPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ];
    }

    /**
     * Activate affiliate program for current customer.
     */
    public function registerAffiliate(): array
    {
        $customer = $this->resolveCustomer();

        if (! $customer) {
            throw new \Exception('Unauthenticated. Please provide a valid Bearer token.');
        }

        $affiliate = $this->affiliateRepository->getOrCreateForCustomer($customer->id);

        return [
            'success' => true,
            'message' => 'Affiliate account activated successfully.',
            'affiliateId' => $affiliate->id,
            'affiliate_id' => $affiliate->id,
            'referralCode' => $affiliate->referral_code,
            'referral_code' => $affiliate->referral_code,
            'referralLink' => url('/').'?ref='.$affiliate->referral_code,
            'referral_link' => url('/').'?ref='.$affiliate->referral_code,
            'status' => $affiliate->status,
        ];
    }

    /**
     * Generate custom affiliate referral link.
     */
    public function generateLink(?string $url = null, ?int $productId = null): array
    {
        $customer = $this->resolveCustomer();

        if (! $customer) {
            throw new \Exception('Unauthenticated. Please provide a valid Bearer token.');
        }

        $affiliate = $this->affiliateRepository->getOrCreateForCustomer($customer->id);

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

        return [
            'success' => true,
            'referralCode' => $affiliate->referral_code,
            'referral_code' => $affiliate->referral_code,
            'shareUrl' => $generatedUrl,
            'share_url' => $generatedUrl,
            'shareMessage' => 'Check out this product on BEENZO! Buy now: '.$generatedUrl,
            'share_message' => 'Check out this product on BEENZO! Buy now: '.$generatedUrl,
        ];
    }

    /**
     * Update customer affiliate payout method and settings.
     */
    public function updatePayoutSettings(array $args): array
    {
        $customer = $this->resolveCustomer();

        if (! $customer) {
            throw new \Exception('Unauthenticated. Please provide a valid Bearer token.');
        }

        $affiliate = $this->affiliateRepository->getOrCreateForCustomer($customer->id);

        $method = $args['payoutMethod'] ?? ($args['payout_method'] ?? 'upi');

        if ($method === 'upi') {
            $upiId = $args['upiId'] ?? ($args['upi_id'] ?? null);
            if (! $upiId) {
                throw new \InvalidArgumentException('UPI ID is required for UPI payout method.');
            }
            $details = ['upi_id' => $upiId];
        } else {
            $acc = $args['accountNumber'] ?? ($args['account_number'] ?? null);
            $ifsc = $args['ifscCode'] ?? ($args['ifsc_code'] ?? null);
            $holder = $args['accountHolder'] ?? ($args['account_holder'] ?? null);
            $bank = $args['bankName'] ?? ($args['bank_name'] ?? null);

            if (! $acc || ! $ifsc || ! $holder) {
                throw new \InvalidArgumentException('Account number, IFSC code, and account holder name are required.');
            }

            $details = [
                'account_number' => $acc,
                'ifsc_code' => $ifsc,
                'account_holder' => $holder,
                'bank_name' => $bank,
            ];
        }

        $affiliate->payout_method = $method;
        $affiliate->payout_details = $details;
        $affiliate->save();

        return [
            'success' => true,
            'message' => 'Payout settings saved successfully.',
            'payoutMethod' => $affiliate->payout_method,
            'payout_method' => $affiliate->payout_method,
            'details' => $affiliate->payout_details,
        ];
    }

    /**
     * Request payout withdrawal from available wallet balance.
     */
    public function requestPayout(array $args): array
    {
        $customer = $this->resolveCustomer();

        if (! $customer) {
            throw new \Exception('Unauthenticated. Please provide a valid Bearer token.');
        }

        $affiliate = $this->affiliateRepository->getOrCreateForCustomer($customer->id);
        $wallet = $this->walletRepository->getByAffiliateId($affiliate->id);

        $minAmount = (float) (core()->getConfigData('affiliate.general.min_withdrawal_amount') ?? 500);
        $amount = (float) ($args['amount'] ?? 0);

        if ($amount < $minAmount) {
            throw new \InvalidArgumentException("Minimum withdrawal amount is ₹{$minAmount}.");
        }

        if ($amount > (float) $wallet->available_balance) {
            throw new \InvalidArgumentException("Requested amount exceeds available balance of ₹{$wallet->available_balance}.");
        }

        $method = $args['payoutMethod'] ?? ($args['payout_method'] ?? $affiliate->payout_method);

        if (! $method) {
            throw new \InvalidArgumentException('Please select a payout method (upi or bank_transfer).');
        }

        if ($method === 'upi') {
            $upiId = $args['upiId'] ?? ($args['upi_id'] ?? ($affiliate->payout_details['upi_id'] ?? null));
            if (! $upiId) {
                throw new \InvalidArgumentException('UPI ID is required.');
            }
            $details = ['upi_id' => $upiId];
        } else {
            $acc = $args['accountNumber'] ?? ($args['account_number'] ?? ($affiliate->payout_details['account_number'] ?? null));
            $ifsc = $args['ifscCode'] ?? ($args['ifsc_code'] ?? ($affiliate->payout_details['ifsc_code'] ?? null));
            $holder = $args['accountHolder'] ?? ($args['account_holder'] ?? ($affiliate->payout_details['account_holder'] ?? null));
            $bank = $args['bankName'] ?? ($args['bank_name'] ?? ($affiliate->payout_details['bank_name'] ?? null));

            if (! $acc || ! $ifsc || ! $holder) {
                throw new \InvalidArgumentException('Account number, IFSC code, and account holder name are required.');
            }

            $details = [
                'account_number' => $acc,
                'ifsc_code' => $ifsc,
                'account_holder' => $holder,
                'bank_name' => $bank,
            ];
        }

        $payout = $this->walletTransactionService->requestPayout($affiliate, $amount, $method, $details);
        $freshWallet = $this->walletRepository->getByAffiliateId($affiliate->id);

        return [
            'success' => true,
            'message' => trans('affiliate::app.shop.account.request-success'),
            'payoutId' => $payout->id,
            'payout_id' => $payout->id,
            'payoutNumber' => $payout->payout_number,
            'payout_number' => $payout->payout_number,
            'amount' => (float) $payout->amount,
            'amountFormatted' => '₹'.number_format((float) $payout->amount, 2),
            'amount_formatted' => '₹'.number_format((float) $payout->amount, 2),
            'method' => $payout->payout_method,
            'status' => $payout->status,
            'availableBalance' => (float) $freshWallet->available_balance,
            'available_balance' => (float) $freshWallet->available_balance,
            'lockedBalance' => (float) $freshWallet->locked_balance,
            'locked_balance' => (float) $freshWallet->locked_balance,
        ];
    }

    /**
     * Record referral click on affiliate code.
     */
    public function trackReferral(string $code): array
    {
        $affiliate = $this->affiliateRepository->findOneByField('referral_code', $code);

        if (! $affiliate || $affiliate->status !== 'active') {
            throw new \Exception('Invalid or inactive referral code.');
        }

        $this->affiliateRepository->incrementClicks($affiliate->id);

        return [
            'success' => true,
            'message' => 'Referral click recorded.',
            'referralCode' => $affiliate->referral_code,
            'referral_code' => $affiliate->referral_code,
            'affiliateId' => $affiliate->id,
            'affiliate_id' => $affiliate->id,
        ];
    }
}
