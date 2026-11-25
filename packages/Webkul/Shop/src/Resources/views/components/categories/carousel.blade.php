@props([
    'src',
    'title' => '',
    'navigationLink' => '',
    'initialData' => null,
])

<v-categories-carousel
    src="{{ $src }}"
    title="{{ $title }}"
    navigation-link="{{ $navigationLink }}"
    :initial-data='@json($initialData)'
>
    <x-shop::shimmer.categories.carousel
        :count="8"
        :navigation-link="$navigationLink ?? false"
    />
</v-categories-carousel>

@pushOnce('styles')
    <style>
        .cat-carousel-section {
            margin: 44px auto 0;
            padding: 0 24px;
            max-width: 1360px;
        }
        .cat-carousel-track {
            display: flex;
            align-items: flex-start;
            gap: 28px;
            overflow-x: auto;
            scroll-behavior: smooth;
            scrollbar-width: none;
            -ms-overflow-style: none;
            padding: 8px 4px;
        }
        .cat-carousel-track::-webkit-scrollbar {
            display: none;
        }
        .cat-carousel-track.is-centered {
            justify-content: center;
        }
        .cat-carousel-item {
            flex: 0 0 auto;
            width: 112px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            transition: transform 0.25s ease;
        }
        .cat-carousel-item:hover {
            transform: translateY(-4px);
        }
        .cat-carousel-avatar {
            width: 100px;
            height: 100px;
            border-radius: 9999px;
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            padding: 4px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.05);
            transition: border-color 0.25s ease, box-shadow 0.25s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .cat-carousel-item:hover .cat-carousel-avatar {
            border-color: #0f172a;
            box-shadow: 0 10px 22px rgba(15, 23, 42, 0.12);
        }
        .cat-carousel-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 9999px;
        }
        .cat-carousel-label {
            text-align: center;
            font-size: 13px;
            font-weight: 600;
            line-height: 1.35;
            color: #1e293b;
            letter-spacing: 0.02em;
            margin: 0;
            word-break: break-word;
        }
        .cat-carousel-arrow {
            position: absolute;
            top: 34px;
            width: 40px;
            height: 40px;
            border-radius: 9999px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            color: #0f172a;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            cursor: pointer;
            box-shadow: 0 4px 14px rgba(15, 23, 42, 0.10);
            transition: all 0.2s ease;
            z-index: 10;
        }
        .cat-carousel-arrow:hover {
            background: #0f172a;
            border-color: #0f172a;
            color: #ffffff;
            transform: scale(1.06);
        }
        @media (max-width: 1024px) {
            .cat-carousel-section {
                margin-top: 32px;
                padding: 0 16px;
            }
            .cat-carousel-track {
                gap: 20px;
            }
            .cat-carousel-item {
                width: 92px;
            }
            .cat-carousel-avatar {
                width: 84px;
                height: 84px;
            }
        }
        @media (max-width: 640px) {
            .cat-carousel-section {
                margin-top: 24px;
                padding: 0 12px;
            }
            .cat-carousel-track {
                gap: 14px;
            }
            .cat-carousel-item {
                width: 76px;
                gap: 8px;
            }
            .cat-carousel-avatar {
                width: 68px;
                height: 68px;
            }
            .cat-carousel-label {
                font-size: 11.5px;
            }
            .cat-carousel-arrow {
                display: none;
            }
        }
    </style>
@endPushOnce

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-categories-carousel-template"
    >
        <div
            class="cat-carousel-section"
            v-if="! isLoading && categories?.length"
        >
            <div class="relative">
                <div
                    ref="swiperContainer"
                    class="cat-carousel-track"
                    :class="{ 'is-centered': ! hasOverflow }"
                    @scroll="updateScrollState"
                >
                    <a
                        :href="category.slug"
                        class="cat-carousel-item"
                        v-for="category in categories"
                        :key="category.id || category.slug"
                        :aria-label="category.name"
                    >
                        <div class="cat-carousel-avatar">
                            <x-shop::media.images.lazy
                                ::src="category.logo?.small_image_url || fallback"
                                ::srcset="`
                                    ${(category.logo?.small_image_url || fallback)} 60w,
                                    ${(category.logo?.medium_image_url || fallback)} 110w,
                                    ${(category.logo?.large_image_url || fallback)} 300w
                                `"
                                sizes="(max-width: 640px) 68px, 100px"
                                width="100"
                                height="100"
                                ::alt="category.name"
                            />
                        </div>

                        <p
                            class="cat-carousel-label"
                            v-text="category.name"
                        ></p>
                    </a>
                </div>

                <span
                    v-if="hasOverflow && canScrollLeft"
                    class="icon-arrow-left-stylish cat-carousel-arrow -left-3"
                    role="button"
                    aria-label="@lang('shop::components.carousel.previous')"
                    tabindex="0"
                    @click="swipeLeft"
                >
                </span>

                <span
                    v-if="hasOverflow && canScrollRight"
                    class="icon-arrow-right-stylish cat-carousel-arrow -right-3"
                    role="button"
                    aria-label="@lang('shop::components.carousel.next')"
                    tabindex="0"
                    @click="swipeRight"
                >
                </span>
            </div>
        </div>

        <!-- Category Carousel Shimmer -->
        <template v-if="isLoading">
            <x-shop::shimmer.categories.carousel
                :count="8"
                :navigation-link="$navigationLink ?? false"
            />
        </template>
    </script>

    <script type="module">
        app.component('v-categories-carousel', {
            template: '#v-categories-carousel-template',

            props: [
                'src',
                'title',
                'navigationLink',
                'initialData',
            ],

            data() {
                return {
                    isLoading: ! Array.isArray(this.initialData),

                    categories: Array.isArray(this.initialData) ? this.initialData : [],

                    offset: 323,

                    hasOverflow: false,

                    canScrollLeft: false,

                    canScrollRight: false,

                    fallback: "{{ product_image()->getPlaceholderUrl('small') }}"
                };
            },

            mounted() {
                if (Array.isArray(this.initialData)) {
                    this.$nextTick(() => {
                        this.updateScrollState();
                    });
                } else {
                    this.getCategories();
                }

                window.addEventListener('resize', this.updateScrollState);
            },

            beforeUnmount() {
                window.removeEventListener('resize', this.updateScrollState);
            },

            methods: {
                getCategories() {
                    this.$axios.get(this.src)
                        .then(response => {
                            this.isLoading = false;

                            this.categories = response.data.data;

                            this.$nextTick(() => {
                                this.updateScrollState();
                            });
                        }).catch(error => {
                            console.log(error);
                        });
                },

                updateScrollState() {
                    const container = this.$refs.swiperContainer;

                    if (! container) {
                        return;
                    }

                    this.hasOverflow = container.scrollWidth > container.clientWidth + 2;
                    this.canScrollLeft = container.scrollLeft > 2;
                    this.canScrollRight = container.scrollLeft + container.clientWidth < container.scrollWidth - 2;
                },

                swipeLeft() {
                    const container = this.$refs.swiperContainer;

                    container.scrollLeft -= this.offset;
                },

                swipeRight() {
                    const container = this.$refs.swiperContainer;

                    container.scrollLeft += this.offset;
                },
            },
        });
    </script>
@endPushOnce
