<!-- SEO Meta Content -->
@push('meta')
    <meta
        name="description"
        content="@lang('shop::app.customers.signup-form.page-title')"
    />

    <meta
        name="keywords"
        content="@lang('shop::app.customers.signup-form.page-title')"
    />
@endPush

<x-shop::layouts
    :has-header="false"
    :has-feature="false"
    :has-footer="false"
>
    <!-- Page Title -->
    <x-slot:title>
        @lang('shop::app.customers.signup-form.page-title')
    </x-slot>

	<div class="container my-14 flex flex-col items-center justify-center max-1180:px-5 max-md:my-8">
        {!! view_render_event('bagisto.shop.customers.sign-up.logo.before') !!}

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

        {!! view_render_event('bagisto.shop.customers.sign-up.logo.after') !!}

        <!-- Form Container -->
		<div class="w-full max-w-[540px] rounded-2xl border border-[#D9C2A4]/50 bg-white p-10 shadow-lg max-md:p-8 max-sm:border-none max-sm:p-4 max-sm:shadow-none">
			<div class="text-center">
				<h1 class="font-dmserif text-3xl text-zinc-900 max-sm:text-2xl">
                    @lang('shop::app.customers.signup-form.page-title')
                </h1>

				<p class="mt-2 text-sm text-zinc-500">
                    @lang('shop::app.customers.signup-form.form-signup-text')
                </p>
			</div>

            <div class="mt-8">
                <x-shop::form :action="route('shop.customers.register.store')">
                    {!! view_render_event('bagisto.shop.customers.signup_form_controls.before') !!}

                    <!-- First Name -->
                    <x-shop::form.control-group>
                        <x-shop::form.control-group.label class="required font-medium text-zinc-700">
                            @lang('shop::app.customers.signup-form.first-name')
                        </x-shop::form.control-group.label>

                        <x-shop::form.control-group.control
                            type="text"
                            class="rounded-xl px-4 py-3 border-zinc-300 focus:border-[#c2955e] focus:ring-1 focus:ring-[#c2955e]"
                            name="first_name"
                            rules="required"
                            :value="old('first_name')"
                            :label="trans('shop::app.customers.signup-form.first-name')"
                            :placeholder="trans('shop::app.customers.signup-form.first-name')"
                            :aria-label="trans('shop::app.customers.signup-form.first-name')"
                            aria-required="true"
                        />

                        <x-shop::form.control-group.error control-name="first_name" />
                    </x-shop::form.control-group>

                    {!! view_render_event('bagisto.shop.customers.signup_form.first_name.after') !!}

                    <!-- Last Name -->
                    <x-shop::form.control-group>
                        <x-shop::form.control-group.label class="required font-medium text-zinc-700">
                            @lang('shop::app.customers.signup-form.last-name')
                        </x-shop::form.control-group.label>

                        <x-shop::form.control-group.control
                            type="text"
                            class="rounded-xl px-4 py-3 border-zinc-300 focus:border-[#c2955e] focus:ring-1 focus:ring-[#c2955e]"
                            name="last_name"
                            rules="required"
                            :value="old('last_name')"
                            :label="trans('shop::app.customers.signup-form.last-name')"
                            :placeholder="trans('shop::app.customers.signup-form.last-name')"
                            :aria-label="trans('shop::app.customers.signup-form.last-name')"
                            aria-required="true"
                        />

                        <x-shop::form.control-group.error control-name="last_name" />
                    </x-shop::form.control-group>

                    {!! view_render_event('bagisto.shop.customers.signup_form.last_name.after') !!}

                    <!-- Email -->
                    <x-shop::form.control-group>
                        <x-shop::form.control-group.label class="required font-medium text-zinc-700">
                            @lang('shop::app.customers.signup-form.email')
                        </x-shop::form.control-group.label>

                        <x-shop::form.control-group.control
                            type="email"
                            class="rounded-xl px-4 py-3 border-zinc-300 focus:border-[#c2955e] focus:ring-1 focus:ring-[#c2955e]"
                            name="email"
                            rules="required|email"
                            :value="old('email')"
                            :label="trans('shop::app.customers.signup-form.email')"
                            placeholder="email@example.com"
                            :aria-label="trans('shop::app.customers.signup-form.email')"
                            aria-required="true"
                        />

                        <x-shop::form.control-group.error control-name="email" />
                    </x-shop::form.control-group>

                    {!! view_render_event('bagisto.shop.customers.signup_form.email.after') !!}

                    <!-- Password -->
                    <x-shop::form.control-group class="mb-4">
                        <x-shop::form.control-group.label class="required font-medium text-zinc-700">
                            @lang('shop::app.customers.signup-form.password')
                        </x-shop::form.control-group.label>

                        <x-shop::form.control-group.control
                            type="password"
                            class="rounded-xl px-4 py-3 border-zinc-300 focus:border-[#c2955e] focus:ring-1 focus:ring-[#c2955e]"
                            name="password"
                            rules="required|min:6"
                            :value="old('password')"
                            :label="trans('shop::app.customers.signup-form.password')"
                            :placeholder="trans('shop::app.customers.signup-form.password')"
                            ref="password"
                            :aria-label="trans('shop::app.customers.signup-form.password')"
                            aria-required="true"
                        />

                        <x-shop::form.control-group.error control-name="password" />
                    </x-shop::form.control-group>

                    {!! view_render_event('bagisto.shop.customers.signup_form.password.after') !!}

                    <!-- Confirm Password -->
                    <x-shop::form.control-group class="mb-4">
                        <x-shop::form.control-group.label class="font-medium text-zinc-700">
                            @lang('shop::app.customers.signup-form.confirm-pass')
                        </x-shop::form.control-group.label>

                        <x-shop::form.control-group.control
                            type="password"
                            class="rounded-xl px-4 py-3 border-zinc-300 focus:border-[#c2955e] focus:ring-1 focus:ring-[#c2955e]"
                            name="password_confirmation"
                            rules="confirmed:@password"
                            value=""
                            :label="trans('shop::app.customers.signup-form.password')"
                            :placeholder="trans('shop::app.customers.signup-form.confirm-pass')"
                            :aria-label="trans('shop::app.customers.signup-form.confirm-pass')"
                            aria-required="true"
                        />

                        <x-shop::form.control-group.error control-name="password_confirmation" />
                    </x-shop::form.control-group>

                    {!! view_render_event('bagisto.shop.customers.signup_form.password_confirmation.after') !!}

                    <!-- Captcha -->
                    @if (core()->getConfigData('customer.captcha.credentials.status'))
                        <x-shop::form.control-group class="mt-4">
                            {!! \Webkul\Customer\Facades\Captcha::render() !!}

                            <x-shop::form.control-group.error control-name="recaptcha_token" />
                        </x-shop::form.control-group>
                    @endif

                    <!-- Subscribed Button -->
                    @if (core()->getConfigData('customer.settings.create_new_account_options.news_letter'))
                        <div class="mb-4 flex select-none items-center gap-2">
                            <input
                                type="checkbox"
                                name="is_subscribed"
                                id="is-subscribed"
                                class="peer hidden"
                            />

                            <label
                                class="icon-uncheck peer-checked:icon-check-box cursor-pointer text-xl text-navyBlue peer-checked:text-navyBlue"
                                for="is-subscribed"
                            ></label>

                            <label
                                class="cursor-pointer select-none text-sm text-zinc-600"
                                for="is-subscribed"
                            >
                                @lang('shop::app.customers.signup-form.subscribe-to-newsletter')
                            </label>
                        </div>
                    @endif

                    {!! view_render_event('bagisto.shop.customers.signup_form.newsletter_subscription.after') !!}

                    @if(
                        core()->getConfigData('general.gdpr.settings.enabled')
                        && core()->getConfigData('general.gdpr.agreement.enabled')
                    )
                        <div class="mb-4 flex select-none items-center gap-2">
                            <x-shop::form.control-group.control
                                type="checkbox"
                                name="agreement"
                                id="agreement"
                                value="0"
                                rules="required"
                                for="agreement"
                            />

                            <label
                                class="cursor-pointer select-none text-sm text-zinc-600"
                                for="agreement"
                                v-pre
                            >
                                {{ core()->getConfigData('general.gdpr.agreement.agreement_label') }}
                            </label>

                            @if (core()->getConfigData('general.gdpr.agreement.agreement_content'))
                                <span
                                    class="cursor-pointer text-sm font-semibold text-navyBlue hover:underline"
                                    @click="$refs.termsModal.open()"
                                >
                                    @lang('shop::app.customers.signup-form.click-here')
                                </span>
                            @endif
                        </div>

                        <x-shop::form.control-group.error control-name="agreement" />
                    @endif

                    <div class="mt-6">
                        <!-- Save Button -->
                        <button
                            class="primary-button !w-full !max-w-full rounded-xl py-3.5 text-center text-base font-semibold shadow-sm transition-all hover:shadow-md"
                            type="submit"
                        >
                            @lang('shop::app.customers.signup-form.button-title')
                        </button>
                    </div>

                    {!! view_render_event('bagisto.shop.customers.signup_form_controls.after') !!}

                </x-shop::form>
            </div>

			<div class="mt-8 border-t border-zinc-100 pt-6 text-center text-sm font-medium text-zinc-600">
                @lang('shop::app.customers.signup-form.account-exists')

                <a class="font-semibold text-navyBlue hover:underline ml-1"
                    href="{{ route('shop.customer.session.index') }}"
                >
                    @lang('shop::app.customers.signup-form.sign-in-button')
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

    <!-- Terms & Conditions Modal -->
    <x-shop::modal ref="termsModal">
        <x-slot:toggle></x-slot>

        <x-slot:header class="!p-5">
            <p>@lang('shop::app.customers.signup-form.terms-conditions')</p>
        </x-slot>

        <x-slot:content class="!p-5">
            <div class="max-h-[500px] overflow-auto">
                {!! core()->getConfigData('general.gdpr.agreement.agreement_content') !!}
            </div>
        </x-slot>
    </x-shop::modal>
</x-shop::layouts>
