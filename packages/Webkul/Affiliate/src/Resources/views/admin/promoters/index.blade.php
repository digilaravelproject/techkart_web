<x-admin::layouts>
    <x-slot:title>
        @lang('affiliate::app.admin.promoters.title')
    </x-slot>

    <div class="mt-3 flex items-center justify-between gap-4 max-sm:flex-wrap">
        <div>
            <p class="text-xl font-bold text-gray-800 dark:text-white">
                @lang('affiliate::app.admin.promoters.title')
            </p>
            <p class="text-sm text-gray-500">
                Live overview of all registered customer promoters and their wallet balances.
            </p>
        </div>
    </div>

    <div class="mt-4">
        <x-admin::datagrid :src="route('admin.affiliate.index')" />
    </div>
</x-admin::layouts>
