@php
    $channel = core()->getCurrentChannel();

    // Fetch live products for Testament Showcase
    $productRepo = app(\Webkul\Product\Repositories\ProductRepository::class);
    
    // Fetch products mapped to this channel
    $allProducts = $productRepo->scopeQuery(function ($query) {
        return $query->where('status', 1)->where('visible_individually', 1)->orderBy('id', 'desc');
    })->all();

    // Specific product highlights
    $willaProduct = $allProducts->firstWhere('sku', 'TESTAMENT-WILLA') 
        ?? $allProducts->first();

    // Trending & Just Arrived subsets
    $trendingProducts = $allProducts->filter(function($p) {
        return str_starts_with($p->sku, 'TESTAMENT-') && $p->sku !== 'TESTAMENT-WILLA';
    })->values();

    if ($trendingProducts->count() < 4) {
        $trendingProducts = $allProducts->take(4);
    }

    $justArrivedProducts = $allProducts->filter(function($p) {
        return !in_array($p->sku, ['TESTAMENT-WILLA', 'TESTAMENT-SUMMER-01']);
    })->take(4);
    if ($justArrivedProducts->count() < 4) {
        $justArrivedProducts = $allProducts->slice(2, 4);
    }
@endphp

<!-- SEO Meta Content -->
@push ('meta')
    <meta name="title" content="The Testament — Responsive Shopify Inspired Theme | {{ $channel->name }}" />
    <meta name="description" content="Shop summer ready styles, trending now playsuits, dresses and fresh designs on The Testament." />
@endPush

