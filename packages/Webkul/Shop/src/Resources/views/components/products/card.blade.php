<v-product-card
    {{ $attributes }}
    :product="product"
>
</v-product-card>

@pushOnce('styles')
    <style>
        .ecom-product-card {
            display: flex;
            flex-direction: column;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            overflow: hidden;
            transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
            height: 100%;
        }
        .ecom-product-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 14px 28px rgba(15, 23, 42, 0.08);
            border-color: #cbd5e1;
        }
        .ecom-card-media {
            position: relative;
            width: 100%;
            aspect-ratio: 4 / 4.4;
            background: #f8fafc;
            overflow: hidden;
        }
        .ecom-card-media img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: top center;
            transition: transform 0.35s ease;
            display: block;
        }
        .ecom-product-card:hover .ecom-card-media img {
            transform: scale(1.04);
        }
        .ecom-card-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            padding: 3px 9px;
            border-radius: 6px;
            font-size: 10.5px;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            color: #ffffff;
            z-index: 2;
        }
        .ecom-card-badge.is-sale {
            background: #dc2626;
        }
        .ecom-card-badge.is-new {
            background: #0f172a;
        }
        .ecom-card-quick-actions {
            position: absolute;
            top: 10px;
            right: 10px;
            display: flex;
            flex-direction: column;
            gap: 6px;
            z-index: 2;
        }
        .ecom-card-icon-btn {
            width: 32px;
            height: 32px;
            border-radius: 9999px;
            background: rgba(255, 255, 255, 0.92);
            border: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            color: #334155;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            transition: all 0.2s ease;
        }
        .ecom-card-icon-btn:hover {
            background: #0f172a;
            color: #ffffff;
            border-color: #0f172a;
        }
        .ecom-card-body {
            display: flex;
            flex-direction: column;
            flex: 1 1 auto;
            padding: 14px;
            gap: 8px;
        }
        .ecom-card-title {
            font-size: 14px;
            font-weight: 500;
            line-height: 1.4;
            color: #0f172a;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            min-height: 39px;
            margin: 0;
            text-decoration: none;
        }
        .ecom-card-price {
            display: flex;
            flex-wrap: wrap;
            align-items: baseline;
            gap: 6px;
            font-size: 15px;
            font-weight: 700;
            color: #0f172a;
        }
        .ecom-card-price .price-label {
            font-size: 11px;
            font-weight: 500;
            color: #64748b;
        }
        .ecom-card-price .regular-price {
            font-size: 12.5px;
            font-weight: 400;
            color: #94a3b8;
        }
        .ecom-card-price .final-price {
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
        }
        .ecom-card-cta {
            margin-top: auto;
            padding-top: 6px;
        }
        .ecom-card-cart-btn {
            width: 100%;
            padding: 9px 12px;
            border-radius: 9px;
            border: 1px solid #0f172a;
            background: #ffffff;
            color: #0f172a;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .ecom-card-cart-btn:hover:not(:disabled) {
            background: #0f172a;
            color: #ffffff;
        }
        .ecom-card-cart-btn:disabled {
            opacity: 0.45;
            cursor: not-allowed;
        }
        @media (max-width: 640px) {
            .ecom-card-body {
                padding: 10px;
                gap: 6px;
            }
            .ecom-card-title {
                font-size: 12.5px;
                min-height: 35px;
            }
            .ecom-card-price .final-price {
                font-size: 14px;
            }
            .ecom-card-cart-btn {
                padding: 7px 10px;
                font-size: 12px;
            }
        }
    </style>
