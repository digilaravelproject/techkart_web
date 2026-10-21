<!-- SEO Meta Content -->
@push('meta')
    <meta
        name="description"
        content="@lang('shop::app.customers.reset-password.title')"
    />

    <meta
        name="keywords"
        content="@lang('shop::app.customers.reset-password.title')"
    />
@endPush

<x-shop::layouts
    :has-header="false"
    :has-feature="false"
    :has-footer="false"
>
    <!-- Page Title -->
    <x-slot:title>
        @lang('shop::app.customers.reset-password.title')
    </x-slot>

    <div class="container mt-20 max-1180:px-5 max-md:mt-12">
        {!! view_render_event('bagisto.shop.customers.reset_password.logo.before') !!}
        
        <!-- Company Logo -->
        <div class="flex items-center gap-x-14 max-[1180px]:gap-x-9">
            <a
                href="{{ route('shop.home.index') }}"
                class="m-[0_auto_20px_auto]"
                aria-label="@lang('shop::app.customers.reset-password.bagisto')"
            >
                <img
                    src="{{ core()->getCurrentChannel()->logo_url ?? asset('images/FullLogo_NoBuffer.jpeg') }}"
                    alt="{{ core()->getCurrentChannel()->logo_alt ?: config('app.name') }}"
                    class="max-h-[40px] w-auto object-contain"
                >
            </a>
        </div>

        {!! view_render_event('bagisto.shop.customers.reset_password.logo.after') !!}

        <!-- Form Container -->
        <div class="m-auto w-full max-w-[500px] rounded-2xl border border-[#D9C2A4]/50 bg-white p-8 md:p-10 shadow-lg">
            <h1 class="font-dmserif text-3xl font-medium text-zinc-900 text-center">
                @lang('shop::app.customers.reset-password.title')
            </h1>

            {!! view_render_event('bagisto.shop.customers.reset_password.before') !!}

            <div class="mt-8 rounded">
                <!-- Form Container-->
                <x-shop::form :action="route('shop.customers.reset_password.store')" >
                    <x-shop::form.control-group.control
                        type="hidden"
                        name="token"
                        :value="$token"
                    />

                    {!! view_render_event('bagisto.shop.customers.reset_password_form_controls.before') !!}

                    <!-- Email -->
                    <x-shop::form.control-group class="mb-4">
                        <x-shop::form.control-group.label class="required font-medium text-zinc-700">
                            @lang('shop::app.customers.reset-password.email')
                        </x-shop::form.control-group.label>

                        <x-shop::form.control-group.control
                            type="email"
                            class="rounded-xl px-4 py-3 border border-zinc-300 focus:border-[#c2955e] focus:ring-1 focus:ring-[#c2955e] transition"
                            id="email"
                            name="email"
                            rules="required|email"
                            :value="old('email')"
                            :label="trans('shop::app.customers.reset-password.email')"
                            placeholder="email@example.com"
                            :aria-label="trans('shop::app.customers.reset-password.email')"
                            aria-required="true"
                        />

                        <x-shop::form.control-group.error control-name="email" />
                    </x-shop::form.control-group>

                    <!-- Password -->
                    <x-shop::form.control-group class="mb-4">
                        <x-shop::form.control-group.label class="required font-medium text-zinc-700">
                            @lang('shop::app.customers.reset-password.password')
                        </x-shop::form.control-group.label>

                        <x-shop::form.control-group.control
                            type="password"
                            class="rounded-xl px-4 py-3 border border-zinc-300 focus:border-[#c2955e] focus:ring-1 focus:ring-[#c2955e] transition"
                            name="password"
                            rules="required|min:6"
                            value=""
                            :label="trans('shop::app.customers.reset-password.password')"
                            :placeholder="trans('shop::app.customers.reset-password.password')"
                            ref="password"
                            :aria-label="trans('shop::app.customers.reset-password.password')"
                            aria-required="true"
                        />

                        <x-shop::form.control-group.error control-name="password" />
                    </x-shop::form.control-group>

                    <!-- Confirm Password -->
                    <x-shop::form.control-group class="mb-6">
                        <x-shop::form.control-group.label class="required font-medium text-zinc-700">
                            @lang('shop::app.customers.reset-password.confirm-password')
                        </x-shop::form.control-group.label>

                        <x-shop::form.control-group.control
                            type="password"
                            class="rounded-xl px-4 py-3 border border-zinc-300 focus:border-[#c2955e] focus:ring-1 focus:ring-[#c2955e] transition"
                            name="password_confirmation"
                            rules="confirmed:@password"
                            value=""
                            :label="trans('shop::app.customers.reset-password.confirm-password')"
                            :placeholder="trans('shop::app.customers.reset-password.confirm-password')"
                            :aria-label="trans('shop::app.customers.reset-password.confirm-password')"
                            aria-required="true"
                        />

                        <x-shop::form.control-group.error control-name="password_confirmation" />
                    </x-shop::form.control-group>

                    {!! view_render_event('bagisto.shop.customers.reset_password_form_controls.after') !!}

                    {!! view_render_event('bagisto.shop.customers.reset_password.submit_button.before') !!}

                    <!-- Submit Button -->
                    <div class="mt-8">
                        <button
                            class="w-full rounded-xl bg-[#c2955e] px-8 py-3.5 text-center text-base font-semibold text-white shadow-md hover:bg-[#a67c48] active:scale-[0.99] transition duration-200"
                            type="submit"
                        >
                            @lang('shop::app.customers.reset-password.submit-btn-title')
                        </button>
                    </div>

                    {!! view_render_event('bagisto.shop.customers.reset_password.submit_button.after') !!}
                </x-shop::form>
            </div>

            {!! view_render_event('bagisto.shop.customers.reset_password.after') !!}

        </div>

        <div class="mb-4 mt-8 flex items-center justify-center gap-2 text-center text-xs text-zinc-500">
            <img src="{{ asset('images/FullLogo_NoBuffer.jpeg') }}" alt="Digi Emperor" class="h-4 w-auto">
            <span>Develop by <a href="https://digiemperor.com/" target="_blank" class="text-blue-600 hover:underline font-medium">Digi Emperor</a></span>
        </div>
    </div>
</x-shop::layouts>
