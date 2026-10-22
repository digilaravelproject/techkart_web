<x-shop::layouts.account>
    <!-- Page Title -->
    <x-slot:title>
        @lang('affiliate::app.shop.account.referral-wallet')
    </x-slot>

    <div class="max-md:hidden">
        <x-shop::layouts.account.navigation />
    </div>

    <div class="mx-4 flex-auto max-md:mx-6 max-sm:mx-4">
        <!-- Top Heading -->
        <div class="mb-6 flex items-center justify-between max-sm:mb-4">
            <div class="flex items-center">
                <a class="grid md:hidden mr-2" href="{{ route('shop.customers.account.profile.index') }}">
                    <span class="icon-arrow-left text-2xl"></span>
                </a>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white max-sm:text-lg">
                        @lang('affiliate::app.shop.account.referral-wallet')
                    </h2>
                    <p class="text-xs text-gray-500 dark:text-neutral-400">
                        Share products, earn real cash commissions, and withdraw directly to your Bank or UPI.
                    </p>
                </div>
            </div>

            <button
                type="button"
                onclick="document.getElementById('payoutModal').classList.remove('hidden')"
                class="rounded-xl bg-gradient-to-r from-[#c2955e] to-[#a0743e] px-4 py-2 text-sm font-semibold text-white shadow-md transition hover:opacity-95 active:scale-95 flex items-center gap-1.5"
            >
                <span class="icon-wallet text-lg"></span>
                @lang('affiliate::app.shop.account.request-withdrawal')
            </button>
        </div>

        <!-- 1. Wallet Bento-Cards -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-6">
            <!-- Available Balance -->
            <div class="relative overflow-hidden rounded-2xl border border-[#c2955e]/30 bg-gradient-to-br from-[#fcf9f5] to-[#f4ebe0] p-5 shadow-sm dark:from-neutral-900 dark:to-neutral-800 dark:border-[#c2955e]/40 flex flex-col justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-[#a0743e] dark:text-[#d4ab74]">
                        @lang('affiliate::app.shop.account.available-balance')
                    </p>
                    <p class="mt-2 text-3xl font-black text-gray-900 dark:text-white">
                        ₹{{ number_format($wallet->available_balance, 2) }}
                    </p>
                    <p class="mt-2 text-xs text-gray-500 dark:text-neutral-400">
                        Ready for withdrawal to Bank/UPI
                    </p>
                </div>
                <div class="mt-3 pt-3 border-t border-[#c2955e]/20 flex items-center justify-between">
                    <span class="text-[11px] text-gray-500 dark:text-neutral-400">Min: ₹{{ number_format($minWithdrawal, 0) }}</span>
                    <button
                        type="button"
                        onclick="document.getElementById('payoutModal').classList.remove('hidden')"
                        class="text-xs font-bold text-[#c2955e] dark:text-[#d4ab74] hover:underline"
                    >
                        Request Payout &rarr;
                    </button>
                </div>
            </div>

            <!-- Pending Clearance -->
            <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-neutral-800 dark:bg-neutral-900">
                <p class="text-xs font-semibold uppercase tracking-wider text-amber-600 dark:text-amber-400">
                    @lang('affiliate::app.shop.account.pending-balance')
                </p>
                <p class="mt-2 text-3xl font-black text-gray-800 dark:text-neutral-200">
                    ₹{{ number_format($wallet->pending_balance, 2) }}
                </p>
                <p class="mt-2 text-xs text-gray-500 dark:text-neutral-400">
                    Releases after return period closes
                </p>
            </div>

            <!-- In Payout Process -->
            <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-neutral-800 dark:bg-neutral-900">
                <p class="text-xs font-semibold uppercase tracking-wider text-blue-600 dark:text-blue-400">
                    @lang('affiliate::app.shop.account.locked-balance')
                </p>
                <p class="mt-2 text-3xl font-black text-gray-800 dark:text-neutral-200">
                    ₹{{ number_format($wallet->locked_balance, 2) }}
                </p>
                <p class="mt-2 text-xs text-gray-500 dark:text-neutral-400">
                    Active withdrawal requests in transit
                </p>
            </div>

            <!-- Lifetime Earned -->
            <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-neutral-800 dark:bg-neutral-900">
                <p class="text-xs font-semibold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">
                    @lang('affiliate::app.shop.account.lifetime-earned')
                </p>
                <p class="mt-2 text-3xl font-black text-gray-800 dark:text-neutral-200">
                    ₹{{ number_format($wallet->total_earned, 2) }}
                </p>
                <p class="mt-2 text-xs text-gray-500 dark:text-neutral-400">
                    Total historical commissions earned
                </p>
            </div>
        </div>

        <!-- 2. Universal Referral Link & Share Suite -->
        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-neutral-800 dark:bg-neutral-900 mb-6">
            <h3 class="text-base font-bold text-gray-900 dark:text-white mb-2">
                @lang('affiliate::app.shop.account.your-referral-link')
            </h3>
            <p class="text-xs text-gray-500 dark:text-neutral-400 mb-3">
                Share this link or your unique referral code <span class="rounded bg-[#c2955e]/10 px-2 py-0.5 font-mono font-bold text-[#a0743e] dark:text-[#d4ab74]">{{ $affiliate->referral_code }}</span>. Any orders placed will automatically credit commission to your wallet.
            </p>

            <div class="flex flex-wrap items-center gap-2">
                <div class="flex-1 min-w-[260px] flex items-center rounded-xl border border-gray-200 dark:border-neutral-700 bg-gray-50 dark:bg-neutral-800 px-3 py-2 text-xs font-mono text-gray-800 dark:text-neutral-200 select-all">
                    {{ $referralLink }}
                </div>
                <button
                    type="button"
                    onclick="copyToClipboard('{{ $referralLink }}')"
                    class="rounded-xl bg-[#c2955e] px-4 py-2 text-xs font-bold text-white shadow transition hover:bg-[#a0743e] active:scale-95"
                >
                    @lang('affiliate::app.shop.account.copy-link')
                </button>
                <a
                    href="https://api.whatsapp.com/send?text={{ rawurlencode('Shop luxury fashion on BEENZO: ' . $referralLink) }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="inline-flex items-center gap-1.5 rounded-xl bg-[#25D366] px-4 py-2 text-xs font-bold text-white shadow transition hover:bg-[#1ebd59] active:scale-95"
                >
                    @lang('affiliate::app.shop.account.share-whatsapp')
                </a>
            </div>

            <!-- Custom Link Generator -->
            <div class="mt-4 pt-4 border-t border-gray-100 dark:border-neutral-800">
                <p class="text-xs font-semibold text-gray-700 dark:text-neutral-300 mb-2">
                    @lang('affiliate::app.shop.account.custom-link-gen')
                </p>
                <div class="flex flex-wrap items-center gap-2">
                    <input
                        type="url"
                        id="custom_target_url"
                        placeholder="@lang('affiliate::app.shop.account.paste-product-url')"
                        class="flex-1 min-w-[260px] rounded-xl border border-gray-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 px-3 py-2 text-xs text-gray-800 dark:text-neutral-200 focus:outline-none focus:ring-1 focus:ring-[#c2955e]"
                    />
                    <button
                        type="button"
                        onclick="generateTrackedLink()"
                        class="rounded-xl border border-[#c2955e] px-4 py-2 text-xs font-bold text-[#c2955e] transition hover:bg-[#c2955e] hover:text-white"
                    >
                        @lang('affiliate::app.shop.account.generate-link')
                    </button>
                </div>
                <div id="custom_link_result" class="hidden mt-2 flex items-center gap-2">
                    <span id="custom_link_text" class="text-xs font-mono text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/40 p-2 rounded-lg break-all"></span>
                    <button type="button" onclick="copyCustomResult()" class="text-xs font-semibold underline text-[#c2955e]">Copy</button>
                </div>
            </div>
        </div>

        <!-- 3. Performance Analytics -->
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-4 mb-6">
            <div class="rounded-xl border border-gray-100 bg-white p-4 shadow-sm dark:border-neutral-800 dark:bg-neutral-900 text-center">
                <p class="text-xs text-gray-500 dark:text-neutral-400">@lang('affiliate::app.shop.account.total-clicks')</p>
                <p class="text-xl font-bold text-gray-900 dark:text-white mt-1">{{ $affiliate->total_clicks }}</p>
            </div>
            <div class="rounded-xl border border-gray-100 bg-white p-4 shadow-sm dark:border-neutral-800 dark:bg-neutral-900 text-center">
                <p class="text-xs text-gray-500 dark:text-neutral-400">@lang('affiliate::app.shop.account.total-orders')</p>
                <p class="text-xl font-bold text-gray-900 dark:text-white mt-1">{{ $affiliate->total_orders }}</p>
            </div>
            <div class="rounded-xl border border-gray-100 bg-white p-4 shadow-sm dark:border-neutral-800 dark:bg-neutral-900 text-center">
                <p class="text-xs text-gray-500 dark:text-neutral-400">@lang('affiliate::app.shop.account.conversion-rate')</p>
                <p class="text-xl font-bold text-gray-900 dark:text-white mt-1">{{ $conversionRate }}%</p>
            </div>
            <div class="rounded-xl border border-gray-100 bg-white p-4 shadow-sm dark:border-neutral-800 dark:bg-neutral-900 text-center">
                <p class="text-xs text-gray-500 dark:text-neutral-400">@lang('affiliate::app.shop.account.total-withdrawn')</p>
                <p class="text-xl font-bold text-gray-900 dark:text-white mt-1">₹{{ number_format($wallet->total_withdrawn, 2) }}</p>
            </div>
        </div>

        <!-- 4. Wallet Passbook & Transaction Ledger -->
        <div class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-neutral-800 dark:bg-neutral-900 overflow-hidden">
            <div class="p-4 border-b border-gray-100 dark:border-neutral-800 flex items-center justify-between">
                <h3 class="text-base font-bold text-gray-900 dark:text-white">
                    @lang('affiliate::app.shop.account.transactions-title')
                </h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-gray-600 dark:text-neutral-300">
                    <thead class="bg-gray-50 dark:bg-neutral-800 text-gray-700 dark:text-neutral-200 uppercase font-semibold">
                        <tr>
                            <th class="px-4 py-3">@lang('affiliate::app.shop.account.date')</th>
                            <th class="px-4 py-3">@lang('affiliate::app.shop.account.txn-number')</th>
                            <th class="px-4 py-3">@lang('affiliate::app.shop.account.title')</th>
                            <th class="px-4 py-3">@lang('affiliate::app.shop.account.type')</th>
                            <th class="px-4 py-3">@lang('affiliate::app.shop.account.amount')</th>
                            <th class="px-4 py-3">@lang('affiliate::app.shop.account.balance-after')</th>
                            <th class="px-4 py-3">@lang('affiliate::app.shop.account.status')</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-neutral-800">
                        @forelse ($transactions as $txn)
                            <tr class="hover:bg-gray-50 dark:hover:bg-neutral-800/50 transition">
                                <td class="px-4 py-3">{{ $txn->created_at?->format('d M Y, h:i A') }}</td>
                                <td class="px-4 py-3 font-mono text-[11px] font-bold">{{ $txn->transaction_number }}</td>
                                <td class="px-4 py-3">
                                    <div class="font-medium text-gray-800 dark:text-neutral-200">{{ $txn->title }}</div>
                                    @if ($txn->description)
                                        <div class="text-[11px] text-gray-400">{{ $txn->description }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 font-semibold uppercase">
                                    <span class="{{ $txn->type === 'credit' ? 'text-emerald-600' : 'text-red-500' }}">
                                        {{ $txn->type }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 font-bold">
                                    <span class="{{ $txn->type === 'credit' ? 'text-emerald-600' : 'text-red-500' }}">
                                        {{ $txn->type === 'credit' ? '+' : '-' }}₹{{ number_format($txn->amount, 2) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 font-medium">₹{{ number_format($txn->balance_after, 2) }}</td>
                                <td class="px-4 py-3">
                                    @php
                                        $statusBadges = [
                                            'cleared'   => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-300',
                                            'pending'   => 'bg-amber-100 text-amber-800 dark:bg-amber-900/50 dark:text-amber-300',
                                            'locked'    => 'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300',
                                            'completed' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/50 dark:text-indigo-300',
                                            'reversed'  => 'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300',
                                        ];
                                    @endphp
                                    <span class="rounded-full px-2.5 py-0.5 text-[10px] font-bold uppercase {{ $statusBadges[$txn->status] ?? 'bg-gray-100 text-gray-700' }}">
                                        {{ $txn->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-gray-400">
                                    @lang('affiliate::app.shop.account.no-transactions')
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-gray-100 dark:border-neutral-800">
                {{ $transactions->links() }}
            </div>
        </div>
    </div>

    <!-- 5. Withdrawal Request Modal -->
    <div id="payoutModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-60 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-neutral-900 rounded-2xl max-w-md w-full p-6 shadow-2xl relative border border-gray-200 dark:border-neutral-800">
            <div class="flex items-center justify-between pb-3 border-b border-gray-100 dark:border-neutral-800">
                <h3 class="text-base font-bold text-gray-900 dark:text-white">
                    @lang('affiliate::app.shop.account.modal-title')
                </h3>
                <button
                    type="button"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 text-lg"
                    onclick="document.getElementById('payoutModal').classList.add('hidden')"
                >
                    ✕
                </button>
            </div>

            @if ($wallet->available_balance < $minWithdrawal)
                <div class="mt-3 rounded-xl border border-amber-200 bg-amber-50 p-3 text-xs text-amber-800 dark:border-amber-900/50 dark:bg-amber-950/40 dark:text-amber-300">
                    <p class="font-bold">Minimum Withdrawal Threshold: ₹{{ number_format($minWithdrawal, 2) }}</p>
                    <p class="mt-1 text-[11px]">Your current available balance is ₹{{ number_format($wallet->available_balance, 2) }}. You need at least ₹{{ number_format($minWithdrawal, 2) }} to request a payout.</p>
                </div>
            @endif

            <form action="{{ route('shop.customers.account.affiliate.request_payout') }}" method="POST" class="mt-4 space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-neutral-300 mb-1">
                        @lang('affiliate::app.shop.account.enter-amount')
                    </label>
                    <input
                        type="number"
                        name="amount"
                        min="{{ $minWithdrawal }}"
                        max="{{ $wallet->available_balance }}"
                        step="0.01"
                        value="{{ $wallet->available_balance }}"
                        required
                        class="w-full rounded-xl border border-gray-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 px-3 py-2 text-sm text-gray-900 dark:text-white font-bold focus:outline-none focus:ring-1 focus:ring-[#c2955e]"
                    />
                    <p class="text-[11px] text-gray-500 mt-1">
                        @lang('affiliate::app.shop.account.min-threshold-info', ['amount' => number_format($minWithdrawal, 2)])
                    </p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-neutral-300 mb-1">
                        @lang('affiliate::app.shop.account.select-method')
                    </label>
                    <select
                        name="payout_method"
                        id="user_payout_method"
                        required
                        onchange="toggleMethodInputs(this.value)"
                        class="w-full rounded-xl border border-gray-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 px-3 py-2 text-xs text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-[#c2955e]"
                    >
                        <option value="upi">UPI Transfer (Instant)</option>
                        <option value="bank_transfer">Direct Bank Transfer (NEFT/IMPS)</option>
                    </select>
                </div>

                <!-- UPI Input -->
                <div id="upi_block" class="space-y-2">
                    <label class="block text-xs font-semibold text-gray-700 dark:text-neutral-300">
                        @lang('affiliate::app.shop.account.upi')
                    </label>
                    <input
                        type="text"
                        name="upi_id"
                        id="upi_id"
                        placeholder="@lang('affiliate::app.shop.account.upi-placeholder')"
                        class="w-full rounded-xl border border-gray-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 px-3 py-2 text-xs text-gray-900 dark:text-white font-mono focus:outline-none focus:ring-1 focus:ring-[#c2955e]"
                    />
                </div>

                <!-- Bank Input -->
                <div id="bank_block" class="hidden space-y-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-neutral-300 mb-1">
                            @lang('affiliate::app.shop.account.account-holder')
                        </label>
                        <input
                            type="text"
                            name="account_holder"
                            placeholder="Name as per Bank Account"
                            class="w-full rounded-xl border border-gray-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 px-3 py-2 text-xs text-gray-900 dark:text-white"
                        />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-neutral-300 mb-1">
                            @lang('affiliate::app.shop.account.account-number')
                        </label>
                        <input
                            type="text"
                            name="account_number"
                            placeholder="Bank Account Number"
                            class="w-full rounded-xl border border-gray-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 px-3 py-2 text-xs text-gray-900 dark:text-white font-mono"
                        />
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-neutral-300 mb-1">
                                @lang('affiliate::app.shop.account.bank-name')
                            </label>
                            <input
                                type="text"
                                name="bank_name"
                                placeholder="e.g. HDFC Bank"
                                class="w-full rounded-xl border border-gray-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 px-3 py-2 text-xs text-gray-900 dark:text-white"
                            />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-neutral-300 mb-1">
                                @lang('affiliate::app.shop.account.ifsc-code')
                            </label>
                            <input
                                type="text"
                                name="ifsc_code"
                                placeholder="e.g. HDFC0001234"
                                class="w-full rounded-xl border border-gray-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 px-3 py-2 text-xs text-gray-900 dark:text-white font-mono uppercase"
                            />
                        </div>
                    </div>
                </div>

                <div class="flex items-start gap-2 pt-2">
                    <input
                        type="checkbox"
                        id="terms_agree"
                        required
                        class="mt-0.5 rounded border-gray-300 text-[#c2955e] focus:ring-[#c2955e]"
                    />
                    <label for="terms_agree" class="text-[11px] text-gray-500 dark:text-neutral-400">
                        @lang('affiliate::app.shop.account.terms-agree')
                    </label>
                </div>

                <div class="flex justify-end gap-2 pt-4 border-t border-gray-100 dark:border-neutral-800">
                    <button
                        type="button"
                        class="rounded-xl border border-gray-300 dark:border-neutral-700 px-4 py-2 text-xs font-semibold text-gray-700 dark:text-neutral-300 hover:bg-gray-50"
                        onclick="document.getElementById('payoutModal').classList.add('hidden')"
                    >
                        Cancel
                    </button>
                    <button
                        type="submit"
                        @if ($wallet->available_balance < $minWithdrawal) disabled title="Minimum balance required: ₹{{ number_format($minWithdrawal, 2) }}" @endif
                        class="rounded-xl bg-[#c2955e] px-4 py-2 text-xs font-bold text-white shadow transition hover:bg-[#a0743e] active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        @lang('affiliate::app.shop.account.submit-request')
                    </button>
                </div>
            </form>
        </div>
    </div>

    @pushOnce('scripts')
        <script>
            function copyToClipboard(text) {
                navigator.clipboard.writeText(text).then(function() {
                    alert("@lang('affiliate::app.shop.account.link-copied')");
                }).catch(function() {
                    prompt("Referral Link:", text);
                });
            }

            function toggleMethodInputs(val) {
                var upiBlock = document.getElementById('upi_block');
                var bankBlock = document.getElementById('bank_block');

                if (val === 'upi') {
                    upiBlock.classList.remove('hidden');
                    bankBlock.classList.add('hidden');
                } else {
                    upiBlock.classList.add('hidden');
                    bankBlock.classList.remove('hidden');
                }
            }

            function generateTrackedLink() {
                var url = document.getElementById('custom_target_url').value;
                if (!url) {
                    alert('Please enter a valid page URL');
                    return;
                }

                fetch("{{ route('shop.customers.account.affiliate.generate_link') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ url: url })
                })
                .then(function(res) { return res.json(); })
                .then(function(data) {
                    if (data.success) {
                        var resBox = document.getElementById('custom_link_result');
                        var resText = document.getElementById('custom_link_text');
                        resText.innerText = data.generated_url;
                        resBox.classList.remove('hidden');
                    }
                })
                .catch(function(err) {
                    alert('Failed to generate link.');
                });
            }

            function copyCustomResult() {
                var text = document.getElementById('custom_link_text').innerText;
                copyToClipboard(text);
            }
        </script>
    @endPushOnce
</x-shop::layouts.account>
