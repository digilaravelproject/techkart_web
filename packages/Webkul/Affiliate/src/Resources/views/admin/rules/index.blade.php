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
    <div id="ruleModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-900 rounded-2xl max-w-xl w-full p-6 shadow-2xl relative border border-gray-200 dark:border-gray-800 transition-all">
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-4 border-b border-gray-100 dark:border-gray-800">
                <div class="flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 font-bold text-lg">
                        %
                    </span>
                    <div>
                        <h3 class="text-base font-bold text-gray-900 dark:text-white">
                            Add Commission Rule Override
                        </h3>
                        <p class="text-xs text-gray-500">
                            Set custom commission for a category tree or specific product.
                        </p>
                    </div>
                </div>

                <button
                    type="button"
                    class="flex h-8 w-8 items-center justify-center rounded-lg text-gray-400 hover:bg-gray-100 hover:text-gray-600 dark:hover:bg-gray-800 dark:hover:text-gray-200 transition"
                    onclick="document.getElementById('ruleModal').classList.add('hidden')"
                >
                    ✕
                </button>
            </div>

            <!-- Form -->
            <form action="{{ route('admin.affiliate.rules.store') }}" method="POST" onsubmit="prepareEntityId()" class="mt-5 space-y-4">
                @csrf

                <!-- Entity Type Selection -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">
                        Apply Rule To <span class="text-red-500">*</span>
                    </label>

                    <div class="grid grid-cols-2 gap-3">
                        <label class="flex items-center gap-2.5 p-3 rounded-xl border border-gray-200 dark:border-gray-700 cursor-pointer transition hover:border-amber-500 bg-gray-50/50 dark:bg-gray-800/50">
                            <input
                                type="radio"
                                name="entity_type"
                                value="category"
                                checked
                                onchange="toggleEntityInputs(this.value)"
                                class="text-amber-600 focus:ring-amber-500"
                            />
                            <div>
                                <p class="text-sm font-semibold text-gray-800 dark:text-white">Category</p>
                                <p class="text-xs text-gray-500">Applies to category and child items</p>
                            </div>
                        </label>

                        <label class="flex items-center gap-2.5 p-3 rounded-xl border border-gray-200 dark:border-gray-700 cursor-pointer transition hover:border-amber-500 bg-gray-50/50 dark:bg-gray-800/50">
                            <input
                                type="radio"
                                name="entity_type"
                                value="product"
                                onchange="toggleEntityInputs(this.value)"
                                class="text-amber-600 focus:ring-amber-500"
                            />
                            <div>
                                <p class="text-sm font-semibold text-gray-800 dark:text-white">Product</p>
                                <p class="text-xs text-gray-500">Specific single product override</p>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Category Dropdown Group -->
                <div id="category_select_group">
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">
                        Select Category <span class="text-red-500">*</span>
                    </label>
                    <select
                        name="category_entity_id"
                        id="category_entity_id"
                        class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3.5 py-2.5 text-sm text-gray-800 dark:text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition"
                    >
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-gray-500">
                        Tip: Choosing <strong>Root</strong> applies to all products across your store.
                    </p>
                </div>

                <!-- Product Dropdown Group -->
                <div id="product_input_group" class="hidden">
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">
                        Select Product <span class="text-red-500">*</span>
                    </label>

                    <input
                        type="text"
                        id="product_search_filter"
                        placeholder="Type to filter products..."
                        onkeyup="filterProductDropdown(this.value)"
                        class="mb-2 w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3.5 py-2 text-xs text-gray-800 dark:text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500"
                    />

                    <select
                        name="product_entity_id"
                        id="product_entity_id"
                        class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3.5 py-2.5 text-sm text-gray-800 dark:text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition"
                    >
                        @foreach ($products as $prod)
                            <option value="{{ $prod->id }}">[ID #{{ $prod->id }}] {{ $prod->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Hidden field for actual entity_id submitted -->
                <input type="hidden" name="entity_id" id="entity_id" value="{{ $categories[0]?->id ?? 1 }}" />

                <!-- Commission Type & Value Grid -->
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">
                            Commission Type <span class="text-red-500">*</span>
                        </label>
                        <select
                            name="commission_type"
                            id="commission_type"
                            required
                            onchange="updateValueUI(this.value)"
                            class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3.5 py-2.5 text-sm text-gray-800 dark:text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition"
                        >
                            <option value="percentage">Percentage (%)</option>
                            <option value="fixed">Fixed Amount (₹)</option>
                        </select>
                    </div>

                    <div>
                        <label id="value_label" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">
                            Commission Rate (%) <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input
                                type="number"
                                step="0.01"
                                min="0"
                                name="commission_value"
                                id="commission_value"
                                required
                                placeholder="10.00"
                                class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3.5 py-2.5 pr-10 text-sm text-gray-800 dark:text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition"
                            />
                            <span
                                id="unit_badge"
                                class="absolute right-3 top-2.5 text-xs font-bold text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded-md"
                            >
                                %
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Dynamic Helper Text -->
                <p id="calc_hint" class="text-xs text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/30 p-2.5 rounded-xl border border-amber-200/60 dark:border-amber-900/40">
                    ℹ️ Affiliates will earn a <strong>percentage (%)</strong> of the product sale price.
                </p>

                <!-- Exclusion Option -->
                <div class="rounded-xl border border-gray-200 dark:border-gray-800 p-3 bg-gray-50/50 dark:bg-gray-800/40">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input
                            type="checkbox"
                            name="is_excluded"
                            id="is_excluded"
                            value="1"
                            onchange="toggleExcluded(this.checked)"
                            class="h-4 w-4 rounded border-gray-300 text-amber-600 focus:ring-amber-500"
                        />
                        <div>
                            <p class="text-sm font-semibold text-gray-800 dark:text-white">
                                Exclude from Referral Program (0% commission)
                            </p>
                            <p class="text-xs text-gray-500">
                                Disables commission payouts for this category or product.
                            </p>
                        </div>
                    </label>
                </div>

                <!-- Modal Actions -->
                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-800">
                    <button
                        type="button"
                        class="secondary-button"
                        onclick="document.getElementById('ruleModal').classList.add('hidden')"
                    >
                        Cancel
                    </button>
                    <button type="submit" class="primary-button" onclick="prepareEntityId()">
                        Save Commission Rule
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

            function updateValueUI(type) {
                var label = document.getElementById('value_label');
                var badge = document.getElementById('unit_badge');
                var hint = document.getElementById('calc_hint');
                var input = document.getElementById('commission_value');

                if (type === 'percentage') {
                    label.innerHTML = 'Commission Rate (%) <span class="text-red-500">*</span>';
                    badge.innerText = '%';
                    input.placeholder = '10.00';
                    hint.innerHTML = 'ℹ️ Affiliates will earn a <strong>percentage (%)</strong> of the product sale price.';
                } else {
                    label.innerHTML = 'Fixed Amount (₹) <span class="text-red-500">*</span>';
                    badge.innerText = '₹';
                    input.placeholder = '150.00';
                    hint.innerHTML = 'ℹ️ Affiliates will earn a <strong>flat rupee amount (₹)</strong> on every item sold.';
                }
            }

            function toggleExcluded(isExcluded) {
                var input = document.getElementById('commission_value');
                if (isExcluded) {
                    input.value = '0';
                    input.setAttribute('disabled', 'disabled');
                } else {
                    input.removeAttribute('disabled');
                    if (input.value === '0') {
                        input.value = '';
                    }
                }
            }

            function filterProductDropdown(search) {
                var select = document.getElementById('product_entity_id');
                var filter = search.toLowerCase();
                var options = select.options;

                for (var i = 0; i < options.length; i++) {
                    var text = options[i].text.toLowerCase();
                    options[i].style.display = text.includes(filter) ? '' : 'none';
                }
            }

            function prepareEntityId() {
                var entityTypeInput = document.querySelector('input[name="entity_type"]:checked');
                var type = entityTypeInput ? entityTypeInput.value : 'category';
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