@push ('styles')
    <!-- Google Fonts: Cormorant Garamond for Editorial Luxury Serif & Plus Jakarta Sans for Body -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;0,700;1,400;1,600&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --testament-serif: 'Cormorant Garamond', Georgia, serif;
            --testament-sans: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            --testament-bg: #fcfbf9;
            --testament-card-bg: #f7f5f0;
            --testament-charcoal: #121212;
            --testament-muted: #6b665f;
            --testament-border: #e8e4dc;
            --testament-red: #ba3227;
            --testament-gold: #c29d59;
        }

        .testament-font-serif {
            font-family: var(--testament-serif);
        }

        .testament-font-sans {
            font-family: var(--testament-sans);
        }

        /* Marquee Ticker Keyframes */
        @keyframes testamentMarquee {
            0% { transform: translateX(0%); }
            100% { transform: translateX(-50%); }
        }

        .testament-marquee-track {
            display: flex;
            width: max-content;
            animation: testamentMarquee 26s linear infinite;
        }

        .testament-marquee-track:hover {
            animation-play-state: paused;
        }

        /* Luxury Button Styling */
        .testament-btn-primary {
            background-color: var(--testament-charcoal);
            color: #ffffff;
            font-family: var(--testament-sans);
            font-size: 0.8125rem;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            font-weight: 600;
            padding: 14px 34px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            border: 1px solid var(--testament-charcoal);
        }

        .testament-btn-primary:hover {
            background-color: transparent;
            color: var(--testament-charcoal);
        }

        .testament-btn-outline {
            background-color: transparent;
            color: var(--testament-charcoal);
            font-family: var(--testament-sans);
            font-size: 0.8125rem;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            font-weight: 600;
            padding: 14px 34px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            border: 1px solid var(--testament-charcoal);
        }

        .testament-btn-outline:hover {
            background-color: var(--testament-charcoal);
            color: #ffffff;
        }

        /* Card Image Zoom Effect */
        .testament-card-img-wrap {
            overflow: hidden;
            background-color: var(--testament-card-bg);
            position: relative;
        }

        .testament-card-img-wrap img {
            transition: transform 0.7s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .testament-card:hover .testament-card-img-wrap img {
            transform: scale(1.06);
        }

        /* Badges */
        .testament-badge {
            font-family: var(--testament-sans);
            font-size: 0.6875rem;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            font-weight: 700;
            padding: 4px 10px;
        }
    </style>
@endpush

<x-shop::layouts>
    <!-- Page Title -->
    <x-slot:title>
        The Testament | {{ $channel->name }} — Responsive Shopify Fashion Theme
    </x-slot>

    <div class="testament-font-sans bg-[#fcfbf9] text-[#121212] antialiased">

        <!-- ================================================================= -->
        <!-- 1. TOP FLASH SALE & COUNTDOWN TIMER BAR ("SALE NOW ON")          -->
        <!-- ================================================================= -->
        <section class="border-b border-[#e8e4dc] bg-[#121212] px-4 py-4 text-white">
            <div class="container mx-auto flex flex-col items-center justify-between gap-4 md:flex-row md:px-8">
                <!-- Headline -->
                <div class="flex items-center gap-3">
                    <span class="inline-block h-2 w-2 animate-ping rounded-full bg-[#ba3227]"></span>
                    <span class="testament-badge bg-[#ba3227] text-white">LIMITED TIME</span>
                    <h2 class="testament-font-serif text-xl font-medium tracking-wider md:text-2xl">
                        SALE NOW ON
                    </h2>
                    <span class="hidden text-xs text-stone-400 md:inline">|</span>
                    <span class="hidden text-xs tracking-widest text-stone-300 uppercase lg:inline">
                        Use Code <strong class="text-white">SUMMER50</strong> at checkout
                    </span>
                </div>

                <!-- Live Countdown Timer -->
                <div class="flex items-center gap-2 text-center md:gap-3">
                    <!-- Days -->
                    <div class="flex min-w-[54px] flex-col rounded bg-stone-900/90 px-2.5 py-1.5 border border-stone-800">
                        <span id="testament-days" class="testament-font-serif text-lg font-bold leading-none text-white md:text-xl">00</span>
                        <span class="text-[9px] tracking-wider text-stone-400 uppercase">Days</span>
                    </div>
                    <span class="text-stone-500 font-bold">:</span>
                    <!-- Hours -->
                    <div class="flex min-w-[54px] flex-col rounded bg-stone-900/90 px-2.5 py-1.5 border border-stone-800">
                        <span id="testament-hours" class="testament-font-serif text-lg font-bold leading-none text-white md:text-xl">16</span>
                        <span class="text-[9px] tracking-wider text-stone-400 uppercase">Hours</span>
                    </div>
                    <span class="text-stone-500 font-bold">:</span>
                    <!-- Mins -->
                    <div class="flex min-w-[54px] flex-col rounded bg-stone-900/90 px-2.5 py-1.5 border border-stone-800">
                        <span id="testament-mins" class="testament-font-serif text-lg font-bold leading-none text-white md:text-xl">43</span>
                        <span class="text-[9px] tracking-wider text-stone-400 uppercase">Mins</span>
                    </div>
                    <span class="text-stone-500 font-bold">:</span>
                    <!-- Secs -->
                    <div class="flex min-w-[54px] flex-col rounded bg-[#ba3227] px-2.5 py-1.5 border border-red-900">
                        <span id="testament-secs" class="testament-font-serif text-lg font-bold leading-none text-white md:text-xl">54</span>
                        <span class="text-[9px] tracking-wider text-red-200 uppercase">Secs</span>
                    </div>
                </div>

                <!-- CTA Link -->
                <a href="#trending-now" class="text-xs font-semibold tracking-widest text-[#c29d59] uppercase hover:underline">
                    Shop The Sale &rarr;
                </a>
            </div>
        </section>


        <!-- ================================================================= -->
        <!-- 2. INFINITE TICKER MARQUEE BAR                                   -->
        <!-- ================================================================= -->
        <div class="relative overflow-hidden border-b border-[#e8e4dc] bg-[#f7f5f0] py-2.5 text-xs font-semibold tracking-[0.2em] uppercase text-[#121212]">
            <div class="testament-marquee-track">
                @for ($i = 0; $i < 4; $i++)
                    <div class="flex items-center gap-10 pr-10">
                        <span class="flex items-center gap-2 text-[#ba3227]">
                            <svg class="h-3.5 w-3.5 fill-current" viewBox="0 0 20 20"><path d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.381z"/></svg>
                            FLASH SALE - SAVE 17%
                        </span>
                        <span class="text-stone-400">•</span>
                        <span class="flex items-center gap-2">
                            PRE ORDER &amp; SAVE 22%
                        </span>
                        <span class="text-stone-400">•</span>
                        <span class="flex items-center gap-2 text-[#ba3227]">
                            14% OFF TODAY ONLY - HURRY!
                        </span>
                        <span class="text-stone-400">•</span>
                        <span class="flex items-center gap-2">
                            FREE WORLDWIDE SHIPPING OVER $100
                        </span>
                        <span class="text-stone-400">•</span>
                    </div>
                @endfor
            </div>
        </div>


        <!-- ================================================================= -->
        <!-- 3. SUMMER READY STYLES (HERO EDITORIAL SECTION)                  -->
        <!-- ================================================================= -->
        <section class="border-b border-[#e8e4dc] bg-[#fcfbf9]">
            <div class="container mx-auto px-4 py-16 md:px-8 md:py-24">
                <div class="grid grid-cols-1 items-center gap-12 lg:grid-cols-12 lg:gap-16">
                    <!-- Left: Editorial Content -->
                    <div class="flex flex-col items-start justify-center lg:col-span-6 lg:pr-8">
                        <div class="mb-3 flex items-center gap-2">
                            <span class="h-px w-8 bg-[#c29d59]"></span>
                            <span class="text-xs font-semibold tracking-[0.25em] text-[#c29d59] uppercase">
                                New Season Capsule
                            </span>
                        </div>

                        <h1 class="testament-font-serif text-4xl leading-[1.1] font-normal tracking-tight text-[#121212] sm:text-5xl md:text-6xl">
                            Summer Ready <br />
                            <span class="italic font-light">Styles</span>
                        </h1>

                        <p class="mt-6 text-base leading-relaxed text-[#59554f] md:text-lg">
                            Embrace the season in style with our latest collection, curated to keep you looking 
                            <span class="testament-font-serif text-xl italic font-semibold text-[#121212]">
                                effortlessly chic
                            </span> 
                            all summer long.
                        </p>

                        <div class="mt-6 inline-flex items-center gap-3 rounded-full bg-[#f1ebe1] px-4 py-2 text-xs font-semibold tracking-wider text-[#121212] uppercase">
                            <span class="h-2 w-2 rounded-full bg-[#ba3227]"></span>
                            Save up to 50% in our end of season sale
                        </div>

                        <div class="mt-10 flex flex-wrap items-center gap-4">
                            <a href="#trending-now" class="testament-btn-primary">
                                Start Shopping
                            </a>
                            <a href="#new-prints" class="testament-btn-outline">
                                View Lookbook
                            </a>
                        </div>

                        <!-- Micro Stats / Highlights -->
                        <div class="mt-12 grid grid-cols-3 gap-6 border-t border-[#e8e4dc] pt-8 text-center sm:text-left">
                            <div>
                                <p class="testament-font-serif text-2xl font-bold text-[#121212]">100%</p>
                                <p class="text-[11px] tracking-wider text-[#6b665f] uppercase">Pure Flax Linen</p>
                            </div>
                            <div>
                                <p class="testament-font-serif text-2xl font-bold text-[#121212]">24h</p>
                                <p class="text-[11px] tracking-wider text-[#6b665f] uppercase">Dispatch Time</p>
                            </div>
                            <div>
                                <p class="testament-font-serif text-2xl font-bold text-[#121212]">4.9★</p>
                                <p class="text-[11px] tracking-wider text-[#6b665f] uppercase">Customer Rating</p>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Editorial Image Collage -->
                    <div class="lg:col-span-6">
                        <div class="grid grid-cols-2 gap-4 md:gap-6">
                            <div class="relative overflow-hidden rounded-sm shadow-md">
                                <img
                                    src="https://images.unsplash.com/photo-1496747611176-843222e1e57c?w=900&auto=format&fit=crop"
                                    alt="Summer Ready Linen Style"
                                    class="h-[380px] w-full object-cover transition-transform duration-700 hover:scale-105 sm:h-[460px]"
                                />
                                <div class="absolute bottom-3 left-3 bg-white/95 px-3 py-1.5 text-[10px] font-bold tracking-widest text-[#121212] uppercase backdrop-blur-sm">
                                    Linen Edit
                                </div>
                            </div>
                            <div class="relative mt-8 overflow-hidden rounded-sm shadow-md sm:mt-12">
                                <img
                                    src="https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?w=900&auto=format&fit=crop"
                                    alt="Willa Playsuit Look"
                                    class="h-[380px] w-full object-cover transition-transform duration-700 hover:scale-105 sm:h-[460px]"
                                />
                                <div class="absolute bottom-3 left-3 bg-[#121212] px-3 py-1.5 text-[10px] font-bold tracking-widest text-white uppercase">
                                    Playsuits
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>


        <!-- ================================================================= -->
        <!-- 4. TRENDING NOW (PRODUCT CAROUSEL / GRID)                        -->
        <!-- ================================================================= -->
        <section id="trending-now" class="border-b border-[#e8e4dc] bg-white py-16 md:py-24">
            <div class="container mx-auto px-4 md:px-8">
                <!-- Section Header -->
                <div class="mb-12 flex flex-col items-center justify-between gap-4 text-center md:flex-row md:text-left">
                    <div>
                        <span class="text-xs font-semibold tracking-[0.25em] text-[#ba3227] uppercase">
                            Curated Favorites
                        </span>
                        <h2 class="testament-font-serif text-3xl font-medium tracking-tight text-[#121212] md:text-4xl">
                            Trending Now
                        </h2>
                    </div>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('shop.search.index') }}" class="text-xs font-semibold tracking-widest text-[#121212] uppercase hover:underline">
                            Explore All Products &rarr;
                        </a>
                    </div>
                </div>

                <!-- Product Grid -->
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4 lg:gap-8">
                    @foreach ($trendingProducts as $product)
                        @php
                            $images = $product->images;
                            $firstImage = $images->first();
                            $imgUrl = $firstImage ? $firstImage->url : 'https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?w=800&auto=format&fit=crop';
                            $productUrl = route('shop.product_or_category.index', $product->url_key);
                            $priceHtml = core()->formatPrice($product->price);
                            $specialPriceHtml = $product->special_price ? core()->formatPrice($product->special_price) : null;
                        @endphp

                        <div class="testament-card group flex flex-col">
                            <!-- Image Wrap -->
                            <div class="testament-card-img-wrap relative aspect-[3/4] w-full rounded-sm">
                                <a href="{{ $productUrl }}">
                                    <img
                                        src="{{ $imgUrl }}"
                                        alt="{{ $product->name }}"
                                        class="h-full w-full object-cover"
                                        loading="lazy"
                                    />
                                </a>

                                <!-- Badges -->
                                <div class="absolute top-3 left-3 flex flex-col gap-1">
                                    @if ($specialPriceHtml)
                                        <span class="testament-badge bg-[#ba3227] text-white">
                                            SALE -20%
                                        </span>
                                    @else
                                        <span class="testament-badge bg-[#121212] text-white">
                                            TRENDING
                                        </span>
                                    @endif
                                </div>

                                <!-- Quick View / Add to Cart Overlay on Hover -->
                                <div class="absolute inset-x-3 bottom-3 translate-y-2 opacity-0 transition-all duration-300 group-hover:translate-y-0 group-hover:opacity-100">
                                    <a
                                        href="{{ $productUrl }}"
                                        class="block w-full bg-white/95 py-2.5 text-center text-xs font-semibold tracking-widest text-[#121212] uppercase shadow-md backdrop-blur hover:bg-[#121212] hover:text-white"
                                    >
                                        Quick View
                                    </a>
                                </div>
                            </div>

                            <!-- Details -->
                            <div class="mt-4 flex flex-1 flex-col">
                                <a href="{{ $productUrl }}" class="testament-font-serif text-lg font-medium text-[#121212] transition-colors hover:text-[#c29d59]">
                                    {{ $product->name }}
                                </a>

                                <p class="mt-1 line-clamp-1 text-xs text-[#6b665f]">
                                    {{ $product->short_description ?? 'Summer essential luxury styling.' }}
                                </p>

                                <div class="mt-3 flex items-center gap-2.5">
                                    @if ($specialPriceHtml)
                                        <span class="text-sm font-bold text-[#ba3227]">
                                            {{ $specialPriceHtml }}
                                        </span>
                                        <span class="text-xs text-[#9c968d] line-through">
                                            {{ $priceHtml }}
                                        </span>
                                    @else
                                        <span class="text-sm font-semibold text-[#121212]">
                                            {{ $priceHtml }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>


        <!-- ================================================================= -->
        <!-- 5. NEW PRINTS (EDITORIAL PROMOTIONAL BANNER)                     -->
        <!-- ================================================================= -->
        <section id="new-prints" class="relative overflow-hidden border-b border-[#e8e4dc] bg-[#f7f5f0] py-20 md:py-28">
            <div class="container mx-auto px-4 md:px-8">
                <div class="relative z-10 mx-auto max-w-3xl text-center">
                    <span class="text-xs font-semibold tracking-[0.3em] text-[#ba3227] uppercase">
                        Exclusive Release
                    </span>

                    <h2 class="testament-font-serif mt-3 text-4xl leading-tight font-normal text-[#121212] sm:text-5xl md:text-6xl">
                        Shop <span class="italic font-light">Fresh Designs</span>: <br class="hidden sm:inline"/>
                        New Prints Just Added!
                    </h2>

                    <p class="mx-auto mt-6 max-w-xl text-base leading-relaxed text-[#59554f] md:text-lg">
                        From delicate hand-painted botanicals to sun-drenched coastal motifs, explore our freshest prints created for summer adventures.
                    </p>

                    <div class="mt-10 flex flex-wrap justify-center gap-4">
                        <a href="{{ route('shop.search.index') }}" class="testament-btn-primary">
                            Shop Now
                        </a>
                        <a href="{{ route('shop.search.index') }}" class="testament-btn-outline">
                            View Print Collection
                        </a>
                    </div>
                </div>
            </div>

            <!-- Subtle background decorative motif -->
            <div class="pointer-events-none absolute inset-0 opacity-10 bg-[radial-gradient(#121212_1px,transparent_1px)] [background-size:16px_16px]"></div>
        </section>


        <!-- ================================================================= -->
        <!-- 6. JUST ARRIVED (PRODUCT GRID)                                   -->
        <!-- ================================================================= -->
        <section class="border-b border-[#e8e4dc] bg-white py-16 md:py-24">
            <div class="container mx-auto px-4 md:px-8">
                <!-- Section Header -->
                <div class="mb-12 flex flex-col items-center justify-between gap-4 text-center md:flex-row md:text-left">
                    <div>
                        <span class="text-xs font-semibold tracking-[0.25em] text-[#c29d59] uppercase">
                            Fresh From Studio
                        </span>
                        <h2 class="testament-font-serif text-3xl font-medium tracking-tight text-[#121212] md:text-4xl">
                            Just Arrived
                        </h2>
                    </div>
                    <div>
                        <a href="{{ route('shop.search.index') }}" class="text-xs font-semibold tracking-widest text-[#121212] uppercase hover:underline">
                            View New Arrivals &rarr;
                        </a>
                    </div>
                </div>

                <!-- Product Grid -->
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4 lg:gap-8">
                    @foreach ($justArrivedProducts as $product)
                        @php
                            $images = $product->images;
                            $firstImage = $images->first();
                            $imgUrl = $firstImage ? $firstImage->url : 'https://images.unsplash.com/photo-1572804013309-59a88b7e92f1?w=800&auto=format&fit=crop';
                            $productUrl = route('shop.product_or_category.index', $product->url_key);
                            $priceHtml = core()->formatPrice($product->price);
                            $specialPriceHtml = $product->special_price ? core()->formatPrice($product->special_price) : null;
                        @endphp

                        <div class="testament-card group flex flex-col">
                            <!-- Image Wrap -->
                            <div class="testament-card-img-wrap relative aspect-[3/4] w-full rounded-sm">
                                <a href="{{ $productUrl }}">
                                    <img
                                        src="{{ $imgUrl }}"
                                        alt="{{ $product->name }}"
                                        class="h-full w-full object-cover"
                                        loading="lazy"
                                    />
                                </a>

                                <!-- Badges -->
                                <div class="absolute top-3 left-3 flex flex-col gap-1">
                                    <span class="testament-badge bg-[#121212] text-white">
                                        JUST IN
                                    </span>
                                </div>

                                <!-- Quick View Overlay -->
                                <div class="absolute inset-x-3 bottom-3 translate-y-2 opacity-0 transition-all duration-300 group-hover:translate-y-0 group-hover:opacity-100">
                                    <a
                                        href="{{ $productUrl }}"
                                        class="block w-full bg-white/95 py-2.5 text-center text-xs font-semibold tracking-widest text-[#121212] uppercase shadow-md backdrop-blur hover:bg-[#121212] hover:text-white"
                                    >
                                        Quick View
                                    </a>
                                </div>
                            </div>

                            <!-- Details -->
                            <div class="mt-4 flex flex-1 flex-col">
                                <a href="{{ $productUrl }}" class="testament-font-serif text-lg font-medium text-[#121212] transition-colors hover:text-[#c29d59]">
                                    {{ $product->name }}
                                </a>

                                <p class="mt-1 line-clamp-1 text-xs text-[#6b665f]">
                                    {{ $product->short_description ?? 'Designed for relaxed occasions and sunny days.' }}
                                </p>

                                <div class="mt-3 flex items-center gap-2.5">
                                    @if ($specialPriceHtml)
                                        <span class="text-sm font-bold text-[#ba3227]">
                                            {{ $specialPriceHtml }}
                                        </span>
                                        <span class="text-xs text-[#9c968d] line-through">
                                            {{ $priceHtml }}
                                        </span>
                                    @else
                                        <span class="text-sm font-semibold text-[#121212]">
                                            {{ $priceHtml }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>


        <!-- ================================================================= -->
        <!-- 7. THERES MORE TO EXPLORE — WILLA PLAYSUIT SPOTLIGHT            -->
        <!-- ================================================================= -->
        @php
            $willaImg = 'https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?w=1000&auto=format&fit=crop';
            if ($willaProduct && $willaProduct->images->first()) {
                $willaImg = $willaProduct->images->first()->url;
            }
            $willaUrl = $willaProduct ? route('shop.product_or_category.index', $willaProduct->url_key) : route('shop.search.index');
        @endphp

        <section class="border-b border-[#e8e4dc] bg-[#fcfbf9] py-20 md:py-28">
            <div class="container mx-auto px-4 md:px-8">
                <div class="grid grid-cols-1 items-center gap-12 lg:grid-cols-12 lg:gap-16">
                    <!-- Left: Hero Imagery -->
                    <div class="lg:col-span-7">
                        <div class="relative overflow-hidden rounded-sm shadow-xl">
                            <img
                                src="{{ $willaImg }}"
                                alt="Willa Playsuit Showcase"
                                class="h-[460px] w-full object-cover transition-transform duration-700 hover:scale-105 sm:h-[580px]"
                            />
                            <div class="absolute top-4 left-4">
                                <span class="testament-badge bg-[#121212] text-white">
                                    HERO PIECE OF THE SEASON
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Storytelling & CTA -->
                    <div class="flex flex-col items-start justify-center lg:col-span-5">
                        <span class="text-xs font-semibold tracking-[0.25em] text-[#c29d59] uppercase">
                            Theres more to explore
                        </span>

                        <h2 class="testament-font-serif mt-3 text-4xl leading-tight font-normal text-[#121212] sm:text-5xl">
                            Willa Playsuit
                        </h2>

                        <p class="mt-6 text-base leading-relaxed text-[#59554f] md:text-lg">
                            The Willa Playsuit is the perfect fit for summer. Light, 
                            <span class="testament-font-serif text-xl italic font-semibold text-[#121212]">
                                Bright and easy
                            </span> 
                            for relaxed occasions.
                        </p>

                        <div class="mt-6 flex items-center gap-4 text-sm font-medium text-[#121212]">
                            <span class="flex items-center gap-1 text-[#ba3227]">
                                <svg class="h-4 w-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                5.0 (128 Reviews)
                            </span>
                            <span>•</span>
                            <span>In Stock & Ready to Ship</span>
                        </div>

                        <div class="mt-8 flex items-baseline gap-3">
                            <span class="testament-font-serif text-3xl font-bold text-[#ba3227]">
                                $59.00
                            </span>
                            <span class="text-base text-[#9c968d] line-through">
                                $79.00
                            </span>
                            <span class="testament-badge bg-[#ba3227] text-white">
                                SAVE $20
                            </span>
                        </div>

                        <div class="mt-10 flex flex-wrap gap-4">
                            <a href="{{ $willaUrl }}" class="testament-btn-primary">
                                Shop Now
                            </a>
                            <a href="{{ route('shop.search.index') }}" class="testament-btn-outline">
                                View Playsuit Collection
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>


        <!-- ================================================================= -->
        <!-- 8. BRAND VALUES / PILLARS BAR                                    -->
        <!-- ================================================================= -->
        <section class="border-b border-[#e8e4dc] bg-white py-12">
            <div class="container mx-auto px-4 md:px-8">
                <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-4">
                    <!-- 1 -->
                    <div class="flex items-start gap-4">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-[#f7f5f0] text-[#121212]">
                            <svg class="h-6 w-6 stroke-current" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                        </div>
                        <div>
                            <h4 class="testament-font-serif text-lg font-semibold text-[#121212]">Free Worldwide Delivery</h4>
                            <p class="mt-1 text-xs text-[#6b665f]">On all orders over $100 worldwide.</p>
                        </div>
                    </div>
                    <!-- 2 -->
                    <div class="flex items-start gap-4">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-[#f7f5f0] text-[#121212]">
                            <svg class="h-6 w-6 stroke-current" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        </div>
                        <div>
                            <h4 class="testament-font-serif text-lg font-semibold text-[#121212]">30-Day Easy Returns</h4>
                            <p class="mt-1 text-xs text-[#6b665f]">Hassle-free exchanges and returns.</p>
                        </div>
                    </div>
                    <!-- 3 -->
                    <div class="flex items-start gap-4">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-[#f7f5f0] text-[#121212]">
                            <svg class="h-6 w-6 stroke-current" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        </div>
                        <div>
                            <h4 class="testament-font-serif text-lg font-semibold text-[#121212]">Secure Checkout</h4>
                            <p class="mt-1 text-xs text-[#6b665f]">Encrypted payments via Stripe &amp; PayPal.</p>
                        </div>
                    </div>
                    <!-- 4 -->
                    <div class="flex items-start gap-4">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-[#f7f5f0] text-[#121212]">
                            <svg class="h-6 w-6 stroke-current" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        </div>
                        <div>
                            <h4 class="testament-font-serif text-lg font-semibold text-[#121212]">24/7 Dedicated Care</h4>
                            <p class="mt-1 text-xs text-[#6b665f]">Always here to assist with styling &amp; orders.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </div>

    <!-- Live Countdown Script -->
    @push ('scripts')
        <script>
            (function() {
                // Initialize Countdown Timer for 2 days into the future
                var countdownDuration = (16 * 3600) + (43 * 60) + 54; // default hours + mins + secs
                var storageKey = 'testament_sale_end_timestamp';
                var targetTime = localStorage.getItem(storageKey);

                if (!targetTime || targetTime < Date.now()) {
                    targetTime = Date.now() + (countdownDuration * 1000);
                    localStorage.setItem(storageKey, targetTime);
                }

                function updateCountdown() {
                    var now = Date.now();
                    var diff = Math.max(0, targetTime - now);

                    var days = Math.floor(diff / (1000 * 60 * 60 * 24));
                    var hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    var mins = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                    var secs = Math.floor((diff % (1000 * 60)) / 1000);

                    var elDays = document.getElementById('testament-days');
                    var elHours = document.getElementById('testament-hours');
                    var elMins = document.getElementById('testament-mins');
                    var elSecs = document.getElementById('testament-secs');

                    if (elDays) elDays.textContent = String(days).padStart(2, '0');
                    if (elHours) elHours.textContent = String(hours).padStart(2, '0');
                    if (elMins) elMins.textContent = String(mins).padStart(2, '0');
                    if (elSecs) elSecs.textContent = String(secs).padStart(2, '0');
                }

                setInterval(updateCountdown, 1000);
                updateCountdown();
            })();
        </script>
    @endpush

</x-shop::layouts>

