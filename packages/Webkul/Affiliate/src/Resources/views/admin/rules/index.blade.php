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
                onclick="openModal()"
            >
                @lang('affiliate::app.admin.rules.create-btn')
            </button>
        </div>
    </div>

    <div class="mt-4">
        <x-admin::datagrid :src="route('admin.affiliate.rules.index')" />
    </div>

    <!-- Create Custom Rule Modal Overlay -->
    <div
        id="ruleModal"
        style="display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; z-index: 99999; background: rgba(15, 23, 42, 0.65); backdrop-filter: blur(4px); align-items: center; justify-content: center; padding: 20px; box-sizing: border-box;"
        onclick="if (event.target === this) closeModal()"
    >
        <div
            style="max-width: 560px; width: 100%; max-height: 90vh; overflow-y: auto; background: #ffffff; border-radius: 16px; box-shadow: 0 20px 40px -10px rgba(0,0,0,0.3); border: 1px solid #e2e8f0; position: relative; margin: auto;"
            class="dark:bg-gray-900 dark:border-gray-800"
            onclick="event.stopPropagation()"
        >
            <!-- Header -->
            <div style="display: flex; align-items: center; justify-content: space-between; padding: 18px 24px; border-bottom: 1px solid #edf2f7;" class="dark:border-gray-800">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(194, 149, 94, 0.15); display: flex; align-items: center; justify-content: center; color: #a0743e; font-weight: bold; font-size: 16px;">
                        %
                    </div>
                    <div>
                        <h3 style="font-size: 16px; font-weight: 700; color: #1a202c; margin: 0;" class="dark:text-white">
                            Add Commission Rule Override
                        </h3>
                        <p style="font-size: 12px; color: #718096; margin: 2px 0 0 0;">
                            Set custom commission for a category or specific product.
                        </p>
                    </div>
                </div>

                <button
                    type="button"
                    style="background: transparent; border: none; font-size: 20px; color: #a0aec0; cursor: pointer; padding: 4px 8px; border-radius: 6px; line-height: 1;"
                    onclick="closeModal()"
                    onmouseover="this.style.color='#4a5568'"
                    onmouseout="this.style.color='#a0aec0'"
                >
                    ✕
                </button>
            </div>

            <!-- Form -->
            <form action="{{ route('admin.affiliate.rules.store') }}" method="POST" onsubmit="prepareEntityId()" style="padding: 20px 24px; display: flex; flex-direction: column; gap: 16px;">
                @csrf

                <!-- Entity Type Selection -->
                <div>
                    <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #4a5568; margin-bottom: 8px;" class="dark:text-gray-300">
                        Apply Rule To <span style="color: #e53e3e;">*</span>
                    </label>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                        <label style="display: flex; align-items: center; gap: 10px; padding: 12px 14px; border-radius: 12px; border: 1px solid #e2e8f0; cursor: pointer; background: #f7fafc;" class="dark:bg-gray-800 dark:border-gray-700">
                            <input
                                type="radio"
                                name="entity_type"
                                value="category"
                                checked
                                onchange="toggleEntityInputs(this.value)"
                                style="width: 16px; height: 16px; accent-color: #c2955e;"
                            />
                            <div>
                                <span style="font-size: 13px; font-weight: 600; color: #2d3748; display: block;" class="dark:text-white">Category</span>
                                <span style="font-size: 11px; color: #718096;">Applies to category and child items</span>
                            </div>
                        </label>

                        <label style="display: flex; align-items: center; gap: 10px; padding: 12px 14px; border-radius: 12px; border: 1px solid #e2e8f0; cursor: pointer; background: #f7fafc;" class="dark:bg-gray-800 dark:border-gray-700">
                            <input
                                type="radio"
                                name="entity_type"
                                value="product"
                                onchange="toggleEntityInputs(this.value)"
                                style="width: 16px; height: 16px; accent-color: #c2955e;"
                            />
                            <div>
                                <span style="font-size: 13px; font-weight: 600; color: #2d3748; display: block;" class="dark:text-white">Product</span>
                                <span style="font-size: 11px; color: #718096;">Specific single product override</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Category Group -->
                <div id="category_select_group">
                    <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #4a5568; margin-bottom: 6px;" class="dark:text-gray-300">
                        Select Category <span style="color: #e53e3e;">*</span>
                    </label>
                    <select
                        name="category_entity_id"
                        id="category_entity_id"
                        style="width: 100%; height: 42px; border-radius: 10px; border: 1px solid #cbd5e1; padding: 0 12px; font-size: 13px; background: #ffffff;"
                        class="dark:bg-gray-800 dark:border-gray-700 dark:text-white"
                    >
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    <p style="font-size: 11px; color: #718096; margin: 6px 0 0 0;">
                        💡 <strong>Root</strong> category rules apply across the entire catalog.
                    </p>
                </div>

                <!-- Product Group -->
                <div id="product_input_group" style="display: none;">
                    <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #4a5568; margin-bottom: 6px;" class="dark:text-gray-300">
                        Select Product <span style="color: #e53e3e;">*</span>
                    </label>
                    <input
                        type="text"
                        id="product_search_filter"
                        placeholder="Search product by name..."
                        onkeyup="filterProductDropdown(this.value)"
                        style="width: 100%; height: 38px; border-radius: 10px; border: 1px solid #cbd5e1; padding: 0 12px; font-size: 12px; margin-bottom: 8px; box-sizing: border-box;"
                        class="dark:bg-gray-800 dark:border-gray-700 dark:text-white"
                    />
                    <select
                        name="product_entity_id"
                        id="product_entity_id"
                        style="width: 100%; height: 42px; border-radius: 10px; border: 1px solid #cbd5e1; padding: 0 12px; font-size: 13px; background: #ffffff;"
                        class="dark:bg-gray-800 dark:border-gray-700 dark:text-white"
                    >
                        @foreach ($products as $prod)
                            <option value="{{ $prod->id }}">[ID #{{ $prod->id }}] {{ $prod->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Hidden field for actual entity_id submitted -->
                <input type="hidden" name="entity_id" id="entity_id" value="{{ $categories[0]?->id ?? 1 }}" />

                <!-- Commission Type & Value Grid -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <div>
                        <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #4a5568; margin-bottom: 6px;" class="dark:text-gray-300">
                            Commission Type <span style="color: #e53e3e;">*</span>
                        </label>
                        <select
                            name="commission_type"
                            id="commission_type"
                            required
                            onchange="updateValueUI(this.value)"
                            style="width: 100%; height: 42px; border-radius: 10px; border: 1px solid #cbd5e1; padding: 0 12px; font-size: 13px; background: #ffffff;"
                            class="dark:bg-gray-800 dark:border-gray-700 dark:text-white"
                        >
                            <option value="percentage">Percentage (%)</option>
                            <option value="fixed">Fixed Amount (₹)</option>
                        </select>
                    </div>

                    <div>
                        <label id="value_label" style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #4a5568; margin-bottom: 6px;" class="dark:text-gray-300">
                            Commission Rate (%) <span style="color: #e53e3e;">*</span>
                        </label>
                        <div style="position: relative; display: flex; align-items: center;">
                            <input
                                type="number"
                                step="0.01"
                                min="0"
                                name="commission_value"
                                id="commission_value"
                                required
                                placeholder="10.00"
                                style="width: 100%; height: 42px; border-radius: 10px; border: 1px solid #cbd5e1; padding: 0 42px 0 12px; font-size: 14px; box-sizing: border-box;"
                                class="dark:bg-gray-800 dark:border-gray-700 dark:text-white"
                            />
                            <span
                                id="unit_badge"
                                style="position: absolute; right: 10px; font-weight: bold; font-size: 12px; color: #718096; background: #edf2f7; padding: 2px 7px; border-radius: 5px; pointer-events: none;"
                                class="dark:bg-gray-700 dark:text-gray-300"
                            >
                                %
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Helper Hint Box -->
                <div id="calc_hint" style="background-color: #fffbeb; border: 1px solid #fef3c7; color: #b45309; padding: 10px 14px; border-radius: 10px; font-size: 12px; line-height: 1.4;">
                    ℹ️ Affiliates will earn a <strong>percentage (%)</strong> of the product sale price.
                </div>

                <!-- Exclusion Option -->
                <div style="background-color: #f7fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 12px;" class="dark:bg-gray-800 dark:border-gray-700">
                    <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                        <input
                            type="checkbox"
                            name="is_excluded"
                            id="is_excluded"
                            value="1"
                            onchange="toggleExcluded(this.checked)"
                            style="width: 16px; height: 16px; accent-color: #c2955e;"
                        />
                        <div>
                            <span style="font-size: 13px; font-weight: 600; color: #2d3748; display: block;" class="dark:text-white">
                                Exclude from Referral Program (0% commission)
                            </span>
                            <span style="font-size: 11px; color: #718096;">
                                Disables commission payouts for this category or product.
                            </span>
                        </div>
                    </label>
                </div>

                <!-- Footer Buttons -->
                <div style="display: flex; justify-content: flex-end; gap: 10px; padding-top: 14px; border-top: 1px solid #edf2f7;" class="dark:border-gray-800">
                    <button
                        type="button"
                        class="secondary-button"
                        onclick="closeModal()"
                    >
                        Cancel
                    </button>
                    <button
                        type="submit"
                        class="primary-button"
                        onclick="prepareEntityId()"
                    >
                        Save Commission Rule
                    </button>
                </div>
            </form>
        </div>
    </div>

    @pushOnce('scripts')
        <script>
            function openModal() {
                var modal = document.getElementById('ruleModal');
                if (modal) {
                    modal.style.display = 'flex';
                }
            }

            function closeModal() {
                var modal = document.getElementById('ruleModal');
                if (modal) {
                    modal.style.display = 'none';
                }
            }

            function toggleEntityInputs(type) {
                var catGroup = document.getElementById('category_select_group');
                var prodGroup = document.getElementById('product_input_group');

                if (type === 'category') {
                    catGroup.style.display = 'block';
                    prodGroup.style.display = 'none';
                } else {
                    catGroup.style.display = 'none';
                    prodGroup.style.display = 'block';
                }
            }

            function updateValueUI(type) {
                var label = document.getElementById('value_label');
                var badge = document.getElementById('unit_badge');
                var hint = document.getElementById('calc_hint');
                var input = document.getElementById('commission_value');

                if (type === 'percentage') {
                    label.innerHTML = 'Commission Rate (%) <span style="color: #e53e3e;">*</span>';
                    badge.innerText = '%';
                    input.placeholder = '10.00';
                    hint.innerHTML = 'ℹ️ Affiliates will earn a <strong>percentage (%)</strong> of the product sale price.';
                } else {
                    label.innerHTML = 'Fixed Amount (₹) <span style="color: #e53e3e;">*</span>';
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
