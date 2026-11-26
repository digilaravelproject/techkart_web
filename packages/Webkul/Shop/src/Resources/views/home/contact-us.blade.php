<!-- Page Layout -->
<x-shop::layouts>
    <!-- Page Title -->
    <x-slot:title>
        @lang('shop::app.home.contact.title')
    </x-slot>

    <!-- Hero Header -->
    <div class="border-b border-gray-200 bg-slate-50 py-10 text-center max-md:py-7">
        <h1 class="font-dmserif text-4xl text-slate-900 max-md:text-2xl">
            @lang('shop::app.home.contact.title')
        </h1>

        <div class="mt-2.5 flex items-center justify-center gap-1.5 text-xs text-slate-500">
            <a href="{{ route('shop.home.index') }}" class="transition hover:text-slate-900">Home</a>
            <span>›</span>
            <span>@lang('shop::app.home.contact.title')</span>
        </div>
    </div>

    <div class="container my-12 max-w-[1240px] px-6 max-md:my-8 max-md:px-4">
        <div class="grid grid-cols-1 items-start gap-12 lg:grid-cols-12">
            <!-- Left Column: Contact Information -->
            <div class="space-y-8 lg:col-span-5">
                <div>
                    <h2 class="text-2xl font-bold tracking-tight text-slate-900">
                        Get In Touch
                    </h2>

                    <p class="mt-3 text-sm leading-relaxed text-slate-600">
                        We love connecting with our community and are always delighted to hear from you! Whether you have a question about a specific product before placing an order, need assistance tracking an existing shipment, or simply wish to share feedback about your shopping experience on Benzoo, our team is ready to assist you with warmth and clarity.
                    </p>
                </div>

                <!-- Contact Details Cards -->
                <div class="space-y-4">
                    <!-- Email Card -->
                    <div class="flex items-start gap-4 rounded-xl border border-slate-200/80 bg-white p-4 shadow-sm transition hover:shadow-md">
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>

                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Customer Support Email</p>
                            <a href="mailto:benzoostore1@gmail.com" class="mt-0.5 inline-block text-sm font-semibold text-slate-900 hover:text-blue-600">
                                benzoostore1@gmail.com
                            </a>
                        </div>
                    </div>

                    <!-- Phone & WhatsApp Card -->
                    <div class="flex items-start gap-4 rounded-xl border border-slate-200/80 bg-white p-4 shadow-sm transition hover:shadow-md">
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                        </div>

                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Phone & WhatsApp Assistance</p>
                            <div class="mt-0.5 flex flex-wrap items-center gap-2">
                                <a href="tel:+918655160555" class="text-sm font-semibold text-slate-900 hover:text-blue-600">
                                    +91 8655160555
                                </a>
                                <a
                                    href="https://wa.me/918655160555"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2 py-0.5 text-[11px] font-medium text-emerald-800 transition hover:bg-emerald-200"
                                >
                                    WhatsApp
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Address Card -->
                    <div class="flex items-start gap-4 rounded-xl border border-slate-200/80 bg-white p-4 shadow-sm transition hover:shadow-md">
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-purple-50 text-purple-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>

                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Registered Business Address</p>
                            <p class="mt-0.5 text-sm leading-relaxed text-slate-800">
                                Motilal nagar phase 3 chawl no 33/14 goregoan west mumbai, maharashtra, 400104
                            </p>
                        </div>
                    </div>

                    <!-- Working Hours Card -->
                    <div class="flex items-start gap-4 rounded-xl border border-slate-200/80 bg-white p-4 shadow-sm transition hover:shadow-md">
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>

                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Support Working Hours</p>
                            <p class="mt-0.5 text-sm font-semibold text-slate-900">
                                Monday to Saturday, 10:00 AM – 6:00 PM IST
                            </p>
                        </div>
                    </div>

                    <!-- Response Time Alert -->
                    <div class="rounded-xl border border-blue-100 bg-blue-50/70 p-4 text-xs leading-relaxed text-blue-900">
                        <span class="font-semibold">⚡ Typical Response Time:</span> We strive to respond to all emails and messages within 24 business hours.
                    </div>
                </div>
            </div>

            <!-- Right Column: Contact Form -->
            <div class="rounded-2xl border border-slate-200 bg-white p-8 shadow-sm lg:col-span-7 max-md:p-5">
                <h3 class="text-xl font-bold tracking-tight text-slate-900">
                    Send Us a Message
                </h3>
                <p class="mt-1 text-xs text-slate-500">
                    Fill out the form below and we will get back to you as soon as possible.
                </p>

                <div
                    id="contact-us-form"
                    class="mt-6"
                >
                    <!-- Contact Form -->
                    <x-shop::form :action="route('shop.home.contact_us.send_mail')">
                        <!-- Name -->
                        <x-shop::form.control-group>
                            <x-shop::form.control-group.label class="required">
                                @lang('shop::app.home.contact.name')
                            </x-shop::form.control-group.label>

                            <x-shop::form.control-group.control
                                type="text"
                                class="px-5 py-3.5 text-sm"
                                name="name"
                                rules="required"
                                :value="old('name')"
                                :label="trans('shop::app.home.contact.name')"
                                :placeholder="trans('shop::app.home.contact.name')"
                                :aria-label="trans('shop::app.home.contact.name')"
                                aria-required="true"
                            />

                            <x-shop::form.control-group.error control-name="name" />
                        </x-shop::form.control-group>

                        <!-- Email & Phone Grid -->
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <!-- Email -->
                            <x-shop::form.control-group>
                                <x-shop::form.control-group.label class="required">
                                    @lang('shop::app.home.contact.email')
                                </x-shop::form.control-group.label>

                                <x-shop::form.control-group.control
                                    type="email"
                                    class="px-5 py-3.5 text-sm"
                                    name="email"
                                    rules="required|email"
                                    :value="old('email')"
                                    :label="trans('shop::app.home.contact.email')"
                                    :placeholder="trans('shop::app.home.contact.email')"
                                    :aria-label="trans('shop::app.home.contact.email')"
                                    aria-required="true"
                                />

                                <x-shop::form.control-group.error control-name="email" />
                            </x-shop::form.control-group>

                            <!-- Contact -->
                            <x-shop::form.control-group>
                                <x-shop::form.control-group.label>
                                    @lang('shop::app.home.contact.phone-number')
                                </x-shop::form.control-group.label>

                                <x-shop::form.control-group.control
                                    type="text"
                                    class="px-5 py-3.5 text-sm"
                                    name="contact"
                                    rules="phone"
                                    :value="old('contact')"
                                    :label="trans('shop::app.home.contact.phone-number')"
                                    :placeholder="trans('shop::app.home.contact.phone-number')"
                                    :aria-label="trans('shop::app.home.contact.phone-number')"
                                />

                                <x-shop::form.control-group.error control-name="contact" />
                            </x-shop::form.control-group>
                        </div>

                        <!-- Message -->
                        <x-shop::form.control-group>
                            <x-shop::form.control-group.label class="required">
                                @lang('shop::app.home.contact.desc')
                            </x-shop::form.control-group.label>

                            <x-shop::form.control-group.control
                                type="textarea"
                                class="px-5 py-3.5 text-sm"
                                name="message"
                                rules="required"
                                :label="trans('shop::app.home.contact.message')"
                                :placeholder="trans('shop::app.home.contact.describe-here')"
                                :aria-label="trans('shop::app.home.contact.message')"
                                aria-required="true"
                                rows="5"
                            />

                            <x-shop::form.control-group.error control-name="message" />
                        </x-shop::form.control-group>

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
                                class="primary-button w-full rounded-xl py-3.5 text-center text-sm font-semibold sm:w-auto sm:px-10"
                                type="submit"
                            >
                                @lang('shop::app.home.contact.submit')
                            </button>
                        </div>
                    </x-shop::form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        {!! \Webkul\Customer\Facades\Captcha::renderJS() !!}
    @endpush
</x-shop::layouts>
