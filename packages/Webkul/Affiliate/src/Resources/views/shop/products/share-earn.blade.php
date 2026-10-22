@php
    $isCustomer = auth()->guard('customer')->check();
    $customer = auth()->guard('customer')->user();
    $affiliate = $isCustomer ? app(\Webkul\Affiliate\Repositories\AffiliateRepository::class)->getOrCreateForCustomer($customer->id) : null;
    $refCode = $affiliate ? $affiliate->referral_code : '';
    
    $productUrl = route('shop.product_or_category.index', $product->url_key);
    $shareUrl = $refCode ? ($productUrl . (str_contains($productUrl, '?') ? '&' : '?') . 'ref=' . $refCode) : $productUrl;
    
    $ruleRepo = app(\Webkul\Affiliate\Repositories\CommissionRuleRepository::class);
    $prodRule = $ruleRepo->getRule('product', $product->id);
    $isExcluded = $prodRule && $prodRule->is_excluded;

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
    
    if ($prodRule && ! $prodRule->is_excluded) {
        if ($prodRule->commission_type === 'percentage') {
            $commRateText = $prodRule->commission_value . '%';
            $estAmount = round(($actualPrice * $prodRule->commission_value) / 100);
        } else {
            $commRateText = '₹' . number_format($prodRule->commission_value, 0);
            $estAmount = round($prodRule->commission_value);
        }
    } else {
        $globalType = core()->getConfigData('affiliate.general.default_commission_type') ?: 'percentage';
        $globalVal = (float) (core()->getConfigData('affiliate.general.default_commission_value') ?? 5);
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
    <div class="mt-6 w-full max-w-[470px] rounded-2xl border border-[#c2955e]/30 bg-gradient-to-br from-[#FAF7F2] via-white to-[#F6EFE6] p-4 text-gray-800 shadow-xs transition hover:border-[#c2955e]/60 dark:from-neutral-900 dark:via-neutral-900 dark:to-neutral-800 dark:border-[#c2955e]/40 dark:text-neutral-100">
        <div class="flex items-start gap-3">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#c2955e]/15 text-lg text-[#a0743e] dark:text-[#d4ab74] shadow-xs">
                🎁
            </span>
            <div class="flex-1">
                <div class="flex flex-wrap items-center justify-between gap-1">
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">
                        @if ($estAmount > 0)
                            Share & Earn up to <span class="text-[#a0743e] dark:text-[#d4ab74] font-bold">₹{{ number_format($estAmount, 0) }}</span> <span class="text-xs text-gray-500 font-normal">({{ $commRateText }})</span>
                        @else
                            Share & Earn <span class="text-[#a0743e] dark:text-[#d4ab74] font-bold">{{ $commRateText }}</span> Commission
                        @endif
                    </p>
                </div>
                <p class="mt-0.5 text-xs text-gray-500 dark:text-neutral-400">
                    Earn instant cash in your wallet whenever someone purchases with your referral link.
                </p>
            </div>
        </div>

        @if ($isCustomer)
            <div class="mt-3.5 flex flex-wrap items-center gap-2.5">
                <button
                    type="button"
                    onclick="copyAffiliateLink('{{ $shareUrl }}')"
                    class="inline-flex flex-1 min-w-[160px] items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-[#c2955e] to-[#a0743e] px-4 py-2.5 text-xs font-semibold text-white shadow-sm transition hover:from-[#b58750] hover:to-[#916634] active:scale-95 cursor-pointer"
                >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                    </svg>
                    Copy Referral Link
                </button>

                <a
                    href="https://api.whatsapp.com/send?text={{ $whatsAppMsg }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-[#25D366] px-4 py-2.5 text-xs font-semibold text-white shadow-sm transition hover:bg-[#20ba5a] active:scale-95"
                >
                    <svg class="h-4 w-4 fill-current" viewBox="0 0 24 24">
                        <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.312.045-.694.075-2.227-.557-1.848-.763-3.036-2.656-3.128-2.78-.093-.123-.746-.993-.746-1.894 0-.901.472-1.343.64-1.527.169-.185.369-.231.492-.231.123 0 .247.001.354.006.113.005.263-.043.411.314.154.37.525 1.28.57 1.373.047.093.078.201.016.324-.062.123-.093.2-.185.308-.092.109-.194.244-.277.328-.093.093-.19.195-.082.38.108.185.48 1.007 1.031 1.497.712.633 1.312.83 1.498.923.185.092.293.077.401-.047.108-.123.462-.538.585-.723.123-.185.247-.154.416-.092.169.062 1.077.508 1.262.6.185.092.308.138.354.215.046.077.046.446-.098.851z"/>
                    </svg>
                    WhatsApp
                </a>

                <span id="copySuccessToast" class="hidden w-full text-center text-xs font-semibold text-emerald-600 dark:text-emerald-400">
                    ✓ Link copied to clipboard!
                </span>
            </div>
        @else
            <div class="mt-3.5">
                <a
                    href="{{ route('shop.customer.session.create') }}"
                    class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-[#c2955e] to-[#a0743e] px-4 py-2.5 text-xs font-semibold text-white shadow-sm transition hover:from-[#b58750] hover:to-[#916634] active:scale-95"
                >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                    </svg>
                    Sign In to Earn {{ $estAmount > 0 ? '₹' . number_format($estAmount, 0) : $commRateText }} Commission
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
