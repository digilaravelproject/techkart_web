@props(['options'])

@php
    $carouselImages = collect($options['images'] ?? [])
        ->filter(fn ($image) => filled($image['image'] ?? null))
        ->map(function ($image) {
            $path = \Illuminate\Support\Str::chopStart($image['image'], 'storage/');

            $urls = image_urls($path);

            $original = \Illuminate\Support\Facades\Storage::url($path);

            return array_merge($image, [
                'src' => $original,
                'srcset' => $original.' 1920w, '.$urls['large_image_url'].' 1280w, '.$urls['medium_image_url'].' 1024w, '.$urls['small_image_url'].' 768w',
            ]);
        })
        ->values()
        ->all();

    $firstImage = $carouselImages[0] ?? null;
@endphp

@if ($firstImage)
    {{--
        Preload the LCP image in <head> so the browser starts fetching it
        before HTML parse reaches the <img> tag. Directly targets the LCP
        "resource load delay" subpart Lighthouse reports as the biggest
        contributor on this page.
    --}}
    @push('meta')
        <link
            rel="preload"
            as="image"
            href="{{ $firstImage['src'] }}"
            imagesrcset="{{ $firstImage['srcset'] }}"
            imagesizes="100vw"
            fetchpriority="high"
        >
    @endpush
@endif

<v-carousel :images="{{ json_encode($carouselImages) }}">
    <div class="hero-slider-wrapper">
        <div class="hero-slider-frame overflow-hidden">
            @if ($firstImage)
                {{--
                    Server-rendered first slide so the browser can discover and
                    fetch the LCP image immediately, before Vue mounts the
                    carousel.
                --}}
                <img
                    src="{{ $firstImage['src'] }}"
                    srcset="{{ $firstImage['srcset'] }}"
                    sizes="100vw"
                    class="hero-slider-img select-none"
                    alt="{{ $firstImage['title'] ?? trans('shop::app.home.index.image-carousel') }}"
                    fetchpriority="high"
                    decoding="sync"
                >
            @else
                <div class="shimmer hero-slider-img"></div>
            @endif
        </div>
    </div>
</v-carousel>

@pushOnce('styles')
    <style>
        .hero-slider-wrapper {
            max-width: 1440px;
            margin: 12px auto 0;
            padding: 0 24px;
        }
        .hero-slider-frame {
            position: relative;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 12px 32px rgba(15, 23, 42, 0.10);
            background-color: #0f172a;
        }
        .hero-slider-img {
            width: 100%;
            aspect-ratio: 2.65 / 1;
            max-height: 520px;
            object-fit: cover;
            object-position: center;
            display: block;
        }
        .hero-slider-arrow {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 42px;
            height: 42px;
            border-radius: 9999px;
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            color: #0f172a;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.18);
            transition: transform 0.2s ease, background-color 0.2s ease, opacity 0.2s ease;
            opacity: 0.85;
            z-index: 10;
        }
        .hero-slider-arrow:hover {
            background: #ffffff;
            transform: translateY(-50%) scale(1.06);
            opacity: 1;
        }
        .hero-slider-dots {
            position: absolute;
            bottom: 14px;
            left: 50%;
            transform: translateX(-50%);
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 9999px;
            background: rgba(15, 23, 42, 0.45);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            z-index: 10;
        }
        .hero-slider-dot {
            height: 7px;
            width: 7px;
            border-radius: 9999px;
            background: rgba(255, 255, 255, 0.5);
            cursor: pointer;
            transition: all 0.25s ease;
            border: 0;
            padding: 0;
        }
        .hero-slider-dot:hover {
            background: rgba(255, 255, 255, 0.85);
        }
        .hero-slider-dot.is-active {
            width: 22px;
            background: #ffffff;
        }
        @media (max-width: 1024px) {
            .hero-slider-wrapper {
                padding: 0 16px;
            }
            .hero-slider-frame {
                border-radius: 14px;
            }
        }
        @media (max-width: 640px) {
            .hero-slider-wrapper {
                padding: 0 10px;
                margin-top: 8px;
            }
            .hero-slider-frame {
                border-radius: 12px;
            }
            .hero-slider-img {
                aspect-ratio: 2.2 / 1;
            }
            .hero-slider-arrow {
                display: none;
            }
            .hero-slider-dots {
                bottom: 8px;
                padding: 4px 9px;
                gap: 5px;
            }
            .hero-slider-dot {
                height: 5px;
                width: 5px;
            }
            .hero-slider-dot.is-active {
                width: 16px;
            }
        }
    </style>
