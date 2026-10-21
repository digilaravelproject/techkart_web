<!-- SEO Meta Content -->
@push('meta')
    <meta name="description" content="@lang('shop::app.customers.login-form.page-title')"/>

    <meta name="keywords" content="@lang('shop::app.customers.login-form.page-title')"/>
@endPush

<x-shop::layouts
    :has-header="false"
    :has-feature="false"
    :has-footer="false"
>
    <!-- Page Title -->
    <x-slot:title>
        @lang('shop::app.customers.login-form.page-title')
    </x-slot>

    <div class="container my-14 flex flex-col items-center justify-center max-1180:px-5 max-md:my-8">
        {!! view_render_event('bagisto.shop.customers.login.logo.before') !!}

        <!-- Company Logo -->
        <div class="mb-6 flex items-center justify-center">
            <a
                href="{{ route('shop.home.index') }}"
                aria-label="{{ config('app.name', 'BEENZO') }}"
            >
                <img
                    src="{{ core()->getCurrentChannel()->logo_url ?? asset('images/FullLogo_NoBuffer.jpeg') }}"
                    alt="{{ core()->getCurrentChannel()->logo_alt ?: config('app.name') }}"
                    class="max-h-[46px] w-auto object-contain"
                >
            </a>
        </div>

        {!! view_render_event('bagisto.shop.customers.login.logo.after') !!}

        <!-- Form Container -->
        <div class="w-full max-w-[500px] rounded-2xl border border-[#D9C2A4]/50 bg-white p-10 shadow-lg max-md:p-8 max-sm:border-none max-sm:p-4 max-sm:shadow-none">
            <div class="text-center">
                <h1 class="font-dmserif text-3xl text-zinc-900 max-sm:text-2xl">
                    @lang('shop::app.customers.login-form.page-title')
                </h1>

                <p class="mt-2 text-sm text-zinc-500">
                    @lang('shop::app.customers.login-form.form-login-text')
                </p>
            </div>

            {!! view_render_event('bagisto.shop.customers.login.before') !!}

            <div class="mt-8">
                <x-shop::form :action="route('shop.customer.session.create')">
                    {!! view_render_event('bagisto.shop.customers.login_form_controls.before') !!}

                    <!-- Email -->
                    <x-shop::form.control-group>
                        <x-shop::form.control-group.label class="required font-medium text-zinc-700">
                            @lang('shop::app.customers.login-form.email')
                        </x-shop::form.control-group.label>

                        <x-shop::form.control-group.control
                            type="email"
                            class="rounded-xl px-4 py-3 border-zinc-300 focus:border-[#c2955e] focus:ring-1 focus:ring-[#c2955e]"
                            name="email"
                            rules="required|email"
                            value=""
                            :label="trans('shop::app.customers.login-form.email')"
                            placeholder="email@example.com"
                            :aria-label="trans('shop::app.customers.login-form.email')"
                            aria-required="true"
                        />

                        <x-shop::form.control-group.error control-name="email" />
                    </x-shop::form.control-group>

                    <!-- Password -->
                    <x-shop::form.control-group class="mb-2">
                        <x-shop::form.control-group.label class="required font-medium text-zinc-700">
                            @lang('shop::app.customers.login-form.password')
                        </x-shop::form.control-group.label>

                        <x-shop::form.control-group.control
                            type="password"
                            class="rounded-xl px-4 py-3 border-zinc-300 focus:border-[#c2955e] focus:ring-1 focus:ring-[#c2955e]"
                            id="password"
                            name="password"
                            rules="required|min:6"
                            value=""
                            :label="trans('shop::app.customers.login-form.password')"
                            :placeholder="trans('shop::app.customers.login-form.password')"
                            :aria-label="trans('shop::app.customers.login-form.password')"
                            aria-required="true"
                        />

                        <x-shop::form.control-group.error control-name="password" />
                    </x-shop::form.control-group>

                    <div class="flex justify-end mb-4">
                        <a
                            href="{{ route('shop.customers.forgot_password.create') }}"
                            class="cursor-pointer text-xs font-semibold text-navyBlue hover:underline"
                        >
                            @lang('shop::app.customers.login-form.forgot-pass')
                        </a>
                    </div>

                    <!-- Captcha -->
                    @if (core()->getConfigData('customer.captcha.credentials.status'))
                        <x-shop::form.control-group class="mt-4">
                            {!! \Webkul\Customer\Facades\Captcha::render() !!}

                            <x-shop::form.control-group.error control-name="recaptcha_token" />
                        </x-shop::form.control-group>
                    @endif

                    <!-- Submit Button -->
                    <div class="mt-6">
                        <button
                            class="primary-button !w-full !max-w-full rounded-xl py-3.5 text-center text-base font-semibold shadow-sm transition-all hover:shadow-md"
                            type="submit"
                        >
                            @lang('shop::app.customers.login-form.button-title')
                        </button>
                    </div>
                </x-shop::form>
            </div>

            {!! view_render_event('bagisto.shop.customers.login.after') !!}

            @if (
                request()->cookie('enable-resend')
                && request()->cookie('email-for-resend')
            )
                <p class="mt-4 text-center text-sm font-medium text-zinc-500">
                    <a
                        class="text-navyBlue hover:underline font-semibold"
                        href="{{ route('shop.customers.resend.verification_email', urlencode(request()->cookie('email-for-resend'))) }}"
                    >
                        @lang('shop::app.customers.login-form.resend-verification')
                    </a>
                </p>
            @endif

            <div class="mt-8 border-t border-zinc-100 pt-6 text-center text-sm font-medium text-zinc-600">
                @lang('shop::app.customers.login-form.new-customer')

                <a
                    class="font-semibold text-navyBlue hover:underline ml-1"
                    href="{{ route('shop.customers.register.index') }}"
                >
                    @lang('shop::app.customers.login-form.create-your-account')
                </a>
            </div>
        </div>

        <div class="mb-4 mt-8 flex items-center justify-center gap-2 text-center text-xs text-zinc-500">
            <img src="{{ asset('images/FullLogo_NoBuffer.jpeg') }}" alt="Digi Emperor" class="h-4 w-auto">
            <span>Develop by <a href="https://digiemperor.com/" target="_blank" class="text-blue-600 hover:underline font-medium">Digi Emperor</a></span>
        </div>
    </div>

    @push('scripts')
        {!! \Webkul\Customer\Facades\Captcha::renderJS() !!}
    @endpush
</x-shop::layouts>
