<x-admin::layouts>
    <x-slot:title>
        @lang('affiliate::app.admin.rules.title')
    </x-slot>

    <div class="mt-3 flex items-center justify-between gap-4 max-sm:flex-wrap">
        <div>
            <p class="text-xl font-bold text-gray-800 dark:text-white">
                @lang('affiliate::app.admin.rules.title')
            </p>
            <p class="text-sm text-gray-500">
                Override global default rates at the Category or individual Product level.
            </p>
        </div>

        <div class="flex items-center gap-x-2.5">
            <button
                type="button"
                class="primary-button"
                onclick="document.getElementById('ruleModal').classList.remove('hidden')"
            >
                @lang('affiliate::app.admin.rules.create-btn')
            </button>
        </div>
    </div>

    <div class="mt-4">
        <x-admin::datagrid :src="route('admin.affiliate.rules.index')" />
    </div>

    <!-- Create Custom Rule Modal -->
    <div id="ruleModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-900 rounded-lg max-w-lg w-full p-6 shadow-xl relative border dark:border-gray-800">
            <div class="flex items-center justify-between pb-3 border-b dark:border-gray-800">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white">
                    Add Commission Rule Override
                </h3>
                <button
                    type="button"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200"
                    onclick="document.getElementById('ruleModal').classList.add('hidden')"
                >
                    ✕
                </button>
            </div>

            <form action="{{ route('admin.affiliate.rules.store') }}" method="POST" onsubmit="prepareEntityId()" class="mt-4 space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Entity Type <span class="text-red-500">*</span>
                    </label>
                    <select
                        name="entity_type"
                        id="entity_type"
                        required
                        class="w-full rounded border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-800 dark:text-white"
                        onchange="toggleEntityInputs(this.value)"
                    >
                        <option value="category">Category</option>
                        <option value="product">Product</option>
                    </select>
                </div>

                <div id="category_select_group">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Select Category <span class="text-red-500">*</span>
                    </label>
                    <select
                        name="category_entity_id"
                        id="category_entity_id"
                        class="w-full rounded border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-800 dark:text-white"
                    >
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name ?? 'Category #' . $cat->id }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="product_input_group" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Product ID <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="number"
                        name="product_entity_id"
                        id="product_entity_id"
                        placeholder="Enter Product ID (e.g. 12)"
                        class="w-full rounded border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-800 dark:text-white"
                    />
                </div>

                <!-- Hidden field for actual entity_id submitted -->
                <input type="hidden" name="entity_id" id="entity_id" value="{{ $categories->first()?->id ?? 1 }}" />

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Commission Type <span class="text-red-500">*</span>
                    </label>
                    <select
                        name="commission_type"
                        required
                        class="w-full rounded border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-800 dark:text-white"
                    >
                        <option value="percentage">Percentage (%)</option>
                        <option value="fixed">Fixed Amount (₹)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Commission Value <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="number"
                        step="0.01"
                        name="commission_value"
                        required
                        placeholder="e.g. 10.00 or 150.00"
                        class="w-full rounded border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-800 dark:text-white"
                    />
                </div>

                <div class="flex items-center gap-2 pt-2">
                    <input
                        type="checkbox"
                        name="is_excluded"
                        id="is_excluded"
                        value="1"
                        class="rounded border-gray-300 text-blue-600 shadow-sm"
                    />
                    <label for="is_excluded" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        Exclude from Referral Program (0% commission)
                    </label>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t dark:border-gray-800">
                    <button
                        type="button"
                        class="secondary-button"
                        onclick="document.getElementById('ruleModal').classList.add('hidden')"
                    >
                        Cancel
                    </button>
                    <button type="submit" class="primary-button" onclick="prepareEntityId()">
                        Save Rule
                    </button>
                </div>
            </form>
        </div>
    </div>

    @pushOnce('scripts')
        <script>
            function toggleEntityInputs(type) {
                var catGroup = document.getElementById('category_select_group');
                var prodGroup = document.getElementById('product_input_group');

                if (type === 'category') {
                    catGroup.classList.remove('hidden');
                    prodGroup.classList.add('hidden');
                } else {
                    catGroup.classList.add('hidden');
                    prodGroup.classList.remove('hidden');
                }
            }

            function prepareEntityId() {
                var type = document.getElementById('entity_type').value;
                var finalId = document.getElementById('entity_id');

                if (type === 'category') {
                    finalId.value = document.getElementById('category_entity_id').value;
                } else {
                    finalId.value = document.getElementById('product_entity_id').value;
                }
            }
        </script>
    @endPushOnce
</x-admin::layouts>
