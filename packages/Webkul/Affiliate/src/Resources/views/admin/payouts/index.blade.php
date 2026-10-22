<x-admin::layouts>
    <x-slot:title>
        @lang('affiliate::app.admin.payouts.title')
    </x-slot>

    <div class="mt-3 flex items-center justify-between gap-4 max-sm:flex-wrap">
        <div>
            <div class="flex items-center gap-2.5">
                <p class="text-xl font-bold text-gray-800 dark:text-white">
                    @lang('affiliate::app.admin.payouts.title')
                </p>
                @if(isset($pendingPayouts) && $pendingPayouts->count() > 0)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                        {{ $pendingPayouts->count() }} Pending
                    </span>
                @else
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                        All Caught Up
                    </span>
                @endif
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                Review and process customer affiliate wallet withdrawal requests.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <button
                type="button"
                class="primary-button flex items-center gap-2"
                onclick="openPayoutModal()"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                Quick Process Payout
            </button>
        </div>
    </div>

    <div class="mt-4">
        <x-admin::datagrid :src="route('admin.affiliate.payouts.index')" />
    </div>

    <!-- Process Payout Modal -->
    <div
        id="processPayoutModal"
        style="display: none; position: fixed; inset: 0; z-index: 99999; align-items: center; justify-content: center; background: rgba(15, 23, 42, 0.7); backdrop-filter: blur(4px); padding: 1rem;"
        onclick="handlePayoutModalBackdropClick(event)"
    >
        <div
            class="bg-white dark:bg-gray-900 rounded-2xl w-full p-6 shadow-2xl relative border border-gray-200 dark:border-gray-800"
            style="max-width: 600px; max-height: 90vh; overflow-y: auto;"
            onclick="event.stopPropagation()"
        >
            <div class="flex items-center justify-between pb-3 border-b border-gray-200 dark:border-gray-800">
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                        Process Affiliate Withdrawal
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                        Transfer requested amount to affiliate and confirm reference
                    </p>
                </div>
                <button
                    type="button"
                    class="w-8 h-8 rounded-full flex items-center justify-center text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-800 transition"
                    onclick="closePayoutModal()"
                >
                    ✕
                </button>
            </div>

            <form id="payoutActionForm" method="POST" action="" class="mt-4 space-y-4">
                @csrf

                <!-- Payout Selector -->
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">
                        Select Payout Request <span class="text-red-500">*</span>
                    </label>

                    @if(isset($pendingPayoutsPayload) && $pendingPayoutsPayload->isNotEmpty())
                        <select
                            id="target_payout_id"
                            class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3.5 py-2.5 text-sm font-medium text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition"
                            onchange="onPayoutSelected(this.value)"
                        >
                            <option value="">-- Choose a pending withdrawal request --</option>
                            @foreach($pendingPayoutsPayload as $p)
                                <option value="{{ $p['id'] }}">
                                    #{{ $p['id'] }} · {{ $p['customer_name'] }} — {{ $p['amount_formatted'] }} ({{ strtoupper(str_replace('_', ' ', $p['method'])) }})
                                </option>
                            @endforeach
                        </select>
                    @else
                        <div class="p-3.5 rounded-lg border border-dashed border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/40 text-center">
                            <p class="text-xs text-gray-600 dark:text-gray-400 font-medium">
                                No pending payout requests right now.
                            </p>
                            <div class="mt-2 text-left">
                                <label class="block text-[11px] text-gray-500 mb-1">Or enter Payout Request ID manually:</label>
                                <input
                                    type="number"
                                    id="manual_payout_id"
                                    placeholder="e.g. 1"
                                    class="w-full rounded border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3 py-1.5 text-sm text-gray-800 dark:text-white"
                                />
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Beneficiary Live Details Card -->
                <div id="payout_details_card" class="hidden rounded-xl border border-blue-200 bg-blue-50/70 p-4 dark:border-blue-900/50 dark:bg-blue-950/30">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <span class="text-[11px] font-bold uppercase tracking-wider text-blue-600 dark:text-blue-400">
                                Beneficiary Affiliate
                            </span>
                            <h4 id="detail_customer_name" class="text-base font-bold text-gray-900 dark:text-white">
                                —
                            </h4>
                            <p id="detail_customer_email" class="text-xs text-gray-600 dark:text-gray-300">
                                —
                            </p>
                        </div>
                        <div class="text-right">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">
                                Amount to Pay
                            </span>
                            <div id="detail_amount" class="text-2xl font-black text-emerald-600 dark:text-emerald-400">
                                ₹0.00
                            </div>
                            <span id="detail_payout_number" class="text-[11px] text-gray-500 font-mono">
                                —
                            </span>
                        </div>
                    </div>

                    <!-- Payment Details (UPI / Bank) -->
                    <div class="mt-3 pt-3 border-t border-blue-200/70 dark:border-blue-900/40">
                        <!-- UPI View -->
                        <div id="detail_upi_box" class="space-y-1.5">
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-semibold text-gray-700 dark:text-gray-300">UPI ID for Payment:</span>
                                <button
                                    type="button"
                                    onclick="copyToClipboard('detail_upi_val', this)"
                                    class="text-xs text-blue-600 hover:text-blue-700 dark:text-blue-400 font-semibold cursor-pointer inline-flex items-center gap-1"
                                >
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                    Copy UPI ID
                                </button>
                            </div>
                            <div class="flex items-center justify-between bg-white dark:bg-gray-900 px-3 py-2 rounded-lg border border-blue-200 dark:border-gray-800">
                                <span id="detail_upi_val" class="font-mono text-sm font-bold text-gray-900 dark:text-white select-all">
                                    —
                                </span>
                                <span class="text-[11px] px-2 py-0.5 rounded bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300 font-semibold uppercase">
                                    UPI
                                </span>
                            </div>
                            <p class="text-[11px] text-gray-500 dark:text-gray-400">
                                Transfer funds to this UPI ID via GPay/PhonePe/Paytm, then enter the UTR below.
                            </p>
                        </div>

                        <!-- Bank Transfer View -->
                        <div id="detail_bank_box" class="hidden space-y-2">
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-semibold text-gray-700 dark:text-gray-300">Bank Account Details:</span>
                                <span class="text-[11px] px-2 py-0.5 rounded bg-indigo-100 text-indigo-800 dark:bg-indigo-950 dark:text-indigo-300 font-semibold uppercase">
                                    Bank Transfer
                                </span>
                            </div>
                            <div class="grid grid-cols-2 gap-2 bg-white dark:bg-gray-900 p-3 rounded-lg border border-blue-200 dark:border-gray-800 text-xs">
                                <div>
                                    <span class="text-gray-500 block text-[11px]">Account Number:</span>
                                    <div class="flex items-center gap-1.5 mt-0.5">
                                        <span id="detail_bank_acc" class="font-mono font-bold text-gray-900 dark:text-white select-all">
                                            —
                                        </span>
                                        <button type="button" onclick="copyToClipboard('detail_bank_acc', this)" class="text-blue-600 hover:text-blue-700 dark:text-blue-400">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                        </button>
                                    </div>
                                </div>
                                <div>
                                    <span class="text-gray-500 block text-[11px]">IFSC Code:</span>
                                    <div class="flex items-center gap-1.5 mt-0.5">
                                        <span id="detail_bank_ifsc" class="font-mono font-bold text-gray-900 dark:text-white select-all">
                                            —
                                        </span>
                                        <button type="button" onclick="copyToClipboard('detail_bank_ifsc', this)" class="text-blue-600 hover:text-blue-700 dark:text-blue-400">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-span-2 pt-1.5 border-t border-gray-100 dark:border-gray-800 flex justify-between">
                                    <div>
                                        <span class="text-gray-500 text-[11px]">Account Holder:</span>
                                        <span id="detail_bank_holder" class="font-semibold text-gray-900 dark:text-white ml-1">
                                            —
                                        </span>
                                    </div>
                                    <div id="detail_bank_name_wrap">
                                        <span class="text-gray-500 text-[11px]">Bank:</span>
                                        <span id="detail_bank_name" class="font-semibold text-gray-900 dark:text-white ml-1">
                                            —
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Type Selector -->
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">
                        Select Action <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="cursor-pointer flex items-center gap-2.5 p-3 rounded-lg border border-emerald-300 dark:border-emerald-800 bg-emerald-50/50 dark:bg-emerald-950/20 text-emerald-900 dark:text-emerald-200 transition">
                            <input
                                type="radio"
                                name="payout_action_choice"
                                value="complete"
                                checked
                                onchange="togglePayoutAction(this.value)"
                                class="text-emerald-600 focus:ring-emerald-500"
                            />
                            <div>
                                <span class="text-sm font-bold block">Approve & Mark Paid</span>
                                <span class="text-[11px] text-gray-500 dark:text-gray-400">Record Bank UTR reference</span>
                            </div>
                        </label>

                        <label class="cursor-pointer flex items-center gap-2.5 p-3 rounded-lg border border-red-200 dark:border-red-900/50 bg-red-50/30 dark:bg-red-950/20 text-red-900 dark:text-red-200 transition">
                            <input
                                type="radio"
                                name="payout_action_choice"
                                value="reject"
                                onchange="togglePayoutAction(this.value)"
                                class="text-red-600 focus:ring-red-500"
                            />
                            <div>
                                <span class="text-sm font-bold block">Reject & Refund</span>
                                <span class="text-[11px] text-gray-500 dark:text-gray-400">Return funds to wallet</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Complete Fields -->
                <div id="complete_fields" class="space-y-3.5">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
                            Bank Reference / UTR Number <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            name="transaction_reference"
                            id="transaction_reference"
                            placeholder="e.g. UTR42981039821 or UPI-40821039"
                            class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3.5 py-2 text-sm text-gray-800 dark:text-white font-mono focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        />
                        <p class="text-[11px] text-gray-500 mt-1">
                            The Unique Transaction Reference (UTR) from your banking / UPI payment confirmation.
                        </p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
                            Admin Transfer Notes (Optional)
                        </label>
                        <textarea
                            name="admin_notes"
                            rows="2"
                            placeholder="Optional remarks (e.g. Paid via HDFC Current A/C)"
                            class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3.5 py-2 text-sm text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        ></textarea>
                    </div>
                </div>

                <!-- Reject Fields -->
                <div id="reject_fields" class="hidden space-y-3.5">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
                            Reason for Rejection <span class="text-red-500">*</span>
                        </label>
                        <textarea
                            name="reason"
                            id="reject_reason"
                            rows="3"
                            placeholder="e.g. Invalid UPI ID / Incorrect IFSC code provided. Please update your payment details."
                            class="w-full rounded-lg border border-red-300 dark:border-red-900 bg-white dark:bg-gray-800 px-3.5 py-2 text-sm text-gray-800 dark:text-white focus:ring-2 focus:ring-red-500 focus:outline-none"
                        ></textarea>
                        <div class="mt-2 p-2.5 rounded-lg bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-900 text-amber-800 dark:text-amber-300 text-xs flex items-start gap-2">
                            <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            <span>Rejecting will instantly unlock and refund the entire requested balance back to the affiliate's wallet.</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-800">
                    <button
                        type="button"
                        class="secondary-button"
                        onclick="closePayoutModal()"
                    >
                        Cancel
                    </button>
                    <button
                        type="submit"
                        id="submit_payout_btn"
                        class="primary-button bg-emerald-600 hover:bg-emerald-700 border-emerald-600 text-white"
                        onclick="return submitPayoutAction(event)"
                    >
                        Confirm & Mark as Paid
                    </button>
                </div>
            </form>
        </div>
    </div>

    @pushOnce('scripts')
        <script>
            const pendingPayoutsList = @json($pendingPayoutsPayload ?? []);

            window.openPayoutModal = function(payoutId = null) {
                const modal = document.getElementById('processPayoutModal');
                if (!modal) return;

                modal.style.display = 'flex';

                const selectElem = document.getElementById('target_payout_id');
                const manualElem = document.getElementById('manual_payout_id');

                let selectedId = payoutId;

                if (!selectedId && selectElem && selectElem.options.length > 1) {
                    selectedId = selectElem.options[1].value;
                }

                if (selectedId) {
                    if (selectElem) {
                        selectElem.value = selectedId;
                    }
                    if (manualElem) {
                        manualElem.value = selectedId;
                    }
                    window.onPayoutSelected(selectedId);
                } else if (payoutId) {
                    if (manualElem) manualElem.value = payoutId;
                }
            };

            window.closePayoutModal = function() {
                const modal = document.getElementById('processPayoutModal');
                if (modal) {
                    modal.style.display = 'none';
                }
            };

            window.handlePayoutModalBackdropClick = function(event) {
                if (event.target && event.target.id === 'processPayoutModal') {
                    window.closePayoutModal();
                }
            };

            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    window.closePayoutModal();
                }
            });

            window.onPayoutSelected = function(id) {
                const detailsCard = document.getElementById('payout_details_card');
                if (!id) {
                    if (detailsCard) detailsCard.classList.add('hidden');
                    return;
                }

                const payout = pendingPayoutsList.find(p => String(p.id) === String(id));
                if (!payout) {
                    if (detailsCard) detailsCard.classList.add('hidden');
                    return;
                }

                if (detailsCard) detailsCard.classList.remove('hidden');

                document.getElementById('detail_customer_name').textContent = payout.customer_name || 'Affiliate User';
                document.getElementById('detail_customer_email').textContent = payout.customer_email || '';
                document.getElementById('detail_amount').textContent = payout.amount_formatted;
                document.getElementById('detail_payout_number').textContent = payout.payout_number;

                const upiBox = document.getElementById('detail_upi_box');
                const bankBox = document.getElementById('detail_bank_box');

                if (payout.method === 'upi') {
                    upiBox.classList.remove('hidden');
                    bankBox.classList.add('hidden');
                    document.getElementById('detail_upi_val').textContent = (payout.details && payout.details.upi_id) ? payout.details.upi_id : 'N/A';
                } else {
                    upiBox.classList.add('hidden');
                    bankBox.classList.remove('hidden');
                    document.getElementById('detail_bank_acc').textContent = (payout.details && payout.details.account_number) ? payout.details.account_number : 'N/A';
                    document.getElementById('detail_bank_ifsc').textContent = (payout.details && payout.details.ifsc_code) ? payout.details.ifsc_code : 'N/A';
                    document.getElementById('detail_bank_holder').textContent = (payout.details && payout.details.account_holder) ? payout.details.account_holder : payout.customer_name;
                    document.getElementById('detail_bank_name').textContent = (payout.details && payout.details.bank_name) ? payout.details.bank_name : '—';
                }
            };

            window.togglePayoutAction = function(val) {
                const compFields = document.getElementById('complete_fields');
                const rejFields = document.getElementById('reject_fields');
                const submitBtn = document.getElementById('submit_payout_btn');

                if (val === 'complete') {
                    compFields.classList.remove('hidden');
                    rejFields.classList.add('hidden');
                    submitBtn.textContent = 'Confirm & Mark as Paid';
                    submitBtn.className = 'primary-button bg-emerald-600 hover:bg-emerald-700 border-emerald-600 text-white';
                } else {
                    compFields.classList.add('hidden');
                    rejFields.classList.remove('hidden');
                    submitBtn.textContent = 'Confirm Rejection & Refund';
                    submitBtn.className = 'primary-button bg-red-600 hover:bg-red-700 border-red-600 text-white';
                }
            };

            window.copyToClipboard = function(elementId, btnElem) {
                const text = document.getElementById(elementId)?.textContent?.trim();
                if (!text || text === '—' || text === 'N/A') return;

                navigator.clipboard.writeText(text).then(() => {
                    const originalHtml = btnElem.innerHTML;
                    btnElem.innerHTML = '✓ Copied!';
                    setTimeout(() => {
                        btnElem.innerHTML = originalHtml;
                    }, 2000);
                });
            };

            window.submitPayoutAction = function(e) {
                const selectElem = document.getElementById('target_payout_id');
                const manualElem = document.getElementById('manual_payout_id');

                const payoutId = (selectElem && selectElem.value) ? selectElem.value : (manualElem ? manualElem.value : '');

                if (!payoutId) {
                    alert('Please select or enter a Payout Request ID.');
                    e.preventDefault();
                    return false;
                }

                const actionChoice = document.querySelector('input[name="payout_action_choice"]:checked')?.value || 'complete';
                const form = document.getElementById('payoutActionForm');

                if (actionChoice === 'complete') {
                    const ref = document.getElementById('transaction_reference').value.trim();
                    if (!ref) {
                        alert('Please enter Bank Reference / UTR Number.');
                        document.getElementById('transaction_reference').focus();
                        e.preventDefault();
                        return false;
                    }
                    form.action = "{{ url(config('app.admin_url') . '/affiliate/payouts') }}/" + payoutId + "/complete";
                } else {
                    const reason = document.getElementById('reject_reason').value.trim();
                    if (!reason) {
                        alert('Please enter Reason for rejection.');
                        document.getElementById('reject_reason').focus();
                        e.preventDefault();
                        return false;
                    }
                    form.action = "{{ url(config('app.admin_url') . '/affiliate/payouts') }}/" + payoutId + "/reject";
                }

                return true;
            };
        </script>
    @endPushOnce
</x-admin::layouts>
