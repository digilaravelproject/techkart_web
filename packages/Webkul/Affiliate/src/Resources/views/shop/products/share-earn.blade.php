@php
    $isCustomer = auth()->guard('customer')->check();
    $customer = auth()->guard('customer')->user();
    $affiliate = $isCustomer ? app(\Webkul\Affiliate\Repositories\AffiliateRepository::class)->getOrCreateForCustomer($customer->id) : null;
    $refCode = $affiliate ? $affiliate->referral_code : '';
    
    $productUrl = route('shop.product_or_category.index', $product->url_key);
    $shareUrl = $refCode ? ($productUrl . (str_contains($productUrl, '?') ? '&' : '?') . 'ref=' . $refCode) : $productUrl;
    
    $ruleRepo = app(\Webkul\Affiliate\Repositories\CommissionRuleRepository::class);
    $prodRule = $ruleRepo->getRule('product', $product->id);

    $catRule = null;
    if (! $prodRule) {
        $categoryIds = $product->categories()->pluck('categories.id')->toArray();
        if (! empty($categoryIds)) {
            $catRule = $ruleRepo->findWhere([
                'entity_type' => 'category',
                'status' => 1,
            ])->whereIn('entity_id', $categoryIds)->sortByDesc('commission_value')->first();
        }
    }

    $activeRule = $prodRule ?: $catRule;
    $isExcluded = $activeRule && $activeRule->is_excluded;

    $actualPrice = 0;
    if (method_exists($product->getTypeInstance(), 'getMinimalPrice')) {
        $actualPrice = (float) $product->getTypeInstance()->getMinimalPrice();
    }
    if (! $actualPrice && method_exists($product->getTypeInstance(), 'getFinalPrice')) {
        $actualPrice = (float) $product->getTypeInstance()->getFinalPrice();
    }
    if (! $actualPrice) {
        $actualPrice = (float) ($product->price ?? 0);
    }
    
    $commRateText = '5%';
    $estAmount = 0;
    
    if ($activeRule && ! $activeRule->is_excluded) {
        if ($activeRule->commission_type === 'percentage') {
            $commRateText = (float) $activeRule->commission_value . '%';
            $estAmount = round(($actualPrice * (float) $activeRule->commission_value) / 100);
        } else {
            $commRateText = '₹' . number_format($activeRule->commission_value, 0);
            $estAmount = round($activeRule->commission_value);
        }
    } else {
        $globalType = core()->getConfigData('affiliate.general.default_commission_type') ?: 'percentage';
        $configVal = core()->getConfigData('affiliate.general.default_commission_value');
        $globalVal = ($configVal !== null && $configVal !== '') ? (float) $configVal : 5.0;

        if ($globalType === 'fixed') {
            $commRateText = '₹' . number_format($globalVal, 0);
            $estAmount = round($globalVal);
        } else {
            $commRateText = $globalVal . '%';
            $estAmount = round(($actualPrice * $globalVal) / 100);
        }
    }

    $whatsAppMsg = rawurlencode("Hey! Check out {$product->name} on BEENZO: {$shareUrl}");
@endphp

