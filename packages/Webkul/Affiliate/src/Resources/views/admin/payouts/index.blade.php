<x-admin::layouts>
    <x-slot:title>
        @lang('affiliate::app.admin.payouts.title')
    </x-slot>

    <div class="mt-3 flex items-center justify-between gap-4 max-sm:flex-wrap">
        <div>
            <p class="text-xl font-bold text-gray-800 dark:text-white">
                @lang('affiliate::app.admin.payouts.title')
            </p>
            <p class="text-sm text-gray-500">
                Review and process customer affiliate wallet withdrawal requests.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <button
                type="button"
                class="primary-button"
                onclick="document.getElementById('processPayoutModal').classList.remove('hidden')"
            >
                Quick Process Payout
            </button>
        </div>
    </div>

    <div class="mt-4">
        <x-admin::datagrid :src="route('admin.affiliate.payouts.index')" />
    </div>

    <!-- Process Payout Modal -->
    <div id="processPayoutModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-900 rounded-lg max-w-md w-full p-6 shadow-xl relative border dark:border-gray-800">
            <div class="flex items-center justify-between pb-3 border-b dark:border-gray-800">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white">
                    Process Withdrawal Request
                </h3>
                <button
                    type="button"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200"
                    onclick="document.getElementById('processPayoutModal').classList.add('hidden')"
                >
                    ✕
                </button>
            </div>

            <form id="payoutActionForm" method="POST" action="" class="mt-4 space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Select Action <span class="text-red-500">*</span>
                    </label>
                    <select
                        id="payout_action_type"
                        class="w-full rounded border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-800 dark:text-white"
                        onchange="togglePayoutFields(this.value)"
                    >
                        <option value="complete">Approve & Mark as Paid (Enter UTR)</option>
                        <option value="reject">Reject & Refund to Wallet</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Payout Request ID (from grid) <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="number"
                        id="target_payout_id"
                        required
                        placeholder="e.g. 1"
                        class="w-full rounded border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-800 dark:text-white"
                    />
                </div>

                <div id="complete_fields" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Bank Reference / UTR Number <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            name="transaction_reference"
                            id="transaction_reference"
                            placeholder="e.g. UTR123456789 or UPI-REF-456"
                            class="w-full rounded border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-800 dark:text-white"
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Admin Notes (Optional)
                        </label>
                        <textarea
                            name="admin_notes"
                            rows="2"
                            placeholder="Optional notes or transfer remarks"
                            class="w-full rounded border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-800 dark:text-white"
                        ></textarea>
                    </div>
                </div>

                <div id="reject_fields" class="hidden space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Reason for Rejection <span class="text-red-500">*</span>
                        </label>
                        <textarea
                            name="reason"
                            id="reject_reason"
                            rows="3"
                            placeholder="Explain why this payout was rejected (will be returned to customer wallet)"
                            class="w-full rounded border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-800 dark:text-white"
                        ></textarea>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t dark:border-gray-800">
                    <button
                        type="button"
                        class="secondary-button"
                        onclick="document.getElementById('processPayoutModal').classList.add('hidden')"
                    >
                        Cancel
                    </button>
                    <button type="submit" class="primary-button" onclick="return submitPayoutAction(event)">
                        Submit
                    </button>
                </div>
            </form>
        </div>
    </div>

    @pushOnce('scripts')
        <script>
            function togglePayoutFields(val) {
                var compFields = document.getElementById('complete_fields');
                var rejFields = document.getElementById('reject_fields');

                if (val === 'complete') {
                    compFields.classList.remove('hidden');
                    rejFields.classList.add('hidden');
                } else {
                    compFields.classList.add('hidden');
                    rejFields.classList.remove('hidden');
                }
            }

            function submitPayoutAction(e) {
                var payoutId = document.getElementById('target_payout_id').value;
                if (!payoutId) {
                    alert('Please enter Payout Request ID.');
                    e.preventDefault();
                    return false;
                }

                var actionType = document.getElementById('payout_action_type').value;
                var form = document.getElementById('payoutActionForm');

                if (actionType === 'complete') {
                    var ref = document.getElementById('transaction_reference').value;
                    if (!ref) {
                        alert('Please enter UTR or Transaction Reference.');
                        e.preventDefault();
                        return false;
                    }
                    form.action = "{{ url(config('app.admin_url') . '/affiliate/payouts') }}/" + payoutId + "/complete";
                } else {
                    var reason = document.getElementById('reject_reason').value;
                    if (!reason) {
                        alert('Please enter Reason for rejection.');
                        e.preventDefault();
                        return false;
                    }
                    form.action = "{{ url(config('app.admin_url') . '/affiliate/payouts') }}/" + payoutId + "/reject";
                }

                return true;
            }
        </script>
    @endPushOnce
</x-admin::layouts>