@endPushOnce

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-product-card-template"
    >
        <!-- Grid Card -->
        <div
            class="ecom-product-card group"
            v-if="mode != 'list'"
        >
            <div class="ecom-card-media">
                {!! view_render_event('bagisto.shop.components.products.card.image.before') !!}

                <!-- Product Image -->
                <a
                    :href="'{{ route('shop.product_or_category.index', ':slug') }}'.replace(':slug', product.url_key)"
                    :aria-label="product.name"
                    class="block h-full w-full"
                >
                    <x-shop::media.images.lazy
                        class="h-full w-full bg-zinc-100 object-cover"
                        ::src="product.base_image.medium_image_url"
                        ::srcset="`
                            ${product.base_image.small_image_url} 150w,
                            ${product.base_image.medium_image_url} 300w,
                        `"
                        sizes="(max-width: 768px) 150px, (max-width: 1200px) 300px, 600px"
                        ::key="product.id"
                        ::index="product.id"
                        width="291"
                        height="320"
                        ::alt="product.base_image.alt"
                    />
                </a>

                {!! view_render_event('bagisto.shop.components.products.card.image.after') !!}

                <!-- Product Ratings -->
                {!! view_render_event('bagisto.shop.components.products.card.average_ratings.before') !!}

                @if (core()->getConfigData('catalog.products.review.summary') == 'star_counts')
                    <x-shop::products.ratings
                        class="absolute bottom-2 items-center !border-white bg-white/90 !px-2 !py-0.5 text-xs shadow-sm ltr:left-2 rtl:right-2"
                        ::average="product.ratings.average"
                        ::total="product.ratings.total"
                        ::rating="false"
                        v-if="product.ratings.total"
                    />
                @else
                    <x-shop::products.ratings
                        class="absolute bottom-2 items-center !border-white bg-white/90 !px-2 !py-0.5 text-xs shadow-sm ltr:left-2 rtl:right-2"
                        ::average="product.ratings.average"
                        ::total="product.reviews.total"
                        ::rating="false"
                        v-if="product.reviews.total"
                    />
                @endif

                {!! view_render_event('bagisto.shop.components.products.card.average_ratings.after') !!}

                <!-- Product Sale / New Badge -->
                <span
                    class="ecom-card-badge is-sale"
                    v-if="product.on_sale"
                >
                    @lang('shop::app.components.products.card.sale')
                </span>

                <span
                    class="ecom-card-badge is-new"
                    v-else-if="product.is_new"
                >
                    @lang('shop::app.components.products.card.new')
                </span>

                <!-- Floating Wishlist & Compare -->
                <div class="ecom-card-quick-actions">
                    {!! view_render_event('bagisto.shop.components.products.card.wishlist_option.before') !!}

                    @if (core()->getConfigData('customer.settings.wishlist.wishlist_option'))
                        <span
                            class="ecom-card-icon-btn"
                            role="button"
                            aria-label="@lang('shop::app.components.products.card.add-to-wishlist')"
                            tabindex="0"
                            :class="product.is_wishlist ? 'icon-heart-fill text-red-500' : 'icon-heart'"
                            @click="addToWishlist()"
                        >
                        </span>
                    @endif

                    {!! view_render_event('bagisto.shop.components.products.card.wishlist_option.after') !!}

                    {!! view_render_event('bagisto.shop.components.products.card.compare_option.before') !!}

                    @if (core()->getConfigData('catalog.products.settings.compare_option'))
                        <span
                            class="icon-compare ecom-card-icon-btn"
                            role="button"
                            aria-label="@lang('shop::app.components.products.card.add-to-compare')"
                            tabindex="0"
                            @click="addToCompare(product.id)"
                        >
                        </span>
                    @endif

                    {!! view_render_event('bagisto.shop.components.products.card.compare_option.after') !!}
                </div>
            </div>

            <!-- Product Information Section -->
            <div class="ecom-card-body">
                {!! view_render_event('bagisto.shop.components.products.card.name.before') !!}

                <a
                    :href="'{{ route('shop.product_or_category.index', ':slug') }}'.replace(':slug', product.url_key)"
                    class="ecom-card-title"
                >
                    @{{ product.name }}
                </a>

                {!! view_render_event('bagisto.shop.components.products.card.name.after') !!}

                <!-- Pricing -->
                {!! view_render_event('bagisto.shop.components.products.card.price.before') !!}

                <div
                    class="ecom-card-price"
                    v-html="product.price_html"
                >
                </div>

                {!! view_render_event('bagisto.shop.components.products.card.price.after') !!}

                <!-- Product Actions Section -->
                <div class="ecom-card-cta">
                    @if (core()->getConfigData('sales.checkout.shopping_cart.cart_page'))
                        {!! view_render_event('bagisto.shop.components.products.card.add_to_cart.before') !!}

                        <button
                            class="ecom-card-cart-btn"
                            :disabled="! product.is_saleable || isAddingToCart"
                            @click="addToCart()"
                        >
                            @lang('shop::app.components.products.card.add-to-cart')
                        </button>

                        {!! view_render_event('bagisto.shop.components.products.card.add_to_cart.after') !!}
                    @endif
                </div>
            </div>
        </div>

        <!-- List Card -->
        <div
            class="relative flex max-w-max grid-cols-2 gap-4 overflow-hidden rounded max-sm:flex-wrap"
            v-else
        >
            <div class="group relative max-h-[258px] max-w-[250px] overflow-hidden">

                {!! view_render_event('bagisto.shop.components.products.card.image.before') !!}

                <a :href="'{{ route('shop.product_or_category.index', ':slug') }}'.replace(':slug', product.url_key)">
                    <x-shop::media.images.lazy
                        class="after:content-[' '] relative min-w-[250px] bg-zinc-100 transition-all duration-300 after:block after:pb-[calc(100%+9px)] group-hover:scale-105"
                        ::src="product.base_image.medium_image_url"
                        ::key="product.id"
                        ::index="product.id"
                        width="291"
                        height="300"
                        ::alt="product.base_image.alt"
                    />
                </a>

                {!! view_render_event('bagisto.shop.components.products.card.image.after') !!}

                <div class="action-items bg-black">
                    <p
                        class="absolute top-5 inline-block rounded-[44px] bg-red-500 px-2.5 text-sm text-white ltr:left-5 max-sm:ltr:left-2 rtl:right-5"
                        v-if="product.on_sale"
                    >
                        @lang('shop::app.components.products.card.sale')
                    </p>

                    <p
                        class="absolute top-5 inline-block rounded-[44px] bg-navyBlue px-2.5 text-sm text-white ltr:left-5 max-sm:ltr:left-2 rtl:right-5"
                        v-else-if="product.is_new"
                    >
                        @lang('shop::app.components.products.card.new')
                    </p>

                    <div class="opacity-0 transition-all duration-300 group-hover:bottom-0 group-hover:opacity-100 max-sm:opacity-100">

                        {!! view_render_event('bagisto.shop.components.products.card.wishlist_option.before') !!}

                        @if (core()->getConfigData('customer.settings.wishlist.wishlist_option'))
                            <span
                                class="absolute top-5 flex h-[30px] w-[30px] cursor-pointer items-center justify-center rounded-md bg-white text-2xl ltr:right-5 rtl:left-5"
                                role="button"
                                aria-label="@lang('shop::app.components.products.card.add-to-wishlist')"
                                tabindex="0"
                                :class="product.is_wishlist ? 'icon-heart-fill text-red-600' : 'icon-heart'"
                                @click="addToWishlist()"
                            >
                            </span>
                        @endif

                        {!! view_render_event('bagisto.shop.components.products.card.wishlist_option.after') !!}

                        {!! view_render_event('bagisto.shop.components.products.card.compare_option.before') !!}

                        @if (core()->getConfigData('catalog.products.settings.compare_option'))
                            <span
                                class="icon-compare absolute top-16 flex h-[30px] w-[30px] cursor-pointer items-center justify-center rounded-md bg-white text-2xl ltr:right-5 rtl:left-5"
                                role="button"
                                aria-label="@lang('shop::app.components.products.card.add-to-compare')"
                                tabindex="0"
                                @click="addToCompare(product.id)"
                            >
                            </span>
                        @endif

                        {!! view_render_event('bagisto.shop.components.products.card.compare_option.after') !!}
                    </div>
                </div>
            </div>

            <div class="grid content-start gap-4">

                {!! view_render_event('bagisto.shop.components.products.card.name.before') !!}

                <p class="text-base">
                    @{{ product.name }}
                </p>

                {!! view_render_event('bagisto.shop.components.products.card.name.after') !!}

                {!! view_render_event('bagisto.shop.components.products.card.price.before') !!}

                <div
                    class="flex flex-wrap items-center gap-x-2.5 gap-y-0.5 text-lg font-semibold"
                    v-html="product.price_html"
                >
                </div>

                {!! view_render_event('bagisto.shop.components.products.card.price.after') !!}

                <!-- Needs to implement that in future -->
                <div class="flex hidden gap-4">
                    <span class="block h-[30px] w-[30px] rounded-full bg-[#B5DCB4]">
                    </span>

                    <span class="block h-[30px] w-[30px] rounded-full bg-zinc-500">
                    </span>
                </div>

                {!! view_render_event('bagisto.shop.components.products.card.average_ratings.before') !!}

                <p class="text-sm text-zinc-500">
                    <template  v-if="! product.ratings.total">
                        <p class="text-sm text-zinc-500">
                            @lang('shop::app.components.products.card.review-description')
                        </p>
                    </template>

                    <template v-else>
                        @if (core()->getConfigData('catalog.products.review.summary') == 'star_counts')
                            <x-shop::products.ratings
                                ::average="product.ratings.average"
                                ::total="product.ratings.total"
                                ::rating="false"
                            />
                        @else
                            <x-shop::products.ratings
                                ::average="product.ratings.average"
                                ::total="product.reviews.total"
                                ::rating="false"
                            />
                        @endif
                    </template>
                </p>

                {!! view_render_event('bagisto.shop.components.products.card.average_ratings.after') !!}

                @if (core()->getConfigData('sales.checkout.shopping_cart.cart_page'))

                    {!! view_render_event('bagisto.shop.components.products.card.add_to_cart.before') !!}

                    <x-shop::button
                        class="primary-button whitespace-nowrap px-8 py-2.5"
                        :title="trans('shop::app.components.products.card.add-to-cart')"
                        ::loading="isAddingToCart"
                        ::disabled="! product.is_saleable || isAddingToCart"
                        @click="addToCart()"
                    />

                    {!! view_render_event('bagisto.shop.components.products.card.add_to_cart.after') !!}

                @endif
            </div>
        </div>
    </script>

    <script type="module">
        app.component('v-product-card', {
            template: '#v-product-card-template',

            props: ['mode', 'product'],

            data() {
                return {
                    isCustomer: '{{ auth()->guard('customer')->check() }}',

                    isAddingToCart: false,
                }
            },

            methods: {
                addToWishlist() {
                    if (this.isCustomer) {
                        this.$axios.post(`{{ route('shop.api.customers.account.wishlist.store') }}`, {
                                product_id: this.product.id
                            })
                            .then(response => {
                                this.product.is_wishlist = ! this.product.is_wishlist;

                                this.$emitter.emit('add-flash', { type: 'success', message: response.data.data.message });
                            })
                            .catch(error => {});
                        } else {
                            window.location.href = "{{ route('shop.customer.session.index')}}";
                        }
                },

                addToCompare(productId) {
                    /**
                     * This will handle for customers.
                     */
                    if (this.isCustomer) {
                        this.$axios.post('{{ route("shop.api.compare.store") }}', {
                                'product_id': productId
                            })
                            .then(response => {
                                this.$emitter.emit('add-flash', { type: 'success', message: response.data.data.message });
                            })
                            .catch(error => {
                                if ([400, 422].includes(error.response.status)) {
                                    this.$emitter.emit('add-flash', { type: 'warning', message: error.response.data.data.message });

                                    return;
                                }

                                this.$emitter.emit('add-flash', { type: 'error', message: error.response.data.message});
                            });

                        return;
                    }

                    /**
                     * This will handle for guests.
                     */
                    let items = this.getStorageValue() ?? [];

                    if (items.length) {
                        if (! items.includes(productId)) {
                            items.push(productId);

                            localStorage.setItem('compare_items', JSON.stringify(items));

                            this.$emitter.emit('add-flash', { type: 'success', message: "@lang('shop::app.components.products.card.add-to-compare-success')" });
                        } else {
                            this.$emitter.emit('add-flash', { type: 'warning', message: "@lang('shop::app.components.products.card.already-in-compare')" });
                        }
                    } else {
                        localStorage.setItem('compare_items', JSON.stringify([productId]));

                        this.$emitter.emit('add-flash', { type: 'success', message: "@lang('shop::app.components.products.card.add-to-compare-success')" });

                    }
                },

                getStorageValue(key) {
                    let value = localStorage.getItem('compare_items');

                    if (! value) {
                        return [];
                    }

                    return JSON.parse(value);
                },

                addToCart() {
                    this.isAddingToCart = true;

                    this.$axios.post('{{ route("shop.api.checkout.cart.store") }}', {
                            'quantity': 1,
                            'product_id': this.product.id,
                        })
                        .then(response => {
                            if (response.data.message) {
                                this.$emitter.emit('update-mini-cart', response.data.data );

                                this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });
                            } else {
                                this.$emitter.emit('add-flash', { type: 'warning', message: response.data.data.message });
                            }

                            this.isAddingToCart = false;
                        })
                        .catch(error => {
                            this.$emitter.emit('add-flash', { type: 'error', message: error.response.data.message });

                            if (error.response.data.redirect_uri) {
                                window.location.href = error.response.data.redirect_uri;
                            }

                            this.isAddingToCart = false;
                        });
                },
            },
        });
    </script>
@endpushOnce
