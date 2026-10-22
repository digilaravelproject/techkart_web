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
    
    $commRateText = '5%';
    $estAmount = round(($product->price * 5) / 100);
    
    if ($prodRule && ! $prodRule->is_excluded) {
        if ($prodRule->commission_type === 'percentage') {
            $commRateText = $prodRule->commission_value . '%';
            $estAmount = round(($product->price * $prodRule->commission_value) / 100);
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
            $estAmount = round(($product->price * $globalVal) / 100);
        }
    }

    $whatsAppMsg = rawurlencode("Hey! Check out the {$product->name} on BEENZO. It looks amazing: {$shareUrl}");
@endphp

@if (! $isExcluded)
    <div class="mt-4 rounded-xl border border-[#c2955e]/30 bg-gradient-to-r from-[#fdfbf7] to-[#f7f0e6] p-4 text-gray-800 shadow-sm transition hover:border-[#c2955e] dark:from-neutral-900 dark:to-neutral-800 dark:border-[#c2955e]/40 dark:text-neutral-100">
        <div class="flex items-center gap-2.5">
            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-[#c2955e]/20 text-[#a0743e] dark:text-[#d4ab74]">
                🎁
            </span>
            <div>
                <p class="text-sm font-semibold text-gray-900 dark:text-white">
                    Share & Earn up to <span class="text-[#c2955e] font-bold">₹{{ $estAmount }}</span> ({{ $commRateText }})
                </p>
                <p class="text-xs text-gray-500 dark:text-neutral-400">
                    Earn cash commission in your wallet whenever someone purchases with your link.
                </p>
            </div>
        </div>

        @if ($isCustomer)
            <div class="mt-3 flex flex-wrap items-center gap-2.5">
                <button
                    type="button"
                    onclick="copyAffiliateLink('{{ $shareUrl }}')"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-[#c2955e] px-3.5 py-2 text-xs font-semibold text-white shadow transition hover:bg-[#a0743e] active:scale-95"
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
                    class="inline-flex items-center gap-1.5 rounded-lg bg-[#25D366] px-3.5 py-2 text-xs font-semibold text-white shadow transition hover:bg-[#1ebd59] active:scale-95"
                >
                    <svg class="h-4 w-4 fill-current" viewBox="0 0 24 24">
                        <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.312.045-.694.075-2.227-.557-1.848-.763-3.036-2.656-3.128-2.78-.093-.123-.746-.993-.746-1.894 0-.901.472-1.343.64-1.527.169-.185.369-.231.492-.231.123 0 .247.001.354.006.113.005.263-.043.411.314.154.37.525 1.28.57 1.373.047.093.078.201.016.324-.062.123-.093.2-.185.308-.092.109-.194.244-.277.328-.093.093-.19.195-.082.38.108.185.48 1.007 1.031 1.497.712.633 1.312.83 1.498.923.185.092.293.077.401-.047.108-.123.462-.538.585-.723.123-.185.247-.154.416-.092.169.062 1.077.508 1.262.6.185.092.308.138.354.215.046.077.046.446-.098.851z"/>
                    </svg>
                    WhatsApp Share
                </a>

                <span id="copySuccessToast" class="hidden text-xs font-semibold text-emerald-600 dark:text-emerald-400 animate-fade">
                    ✓ Link copied!
                </span>
            </div>
        @else
            <div class="mt-3">
                <a
                    href="{{ route('shop.customer.session.create') }}"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-[#c2955e] px-4 py-2 text-xs font-semibold text-white shadow transition hover:bg-[#a0743e]"
                >
                    Sign In to Earn ₹{{ $estAmount }} Commission
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