@if (! $isExcluded)
    <div
        class="mt-6 w-full max-w-[470px] rounded-2xl p-4 shadow-sm"
        style="border: 1px solid rgba(194, 149, 94, 0.35); background: linear-gradient(135deg, #FAF7F2 0%, #FFFFFF 50%, #F6EFE6 100%);"
    >
        <div class="flex items-start gap-3">
            <span
                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl text-lg shadow-xs"
                style="background-color: rgba(194, 149, 94, 0.15); color: #a0743e;"
            >
                🎁
            </span>
            <div class="flex-1">
                <p class="text-sm font-bold" style="color: #1f2937;">
                    @if ($estAmount > 0)
                        Share & Earn up to <span style="color: #a0743e; font-weight: 700;">₹{{ number_format($estAmount, 0) }}</span> <span class="text-xs font-normal" style="color: #6b7280;">({{ $commRateText }})</span>
                    @else
                        Share & Earn <span style="color: #a0743e; font-weight: 700;">{{ $commRateText }}</span> Commission
                    @endif
                </p>
                <p class="mt-0.5 text-xs" style="color: #6b7280;">
                    Earn instant cash in your wallet whenever someone purchases with your referral link.
                </p>
            </div>
        </div>

        @if ($isCustomer)
            <div class="mt-3.5 flex flex-wrap items-center gap-2.5">
                <button
                    type="button"
                    onclick="copyAffiliateLink('{{ $shareUrl }}')"
                    style="background: linear-gradient(135deg, #c2955e 0%, #a0743e 100%); color: #ffffff !important; border: none; cursor: pointer;"
                    class="inline-flex flex-1 min-w-[170px] items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-xs font-semibold shadow-sm transition-all hover:opacity-95 active:scale-95"
                >
                    <svg style="width: 16px; height: 16px; fill: none; stroke: #ffffff; stroke-width: 2;" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                    </svg>
                    <span style="color: #ffffff !important; font-weight: 600;">Copy Referral Link</span>
                </button>

                <a
                    href="https://api.whatsapp.com/send?text={{ $whatsAppMsg }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    style="background-color: #25D366; color: #ffffff !important; text-decoration: none;"
                    class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-xs font-semibold shadow-sm transition-all hover:opacity-95 active:scale-95"
                >
                    <svg style="width: 16px; height: 16px; fill: #ffffff;" viewBox="0 0 24 24">
                        <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.312.045-.694.075-2.227-.557-1.848-.763-3.036-2.656-3.128-2.78-.093-.123-.746-.993-.746-1.894 0-.901.472-1.343.64-1.527.169-.185.369-.231.492-.231.123 0 .247.001.354.006.113.005.263-.043.411.314.154.37.525 1.28.57 1.373.047.093.078.201.016.324-.062.123-.093.2-.185.308-.092.109-.194.244-.277.328-.093.093-.19.195-.082.38.108.185.48 1.007 1.031 1.497.712.633 1.312.83 1.498.923.185.092.293.077.401-.047.108-.123.462-.538.585-.723.123-.185.247-.154.416-.092.169.062 1.077.508 1.262.6.185.092.308.138.354.215.046.077.046.446-.098.851z"/>
                    </svg>
                    <span style="color: #ffffff !important; font-weight: 600;">WhatsApp</span>
                </a>

                <span id="copySuccessToast" class="hidden w-full text-center text-xs font-semibold" style="color: #059669;">
                    ✓ Link copied to clipboard!
                </span>
            </div>
        @else
            <div class="mt-3.5">
                <a
                    href="{{ route('shop.customer.session.create') }}"
                    style="background: linear-gradient(135deg, #c2955e 0%, #a0743e 100%); color: #ffffff !important; text-decoration: none;"
                    class="inline-flex w-full items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-xs font-semibold shadow-sm transition-all hover:opacity-95 active:scale-95"
                >
                    <svg style="width: 16px; height: 16px; fill: none; stroke: #ffffff; stroke-width: 2;" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                    </svg>
                    <span style="color: #ffffff !important; font-weight: 600;">Sign In to Earn {{ $estAmount > 0 ? '₹' . number_format($estAmount, 0) : $commRateText }} Commission</span>
                </a>
            </div>
        @endif
    </div>

    @pushOnce('scripts')
        <script>
            window.copyAffiliateLink = function(url) {
                if (navigator.clipboard && window.isSecureContext) {
                    navigator.clipboard.writeText(url).then(function() {
                        var toast = document.getElementById('copySuccessToast');
                        if (toast) {
                            toast.classList.remove('hidden');
                            setTimeout(function() {
                                toast.classList.add('hidden');
                            }, 3000);
                        }
                    }).catch(function() {
                        prompt("Copy your referral link:", url);
                    });
                } else {
                    prompt("Copy your referral link:", url);
                }
            };
        </script>
    @endPushOnce
@endif
