@props([
    'src',
    'title' => '',
    'navigationLink' => '',
    'initialData' => null,
])

<v-products-carousel
    src="{{ $src }}"
    title="{{ $title }}"
    navigation-link="{{ $navigationLink }}"
    :initial-data='@json($initialData)'
>
    <x-shop::shimmer.products.carousel :navigation-link="$navigationLink ?? false" />
</v-products-carousel>

@pushOnce('styles')
    <style>
        .ecom-carousel-section {
            max-width: 1360px;
            margin: 56px auto 0;
            padding: 0 24px;
        }
        .ecom-carousel-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 24px;
        }
        .ecom-carousel-title {
            font-family: 'DM Serif Display', Georgia, serif;
            font-size: 30px;
            font-weight: 400;
            color: #0f172a;
            margin: 0;
            line-height: 1.2;
        }
        .ecom-carousel-controls {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .ecom-carousel-nav-btn {
            width: 38px;
            height: 38px;
            border-radius: 9999px;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            color: #0f172a;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .ecom-carousel-nav-btn:hover {
            background: #0f172a;
            border-color: #0f172a;
            color: #ffffff;
        }
        .ecom-carousel-viewall {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 18px;
            border-radius: 9999px;
            border: 1px solid #0f172a;
            color: #0f172a;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
            white-space: nowrap;
        }
        .ecom-carousel-viewall:hover {
            background: #0f172a;
            color: #ffffff;
        }
        .ecom-carousel-track {
            display: flex;
            gap: 20px;
            overflow-x: auto;
            scroll-behavior: smooth;
            scrollbar-width: none;
            -ms-overflow-style: none;
            padding: 4px 2px 12px;
        }
        .ecom-carousel-track::-webkit-scrollbar {
            display: none;
        }
        .ecom-carousel-card-slot {
            flex: 0 0 calc(25% - 15px);
            min-width: 240px;
        }
        @media (max-width: 1100px) {
            .ecom-carousel-card-slot {
                flex: 0 0 calc(33.333% - 14px);
                min-width: 220px;
            }
        }
        @media (max-width: 768px) {
            .ecom-carousel-section {
                margin-top: 36px;
                padding: 0 14px;
            }
            .ecom-carousel-title {
                font-size: 22px;
            }
            .ecom-carousel-nav-btn {
                display: none;
            }
            .ecom-carousel-viewall {
                padding: 6px 14px;
                font-size: 12px;
            }
            .ecom-carousel-track {
                gap: 12px;
            }
            .ecom-carousel-card-slot {
                flex: 0 0 calc(50% - 6px);
                min-width: 168px;
            }
        }
    </style>
@endPushOnce

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-products-carousel-template"
    >
        <div
            class="ecom-carousel-section"
            v-if="! isLoading && products.length"
        >
            <div class="ecom-carousel-header">
                <h2 class="ecom-carousel-title">
                    @{{ title }}
                </h2>

                <div class="ecom-carousel-controls">
                    <a
                        :href="navigationLink"
                        class="ecom-carousel-viewall"
                        :aria-label="title"
                        v-if="navigationLink"
                    >
                        <span>@lang('shop::app.components.products.carousel.view-all')</span>
                        <span class="icon-arrow-right text-sm"></span>
                    </a>

                    <template v-if="products.length > 3">
                        <span
                            v-if="products.length > 4 || (products.length > 3 && isScreenMax2xl)"
                            class="icon-arrow-left-stylish rtl:icon-arrow-right-stylish ecom-carousel-nav-btn"
                            role="button"
                            aria-label="@lang('shop::app.components.products.carousel.previous')"
                            tabindex="0"
                            @click="swipeLeft"
                        >
                        </span>

                        <span
                            v-if="products.length > 4 || (products.length > 3 && isScreenMax2xl)"
                            class="icon-arrow-right-stylish rtl:icon-arrow-left-stylish ecom-carousel-nav-btn"
                            role="button"
                            aria-label="@lang('shop::app.components.products.carousel.next')"
                            tabindex="0"
                            @click="swipeRight"
                        >
                        </span>
                    </template>
                </div>
            </div>

            <div
                ref="swiperContainer"
                class="ecom-carousel-track"
            >
                <x-shop::products.card
                    class="ecom-carousel-card-slot"
                    v-for="product in products"
                    :key="product.id"
                />
            </div>
        </div>

        <!-- Product Card Listing -->
        <template v-if="isLoading">
            <x-shop::shimmer.products.carousel :navigation-link="$navigationLink ?? false" />
        </template>
    </script>

    <script type="module">
        app.component('v-products-carousel', {
            template: '#v-products-carousel-template',

            props: [
                'src',
                'title',
                'navigationLink',
                'initialData',
            ],

            data() {
                return {
                    isLoading: ! Array.isArray(this.initialData),

                    products: Array.isArray(this.initialData) ? this.initialData : [],

                    offset: 323,

                    isScreenMax2xl: window.innerWidth <= 1440,
                };
            },

            mounted() {
                if (! Array.isArray(this.initialData)) {
                    this.getProducts();
                }
            },

            created() {
                window.addEventListener('resize', this.updateScreenSize);
            },

            beforeDestroy() {
                window.removeEventListener('resize', this.updateScreenSize);
            },

            methods: {
                getProducts() {
                    this.$axios.get(this.src)
                        .then(response => {
                            this.isLoading = false;

                            this.products = response.data.data;
                        }).catch(error => {
                            console.log(error);
                        });
                },

                updateScreenSize() {
                    this.isScreenMax2xl = window.innerWidth <= 1440;
                },

                swipeLeft() {
                    const container = this.$refs.swiperContainer;

                    container.scrollLeft -= this.offset;
                },

                swipeRight() {
                    const container = this.$refs.swiperContainer;

                    // Check if scroll reaches the end
                    if (container.scrollLeft + container.clientWidth >= container.scrollWidth) {
                        // Reset scroll to the beginning
                        container.scrollLeft = 0;
                    } else {
                        // Scroll to the right
                        container.scrollLeft += this.offset;
                    }
                },
            },
        });
    </script>
@endPushOnce