@endPushOnce

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-carousel-template"
    >
        <div class="hero-slider-wrapper">
            <div class="hero-slider-frame group">
                <!-- Slider -->
                <div
                    class="inline-flex w-full translate-x-0 cursor-pointer transition-transform duration-700 ease-out will-change-transform"
                    ref="sliderContainer"
                >
                    <div
                        class="w-full flex-shrink-0 bg-cover bg-no-repeat"
                        v-for="(image, index) in images"
                        :key="index"
                        @click="visitLink(image)"
                        ref="slide"
                    >
                        <x-shop::media.images.lazy
                            class="hero-slider-img select-none"
                            ::lazy="index === 0 ? false : true"
                            ::src="image.src"
                            ::srcset="image.srcset"
                            sizes="(max-width: 1440px) 100vw, 1440px"
                            ::alt="image?.title || 'Carousel Image ' + (index + 1)"
                            tabindex="0"
                            ::fetchpriority="index === 0 ? 'high' : 'low'"
                            ::decoding="index === 0 ? 'sync' : 'async'"
                        />
                    </div>
                </div>

                <!-- Navigation -->
                <span
                    class="icon-arrow-left hero-slider-arrow left-3.5"
                    :class="{
                        'cursor-not-allowed opacity-40': direction == 'ltr' && currentIndex == 0,
                        'cursor-pointer': direction == 'ltr' ? currentIndex > 0 : currentIndex <= 0
                    }"
                    role="button"
                    aria-label="@lang('shop::components.carousel.previous')"
                    tabindex="0"
                    v-if="images?.length >= 2"
                    @click="navigate('prev')"
                >
                </span>

                <span
                    class="icon-arrow-right hero-slider-arrow right-3.5"
                    :class="{
                        'cursor-not-allowed opacity-40': direction == 'rtl' && currentIndex == 0,
                        'cursor-pointer': direction == 'rtl' ? currentIndex < 0 : currentIndex >= 0
                    }"
                    role="button"
                    aria-label="@lang('shop::components.carousel.next')"
                    tabindex="0"
                    v-if="images?.length >= 2"
                    @click="navigate('next')"
                >
                </span>

                <!-- Pagination -->
                <div
                    class="hero-slider-dots"
                    v-if="images?.length >= 2"
                >
                    <button
                        type="button"
                        v-for="(image, index) in images"
                        :key="index"
                        class="hero-slider-dot focus:outline-none"
                        :class="{ 'is-active': index === Math.abs(currentIndex) }"
                        :aria-label="'Go to slide ' + (index + 1)"
                        @click="navigateByPagination(index)"
                        @keydown.enter="navigateByPagination(index)"
                        @keydown.space.prevent="navigateByPagination(index)"
                    ></button>
                </div>
            </div>
        </div>
    </script>

    <script type="module">
        app.component("v-carousel", {
            template: '#v-carousel-template',

            props: ['images'],

            data() {
                return {
                    isDragging: false,
                    startPos: 0,
                    currentTranslate: 0,
                    prevTranslate: 0,
                    animationID: 0,
                    currentIndex: 0,
                    slider: '',
                    slides: [],
                    autoPlayInterval: null,
                    direction: 'ltr',
                    startFrom: 1,
                };
            },

            mounted() {
                this.slider = this.$refs.sliderContainer;

                if (
                    this.$refs.slide
                    && typeof this.$refs.slide[Symbol.iterator] === 'function'
                ) {
                    this.slides = Array.from(this.$refs.slide);
                }

                // Use requestIdleCallback for non-critical initialization
                if ('requestIdleCallback' in window) {
                    requestIdleCallback(() => {
                        this.init();
                        setTimeout(() => {
                            this.play();
                        }, 4000);
                    });
                } else {
                    setTimeout(() => {
                        this.init();
                        setTimeout(() => {
                            this.play();
                        }, 4000);
                    });
                }
            },

            beforeUnmount() {
                this.cleanup();
            },

            methods: {
                init() {
                    this.direction = document.dir;

                    if (this.direction == 'rtl') {
                        this.startFrom = -1;
                    }

                    this.slides.forEach((slide, index) => {
                        slide.querySelector('img')?.addEventListener('dragstart', (e) => e.preventDefault());

                        slide.addEventListener('mousedown', this.handleDragStart);

                        slide.addEventListener('touchstart', this.handleDragStart, { passive: true });

                        slide.addEventListener('mouseup', this.handleDragEnd);

                        slide.addEventListener('mouseleave', this.handleDragEnd);

                        slide.addEventListener('touchend', this.handleDragEnd, { passive: true });

                        slide.addEventListener('mousemove', this.handleDrag);

                        slide.addEventListener('touchmove', this.handleDrag, { passive: true });
                    });

                    window.addEventListener('resize', this.setPositionByIndex);
                },

                handleDragStart(event) {
                    this.startPos = event.type === 'mousedown' ? event.clientX : event.touches[0].clientX;

                    this.isDragging = true;

                    this.animationID = requestAnimationFrame(this.animation);
                },

                handleDrag(event) {
                    if (! this.isDragging) {
                        return;
                    }

                    const currentPosition = event.type === 'mousemove' ? event.clientX : event.touches[0].clientX;

                    this.currentTranslate = this.prevTranslate + currentPosition - this.startPos;
                },

                handleDragEnd(event) {
                    clearInterval(this.autoPlayInterval);

                    cancelAnimationFrame(this.animationID);

                    this.isDragging = false;

                    const movedBy = this.currentTranslate - this.prevTranslate;

                    if (this.direction == 'ltr') {
                        if (
                            movedBy < -100
                            && this.currentIndex < this.slides.length - 1
                        ) {
                            this.currentIndex += 1;
                        }

                        if (
                            movedBy > 100
                            && this.currentIndex > 0
                        ) {
                            this.currentIndex -= 1;
                        }
                    } else {
                        if (
                            movedBy > 100
                            && this.currentIndex < this.slides.length - 1
                        ) {
                            if (Math.abs(this.currentIndex) != this.slides.length - 1) {
                                this.currentIndex -= 1;
                            }
                        }

                        if (
                            movedBy < -100
                            && this.currentIndex < 0
                        ) {
                            this.currentIndex += 1;
                        }
                    }

                    this.setPositionByIndex();

                    this.play();
                },

                animation() {
                    this.setSliderPosition();

                    if (this.isDragging) {
                        requestAnimationFrame(this.animation);
                    }
                },

                setPositionByIndex() {
                    const slideWidth = this.slides[0]?.clientWidth || this.slider?.parentElement?.clientWidth || window.innerWidth;

                    this.currentTranslate = this.currentIndex * -slideWidth;

                    this.prevTranslate = this.currentTranslate;

                    this.setSliderPosition();
                },

                setSliderPosition() {
                    if (this.slider) {
                        this.slider.style.transform = `translateX(${this.currentTranslate}px)`;
                    }
                },

                visitLink(image) {
                    if (image.link) {
                        window.location.href = image.link;
                    }
                },

                navigate(type) {
                    clearInterval(this.autoPlayInterval);

                    if (this.direction === 'rtl') {
                        type === 'next' ? this.prev() : this.next();
                    } else {
                        type === 'next' ? this.next() : this.prev();
                    }

                    this.setPositionByIndex();

                    this.play();
                },

                next() {
                    this.currentIndex = (this.currentIndex + this.startFrom) % this.images.length;
                },

                prev() {
                    this.currentIndex = this.direction == 'ltr'
                        ? this.currentIndex > 0 ? this.currentIndex - 1 : 0
                        : this.currentIndex < 0 ? this.currentIndex + 1 : 0;
                },

                navigateByPagination(index) {
                    this.direction == 'rtl' ? index = -index : '';

                    clearInterval(this.autoPlayInterval);

                    this.currentIndex = index;

                    this.setPositionByIndex();

                    this.play();
                },

                play() {
                    clearInterval(this.autoPlayInterval);

                    this.autoPlayInterval = setInterval(() => {
                        this.currentIndex = (this.currentIndex + this.startFrom) % this.images.length;

                        this.setPositionByIndex();
                    }, 5000);
                },

                cleanup() {
                    // Clear intervals and animation frames
                    clearInterval(this.autoPlayInterval);
                    cancelAnimationFrame(this.animationID);

                    // Remove event listeners
                    if (this.slides) {
                        this.slides.forEach(slide => {
                            slide.removeEventListener('mousedown', this.handleDragStart);
                            slide.removeEventListener('touchstart', this.handleDragStart);
                            slide.removeEventListener('mouseup', this.handleDragEnd);
                            slide.removeEventListener('mouseleave', this.handleDragEnd);
                            slide.removeEventListener('touchend', this.handleDragEnd);
                            slide.removeEventListener('mousemove', this.handleDrag);
                            slide.removeEventListener('touchmove', this.handleDrag);
                        });
                    }

                    window.removeEventListener('resize', this.setPositionByIndex);
                },
            },
        });
    </script>
@endpushOnce
