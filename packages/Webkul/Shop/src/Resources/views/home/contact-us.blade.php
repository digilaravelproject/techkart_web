<!-- Page Layout -->
<x-shop::layouts>
    <!-- Page Title -->
    <x-slot:title>
        @lang('shop::app.home.contact.title')
    </x-slot>

    @push('styles')
    <style>
        .contact-page-hero {
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            padding: 36px 20px;
            text-align: center;
        }
        .contact-page-hero h1 {
            font-family: 'DM Serif Display', Georgia, serif;
            font-size: 36px;
            font-weight: 400;
            color: #0f172a;
            margin: 0 0 8px;
            line-height: 1.2;
        }
        .contact-breadcrumb {
            font-size: 13px;
            color: #94a3b8;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }
        .contact-breadcrumb a {
            color: #64748b;
            text-decoration: none;
            transition: color 0.2s;
        }
        .contact-breadcrumb a:hover {
            color: #0f172a;
        }
        .contact-wrapper {
            max-width: 1200px;
            margin: 0 auto;
            padding: 48px 24px 72px;
        }
        .contact-layout-grid {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 48px;
        }
        .contact-info-pane {
            flex: 0 0 44%;
            max-width: 480px;
        }
        .contact-form-pane {
            flex: 1 1 52%;
            max-width: 640px;
        }
        .contact-section-title {
            font-size: 24px;
            font-weight: 700;
            color: #0f172a;
            margin: 0 0 10px;
            letter-spacing: -0.01em;
        }
        .contact-section-desc {
            font-size: 14.5px;
            line-height: 1.7;
            color: #475569;
            margin: 0 0 24px;
        }
        .contact-cards-stack {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .contact-detail-card {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 16px 18px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .contact-detail-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.06);
        }
        .contact-icon-bubble {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .contact-icon-bubble svg {
            width: 22px;
            height: 22px;
        }
        .contact-icon-bubble.email { background: #eff6ff; color: #2563eb; }
        .contact-icon-bubble.phone { background: #ecfdf5; color: #059669; }
        .contact-icon-bubble.address { background: #faf5ff; color: #7c3aed; }
        .contact-icon-bubble.hours { background: #fffbeb; color: #d97706; }
        .contact-card-label {
            font-size: 11.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #94a3b8;
            margin: 0 0 3px;
        }
        .contact-card-value {
            font-size: 14.5px;
            font-weight: 600;
            color: #0f172a;
            margin: 0;
            line-height: 1.5;
        }
        .contact-card-value a {
            color: #0f172a;
            text-decoration: none;
            transition: color 0.2s;
        }
        .contact-card-value a:hover {
            color: #2563eb;
        }
        .whatsapp-tag {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: #25D366;
            color: #ffffff !important;
            padding: 3px 10px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
            margin-left: 8px;
            vertical-align: middle;
            transition: opacity 0.2s;
        }
        .whatsapp-tag:hover {
            opacity: 0.9;
        }
        .contact-alert-banner {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 12px;
            padding: 14px 18px;
            font-size: 13.5px;
            color: #166534;
            line-height: 1.6;
            margin-top: 16px;
        }
        .contact-form-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 36px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
        }
        .contact-form-card h3 {
            font-size: 20px;
            font-weight: 700;
            color: #0f172a;
            margin: 0 0 6px;
        }
        .contact-form-card p.subtitle {
            font-size: 13.5px;
            color: #64748b;
            margin: 0 0 24px;
        }
        .form-row-two-col {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }
        .contact-submit-btn {
            background: #0f172a;
            color: #ffffff;
            font-size: 14.5px;
            font-weight: 600;
            padding: 13px 28px;
            border-radius: 10px;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .contact-submit-btn:hover {
            background: #1e293b;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.15);
        }
        @media (max-width: 900px) {
            .contact-layout-grid {
                flex-direction: column;
                gap: 36px;
            }
            .contact-info-pane,
            .contact-form-pane {
                flex: 1 1 100%;
                max-width: 100%;
                width: 100%;
            }
            .contact-wrapper {
                padding: 28px 16px 56px;
            }
            .contact-form-card {
                padding: 22px 18px;
            }
            .form-row-two-col {
                grid-template-columns: 1fr;
                gap: 0;
            }
            .contact-page-hero h1 {
                font-size: 28px;
            }
            .contact-submit-btn {
                width: 100%;
            }
        }
    </style>
    @endpush

    <!-- Hero Header -->
    <div class="contact-page-hero">
        <h1>@lang('shop::app.home.contact.title')</h1>
        <div class="contact-breadcrumb">
            <a href="{{ route('shop.home.index') }}">Home</a>
            <span>›</span>
            <span>@lang('shop::app.home.contact.title')</span>
        </div>
    </div>

    <!-- Main Content Container -->
    <div class="contact-wrapper">
        <div class="contact-layout-grid">
            <!-- Left Column: Contact Information (Desktop: Left, Mobile: Top) -->
            <div class="contact-info-pane">
                <h2 class="contact-section-title">Get in Touch With Us</h2>
                <p class="contact-section-desc">
                    We love connecting with our community and are always delighted to hear from you! Whether you have a question about a specific product before placing an order, need assistance tracking an existing shipment, or simply wish to share feedback about your shopping experience on Benzoo, our team is ready to assist you with warmth and clarity.
                </p>

                <div class="contact-cards-stack">
                    <!-- Email Card -->
                    <div class="contact-detail-card">
                        <div class="contact-icon-bubble email">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="contact-card-label">Customer Support Email</p>
                            <p class="contact-card-value">
                                <a href="mailto:benzoostore1@gmail.com">benzoostore1@gmail.com</a>
                            </p>
                        </div>
                    </div>

                    <!-- Phone & WhatsApp Card -->
                    <div class="contact-detail-card">
                        <div class="contact-icon-bubble phone">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="contact-card-label">Phone & WhatsApp Assistance</p>
                            <p class="contact-card-value">
                                <a href="tel:+918655160555">+91 8655160555</a>
                                <a
                                    href="https://wa.me/918655160555"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="whatsapp-tag"
                                >
                                    WhatsApp
                                </a>
                            </p>
                        </div>
                    </div>

                    <!-- Address Card -->
                    <div class="contact-detail-card">
                        <div class="contact-icon-bubble address">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="contact-card-label">Registered Business Address</p>
                            <p class="contact-card-value">
                                Motilal nagar phase 3 chawl no 33/14 goregoan west mumbai, maharashtra, 400104
                            </p>
                        </div>
                    </div>

                    <!-- Working Hours Card -->
                    <div class="contact-detail-card">
                        <div class="contact-icon-bubble hours">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="contact-card-label">Support Working Hours</p>
                            <p class="contact-card-value">
                                Monday to Saturday, 10:00 AM – 6:00 PM IST
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Typical Response Time Banner -->
                <div class="contact-alert-banner">
                    <strong>⚡ Typical Response Time:</strong> We strive to respond to all emails and messages within 24 business hours.
                </div>
            </div>

            <!-- Right Column: Contact Form (Desktop: Right, Mobile: Bottom) -->
            <div class="contact-form-pane">
                <div class="contact-form-card">
                    <h3>Send Us a Message</h3>
                    <p class="subtitle">Have a question or request? Fill out the form and our team will get back to you shortly.</p>

                    <div id="contact-us-form">
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

                            <!-- Email & Phone Row -->
                            <div class="form-row-two-col">
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

                                <!-- Phone -->
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
                                    class="contact-submit-btn"
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
    </div>

    @push('scripts')
        {!! \Webkul\Customer\Facades\Captcha::renderJS() !!}
    @endpush
</x-shop::layouts>
