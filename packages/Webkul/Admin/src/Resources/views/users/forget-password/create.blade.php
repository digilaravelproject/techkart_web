<x-admin::layouts.anonymous>
    <!-- Page Title -->
    <x-slot:title>
        @lang('admin::app.users.forget-password.create.page-title')
    </x-slot>

    <div class="flex h-[100vh] items-center justify-center bg-[#FDFDF8] dark:bg-gray-950">
        <div class="flex flex-col items-center gap-5">
            <!-- Logo -->
            @if ($logo = core()->getConfigData('general.design.admin_logo.logo_image'))
                <img
                    class="h-10 w-[110px]"
                    src="{{ Storage::url($logo) }}"
                    alt="{{ config('app.name') }}"
                />
            @else
                <img
                    class="h-10 w-auto" 
                    src="{{ asset('images/FullLogo_NoBuffer.jpeg') }}"
                    alt="{{ config('app.name') }}"
                />
            @endif

            <div class="box-shadow flex min-w-[320px] flex-col rounded-xl border border-[#D9C2A4]/50 bg-white shadow-xl dark:border-gray-800 dark:bg-gray-900">
                <!-- Forget Password Form -->
                <x-admin::form :action="route('admin.forget_password.store')">
                    <div class="p-4">
                        <p class="text-xl font-bold text-gray-800 dark:text-white">
                            @lang('admin::app.users.forget-password.create.title')
                        </p>
                    </div>

                    <div class="border-y p-4 dark:border-gray-800">
                        <!-- Registered Email -->
                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label class="required">
                                @lang('admin::app.users.forget-password.create.email')
                            </x-admin::form.control-group.label>

                            <x-admin::form.control-group.control
                                type="email"
                                class="w-[254px] max-w-full" 
                                id="email"
                                name="email" 
                                rules="required|email" 
                                :value="old('email')"
                                :label="trans('admin::app.users.forget-password.create.email')"
                                :placeholder="trans('admin::app.users.forget-password.create.email')"
                            />

                            <x-admin::form.control-group.error control-name="email" />
                        </x-admin::form.control-group>
                    </div>

                    <div class="flex items-center justify-between p-4">
                        <!-- Back to Sign In link -->
                        <a 
                            class="cursor-pointer text-xs font-semibold leading-6 text-blue-600 hover:text-blue-700 hover:underline"
                            href="{{ route('admin.session.create') }}"
                        >
                            @lang('admin::app.users.forget-password.create.sign-in-link')
                        </a>

                        <!-- Form Submit Button -->
                        <button 
                            class="cursor-pointer rounded-lg border border-blue-700 bg-blue-600 px-4 py-2 font-semibold text-white shadow-sm transition-all hover:bg-blue-700">
                            @lang('admin::app.users.forget-password.create.submit-btn')
                        </button>
                    </div>
                </x-admin::form>
            </div>

            <!-- Powered By -->
            <div class="flex items-center justify-center gap-2 text-sm font-normal">
                <img src="{{ asset('images/FullLogo_NoBuffer.jpeg') }}" alt="Digi Emperor" class="h-4 w-auto">
                <span>Develop by <a class="text-blue-600 hover:underline dark:text-darkBlue font-medium" href="https://digiemperor.com/" target="_blank">Digi Emperor</a></span>
            </div>
        </div>
    </div>
</x-admin::layouts.anonymous>