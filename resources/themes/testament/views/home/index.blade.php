@php
    $channel = core()->getCurrentChannel();

    $productRepo = app(\Webkul\Product\Repositories\ProductRepository::class);
    $categoryRepo = app(\Webkul\Category\Repositories\CategoryRepository::class);

    // 1. Fetch real active products from database
    $dbProducts = $productRepo->getAll(['limit' => 24]);

    // Curated high-fashion fallbacks if catalog is brand new
    $fallbackTrending = [
        [
            'name'        => 'Aria Floral Tiered Maxi Dress',
            'price'       => '$68.00',
            'orig_price'  => '$88.00',
            'badge'       => 'SALE',
            'badge_color' => '#b83b32',
            'image'       => 'https://images.unsplash.com/photo-1572804013309-59a88b7e92f1?w=800&auto=format&fit=crop&q=80',
            'url'         => route('shop.search.index', ['term' => 'dress']),
            'colors'      => ['#e6b89c', '#d4a373', '#ccd5ae'],
        ],
        [
            'name'        => 'Sienna Terracotta High-Low Sundress',
            'price'       => '$84.00',
            'orig_price'  => null,
            'badge'       => 'BESTSELLER',
            'badge_color' => '#1a1a1a',
            'image'       => 'https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?w=800&auto=format&fit=crop&q=80',
            'url'         => route('shop.search.index', ['term' => 'sienna']),
            'colors'      => ['#bc6c25', '#dda15e'],
        ],
        [
            'name'        => 'Blush Meadow Puff Sleeve Mini',
            'price'       => '$72.00',
            'orig_price'  => '$92.00',
            'badge'       => 'SAVE 22%',
            'badge_color' => '#b83b32',
            'image'       => 'https://images.unsplash.com/photo-1529139574466-a303027c1d8b?w=800&auto=format&fit=crop&q=80',
            'url'         => route('shop.search.index', ['term' => 'blush']),
            'colors'      => ['#fcd5ce', '#f8edeb', '#faedcd'],
        ],
        [
            'name'        => 'Elodie Button-Down Linen Shirt Dress',
            'price'       => '$78.00',
            'orig_price'  => null,
            'badge'       => 'NEW',
            'badge_color' => '#1a1a1a',
            'image'       => 'https://images.unsplash.com/photo-1496747611176-843222e1e57c?w=800&auto=format&fit=crop&q=80',
            'url'         => route('shop.search.index', ['term' => 'elodie']),
            'colors'      => ['#fefae0', '#faedcd', '#d4a373'],
        ],
    ];

    $fallbackJustArrived = [
        [
            'name'       => 'Noir Strapless Bubble Hem Mini',
            'price'      => '$79.00',
            'orig_price' => null,
            'badge'      => 'LIMITED',
            'badge_color'=> '#111111',
            'image'      => 'https://images.unsplash.com/photo-1539109136881-3be0616acf4b?w=800&auto=format&fit=crop&q=80',
            'url'        => route('shop.search.index', ['term' => 'noir']),
        ],
        [
            'name'       => 'Ivory Strapless Balloon Dress',
            'price'      => '$79.00',
            'orig_price' => null,
            'badge'      => 'NEW',
            'badge_color'=> '#111111',
            'image'      => 'https://images.unsplash.com/photo-1502716119720-b23a93e5fe1b?w=800&auto=format&fit=crop&q=80',
            'url'        => route('shop.search.index', ['term' => 'ivory']),
        ],
        [
            'name'       => 'Midnight Satin Corset Mini',
            'price'      => '$85.00',
            'orig_price' => null,
            'badge'      => 'TRENDING',
            'badge_color'=> '#111111',
            'image'      => 'https://images.unsplash.com/photo-1509631179647-0177331693ae?w=800&auto=format&fit=crop&q=80',
            'url'        => route('shop.search.index', ['term' => 'midnight']),
        ],
        [
            'name'       => 'Scarlet Red Tiered Bubble Dress',
            'price'      => '$89.00',
            'orig_price' => null,
            'badge'      => 'SELLING FAST',
            'badge_color'=> '#b83b32',
            'image'      => 'https://images.unsplash.com/photo-1551803091-e20673f15770?w=800&auto=format&fit=crop&q=80',
            'url'        => route('shop.search.index', ['term' => 'scarlet']),
        ],
    ];

    // Map dynamic products from database
    $dynamicProducts = $dbProducts->map(function ($product) {
        $firstImage = $product->images->first();
        $imgUrl = $firstImage ? $firstImage->url : 'https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?w=800&auto=format&fit=crop&q=80';
        $minPrice = $product->getTypeInstance()->getMinimalPrice();
        $specialPrice = $product->special_price;

        return [
            'id'          => $product->id,
            'name'        => $product->name,
            'price'       => core()->formatPrice($minPrice),
            'orig_price'  => ($specialPrice && $specialPrice < $product->price) ? core()->formatPrice($product->price) : null,
            'badge'       => $specialPrice ? 'SALE' : ($product->new ? 'NEW' : 'POPULAR'),
            'badge_color' => $specialPrice ? '#b83b32' : '#111111',
            'image'       => $imgUrl,
            'url'         => route('shop.product_or_category.index', $product->url_key),
            'colors'      => ['#e6b89c', '#d4a373', '#ccd5ae'],
        ];
    });

    // Trending Collection: Dynamically uses real database products!
    $trendingCollection = $dynamicProducts->isNotEmpty() ? $dynamicProducts->take(4) : collect($fallbackTrending);

    // Just Arrived Collection: Dynamically uses latest database products!
    $justArrivedCollection = $dynamicProducts->count() > 4 ? $dynamicProducts->slice(4, 4) : ($dynamicProducts->isNotEmpty() ? $dynamicProducts->take(4) : collect($fallbackJustArrived));

    // Dynamic Spotlight: Willa Playsuit or first featured real product
    $willaModel = \Webkul\Product\Models\Product::where('sku', 'TESTAMENT-WILLA')->first()
        ?? $dbProducts->first();

    $willaData = [
        'name'        => $willaModel ? $willaModel->name : 'Willa Playsuit',
        'desc'        => $willaModel && $willaModel->short_description ? $willaModel->short_description : 'The Willa Playsuit is the perfect fit for summer. Light, Bright and easy for relaxed occasions. Tailored from pure breathable flax linen with a flattering cinched tie-waist.',
        'price'       => $willaModel ? core()->formatPrice($willaModel->getTypeInstance()->getMinimalPrice()) : '$59.00',
        'orig_price'  => ($willaModel && $willaModel->special_price) ? core()->formatPrice($willaModel->price) : '$79.00',
        'image'       => ($willaModel && $willaModel->images->first()) ? $willaModel->images->first()->url : 'https://images.unsplash.com/photo-1502716119720-b23a93e5fe1b?w=1000&auto=format&fit=crop&q=85',
        'url'         => $willaModel ? route('shop.product_or_category.index', $willaModel->url_key) : route('shop.search.index', ['term' => 'willa']),
    ];

    // Dynamic Category Bubbles from DB
    $dbCategories = $categoryRepo->getVisibleCategoryTree($channel->root_category_id);
    $catFallbackImgs = [
        'https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?w=300&auto=format&fit=crop&q=70',
        'https://images.unsplash.com/photo-1502716119720-b23a93e5fe1b?w=300&auto=format&fit=crop&q=70',
        'https://images.unsplash.com/photo-1529139574466-a303027c1d8b?w=300&auto=format&fit=crop&q=70',
        'https://images.unsplash.com/photo-1496747611176-843222e1e57c?w=300&auto=format&fit=crop&q=70',
        'https://images.unsplash.com/photo-1539109136881-3be0616acf4b?w=300&auto=format&fit=crop&q=70',
        'https://images.unsplash.com/photo-1572804013309-59a88b7e92f1?w=300&auto=format&fit=crop&q=70',
    ];

    if ($dbCategories->isNotEmpty()) {
        $categoriesBubbles = $dbCategories->take(6)->values()->map(function($cat, $idx) use ($catFallbackImgs) {
            return [
                'name' => $cat->name,
                'img'  => $cat->logo_url ?: ($cat->banner_url ?: $catFallbackImgs[$idx % count($catFallbackImgs)]),
                'url'  => route('shop.product_or_category.index', $cat->slug),
            ];
        });
    } else {
        $categoriesBubbles = collect([
            ['name' => 'Dresses', 'img' => $catFallbackImgs[0], 'url' => route('shop.search.index', ['term' => 'dress'])],
            ['name' => 'Tops', 'img' => $catFallbackImgs[1], 'url' => route('shop.search.index', ['term' => 'top'])],
            ['name' => 'Playsuits', 'img' => $catFallbackImgs[2], 'url' => route('shop.search.index', ['term' => 'playsuit'])],
            ['name' => 'Linen Edit', 'img' => $catFallbackImgs[3], 'url' => route('shop.search.index', ['term' => 'linen'])],
            ['name' => 'Knitwear', 'img' => $catFallbackImgs[4], 'url' => route('shop.search.index', ['term' => 'knitwear'])],
            ['name' => 'Sale', 'img' => $catFallbackImgs[5], 'url' => route('shop.search.index', ['term' => 'sale'])],
        ]);
    }

    $journalArticles = [
        [
            'title' => 'How to Style Linen for Every Occasion',
            'desc'  => 'From relaxed seaside mornings to candlelit al fresco dinners, discover the art of effortless linen styling.',
            'date'  => 'OCTOBER 24, 2026',
            'img'   => 'https://images.unsplash.com/photo-1490481651871-ab68de25d43d?w=800&auto=format&fit=crop&q=80',
        ],
        [
            'title' => 'The Vacation Packing Checklist',
            'desc'  => 'Light, versatile capsule pieces curated to take you seamlessly from flight to Mediterranean sunset.',
            'date'  => 'OCTOBER 18, 2026',
            'img'   => 'https://images.unsplash.com/photo-1469334031218-e382a71b716b?w=800&auto=format&fit=crop&q=80',
        ],
        [
            'title' => 'Behind the Design: The Resort Capsule',
            'desc'  => 'Our head of design walks through the hand-painted prints, sustainable flax, and thoughtful silhouettes.',
            'date'  => 'OCTOBER 10, 2026',
            'img'   => 'https://images.unsplash.com/photo-1485968579580-b6d095142e6e?w=800&auto=format&fit=crop&q=80',
        ],
    ];

    $instagramPhotos = [
        'https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?w=600&auto=format&fit=crop&q=80',
        'https://images.unsplash.com/photo-1509631179647-0177331693ae?w=600&auto=format&fit=crop&q=80',
        'https://images.unsplash.com/photo-1539109136881-3be0616acf4b?w=600&auto=format&fit=crop&q=80',
        'https://images.unsplash.com/photo-1572804013309-59a88b7e92f1?w=600&auto=format&fit=crop&q=80',
        'https://images.unsplash.com/photo-1529139574466-a303027c1d8b?w=600&auto=format&fit=crop&q=80',
        'https://images.unsplash.com/photo-1502716119720-b23a93e5fe1b?w=600&auto=format&fit=crop&q=80',
    ];
@endphp

<!-- SEO Meta Content -->
@push ('meta')
    <meta name="title" content="The Testament — Responsive Shopify Theme | {{ $channel->name }}" />
    <meta name="description" content="Shop summer ready styles, trending now playsuits, dresses, and fresh designs on The Testament." />
@endPush

@push ('styles')
    <!-- Google Fonts: Cormorant Garamond (Editorial Luxury Serif) + Jost/Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;0,700;1,400;1,600&family=Jost:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* ================================================================= */
        /* PURE SELF-CONTAINED TESTAMENT THEME CSS (Zero purge dependency)   */
        /* ================================================================= */
        .tst-theme-wrap {
            font-family: 'Jost', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            color: #1a1a1a;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
        }

        .tst-serif {
            font-family: 'Cormorant Garamond', Garamond, Georgia, serif;
        }

        .tst-sans {
            font-family: 'Jost', sans-serif;
        }

        /* Announcement Bar */
        .tst-announcement {
            background-color: #ffffff;
            border-bottom: 1px solid #ebe7df;
            padding: 10px 20px;
            text-align: center;
            font-size: 11px;
            letter-spacing: 0.22em;
            text-transform: uppercase;
            font-weight: 500;
            color: #555555;
        }

        /* Clean Luxury Brand Header */
        .tst-header {
            background: #ffffff;
            border-bottom: 1px solid #ebe7df;
            padding: 24px 30px;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .tst-header-inner {
            max-width: 1380px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .tst-brand-logo {
            font-family: 'Cormorant Garamond', serif;
            font-size: 34px;
            letter-spacing: 0.35em;
            font-weight: 600;
            text-transform: uppercase;
            color: #111111;
            text-decoration: none;
            margin: 0 auto;
        }

        .tst-nav-links {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 34px;
            margin-top: 14px;
        }

        .tst-nav-link {
            font-size: 12px;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            font-weight: 500;
            color: #1a1a1a;
            text-decoration: none;
            transition: color 0.2s ease;
            position: relative;
            padding-bottom: 4px;
        }

        .tst-nav-link:hover {
            color: #b83b32;
        }

        .tst-nav-link.sale {
            color: #b83b32;
            font-weight: 600;
        }

        /* Hero 1 - Full Width Editorial Slider / Banner (Matching Image 2 Top) */
        .tst-hero-banner {
            position: relative;
            height: 82vh;
            min-height: 580px;
            max-height: 840px;
            background-color: #e5dfd5;
            background-image: url('https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?w=1920&auto=format&fit=crop&q=85');
            background-size: cover;
            background-position: center 25%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .tst-hero-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to bottom, rgba(0,0,0,0.15) 0%, rgba(0,0,0,0.35) 100%);
        }

        .tst-hero-content {
            position: relative;
            z-index: 10;
            max-width: 780px;
            padding: 30px;
            color: #ffffff;
        }

        .tst-hero-subtitle {
            font-size: 13px;
            letter-spacing: 0.35em;
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: 14px;
            text-shadow: 0 1px 4px rgba(0,0,0,0.3);
        }

        .tst-hero-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 64px;
            line-height: 1.05;
            font-weight: 400;
            letter-spacing: 0.02em;
            margin: 0 0 26px 0;
            text-shadow: 0 2px 10px rgba(0,0,0,0.3);
        }

        .tst-hero-title em {
            font-style: italic;
            font-weight: 300;
        }

        .tst-btn-group {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 16px;
            flex-wrap: wrap;
        }

        .tst-btn-black {
            background: #111111;
            color: #ffffff;
            border: 1px solid #111111;
            padding: 15px 36px;
            font-size: 11px;
            letter-spacing: 0.22em;
            text-transform: uppercase;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
        }

        .tst-btn-black:hover {
            background: #ffffff;
            color: #111111;
            border-color: #ffffff;
        }

        .tst-btn-white {
            background: #ffffff;
            color: #111111;
            border: 1px solid #ffffff;
            padding: 15px 36px;
            font-size: 11px;
            letter-spacing: 0.22em;
            text-transform: uppercase;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .tst-btn-white:hover {
            background: transparent;
            color: #ffffff;
        }

        /* SALE NOW ON Bar with Live Countdown */
        .tst-sale-bar {
            background: #111111;
            color: #ffffff;
            padding: 18px 24px;
            border-bottom: 1px solid #222222;
        }

        .tst-sale-inner {
            max-width: 1280px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
        }

        .tst-sale-headline {
            font-family: 'Cormorant Garamond', serif;
            font-size: 26px;
            letter-spacing: 0.15em;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .tst-sale-pill {
            background: #b83b32;
            color: #ffffff;
            font-family: 'Jost', sans-serif;
            font-size: 10px;
            letter-spacing: 0.15em;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 2px;
        }

        .tst-timer-wrap {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .tst-timer-box {
            background: #222222;
            padding: 6px 14px;
            border-radius: 2px;
            text-align: center;
            min-width: 58px;
            border: 1px solid #333333;
        }

        .tst-timer-box.red {
            background: #b83b32;
            border-color: #8f2922;
        }

        .tst-timer-num {
            font-family: 'Cormorant Garamond', serif;
            font-size: 22px;
            font-weight: 700;
            line-height: 1;
            display: block;
        }

        .tst-timer-lbl {
            font-size: 8px;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            color: #aaaaaa;
            display: block;
            margin-top: 2px;
        }

        .tst-timer-box.red .tst-timer-lbl {
            color: #f7d2d0;
        }

        /* Section Containers */
        .tst-section {
            padding: 80px 24px;
            max-width: 1400px;
            margin: 0 auto;
        }

        .tst-section-header {
            text-align: center;
            margin-bottom: 48px;
        }

        .tst-section-subtitle {
            font-size: 11px;
            letter-spacing: 0.28em;
            text-transform: uppercase;
            font-weight: 600;
            color: #8c8277;
            margin-bottom: 8px;
            display: block;
        }

        .tst-section-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 42px;
            font-weight: 400;
            letter-spacing: 0.04em;
            color: #111111;
            margin: 0;
        }

        .tst-section-title em {
            font-style: italic;
        }

        /* Product Grid & Cards (Matching Image 2) */
        .tst-product-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 28px;
        }

        @media (max-width: 1024px) {
            .tst-product-grid { grid-template-columns: repeat(2, 1fr); gap: 20px; }
        }

        @media (max-width: 600px) {
            .tst-product-grid { grid-template-columns: 1fr; }
        }

        .tst-card {
            display: flex;
            flex-direction: column;
            text-decoration: none;
            color: inherit;
            position: relative;
            background: #ffffff;
        }

        .tst-card-img-wrap {
            position: relative;
            width: 100%;
            aspect-ratio: 2 / 3;
            overflow: hidden;
            background: #f7f5f0;
        }

        .tst-card-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            transition: transform 0.8s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .tst-card:hover .tst-card-img {
            transform: scale(1.05);
        }

        .tst-card-badge {
            position: absolute;
            top: 12px;
            left: 12px;
            font-size: 9px;
            letter-spacing: 0.15em;
            font-weight: 700;
            text-transform: uppercase;
            padding: 4px 8px;
            color: #ffffff;
            z-index: 2;
        }

        .tst-card-hover-action {
            position: absolute;
            bottom: 14px;
            left: 14px;
            right: 14px;
            background: #ffffff;
            color: #111111;
            padding: 12px;
            text-align: center;
            font-size: 11px;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            font-weight: 600;
            opacity: 0;
            transform: translateY(8px);
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .tst-card:hover .tst-card-hover-action {
            opacity: 1;
            transform: translateY(0);
        }

        .tst-card-body {
            padding-top: 16px;
            text-align: center;
        }

        .tst-card-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 19px;
            font-weight: 500;
            color: #111111;
            margin: 0 0 6px 0;
            transition: color 0.2s ease;
        }

        .tst-card:hover .tst-card-title {
            color: #b83b32;
        }

        .tst-card-price {
            font-size: 14px;
            font-weight: 500;
            color: #222222;
        }

        .tst-card-orig-price {
            text-decoration: line-through;
            color: #999999;
            margin-left: 8px;
            font-weight: 400;
            font-size: 13px;
        }

        .tst-card-colors {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            margin-top: 8px;
        }

        .tst-card-color-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            border: 1px solid rgba(0,0,0,0.15);
        }

        /* 3-Column Collection Banner Tiles (Matching Image 2) */
        .tst-3tiles-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
        }

        @media (max-width: 860px) {
            .tst-3tiles-grid { grid-template-columns: 1fr; }
        }

        .tst-tile-card {
            position: relative;
            aspect-ratio: 3 / 4;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #eee;
        }

        .tst-tile-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.8s ease;
        }

        .tst-tile-card:hover .tst-tile-img {
            transform: scale(1.05);
        }

        .tst-tile-btn {
            position: absolute;
            background: rgba(255, 255, 255, 0.95);
            color: #111111;
            padding: 16px 32px;
            font-size: 12px;
            letter-spacing: 0.22em;
            text-transform: uppercase;
            font-weight: 600;
            text-decoration: none;
            backdrop-filter: blur(4px);
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .tst-tile-card:hover .tst-tile-btn {
            background: #111111;
            color: #ffffff;
        }

        /* Summer Ready Styles - Text Callout (Matching Image 2) */
        .tst-editorial-callout {
            background: #faf8f5;
            border-top: 1px solid #ebe7df;
            border-bottom: 1px solid #ebe7df;
            padding: 70px 24px;
            text-align: center;
        }

        .tst-editorial-callout-inner {
            max-width: 740px;
            margin: 0 auto;
        }

        .tst-editorial-callout h3 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 46px;
            font-weight: 400;
            letter-spacing: 0.02em;
            margin: 0 0 18px 0;
        }

        .tst-editorial-callout p {
            font-size: 16px;
            color: #555555;
            line-height: 1.7;
            margin: 0 0 30px 0;
        }

        /* Circular Category Bubbles (Matching Image 2) */
        .tst-bubbles-bar {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 36px;
            padding: 40px 20px;
            overflow-x: auto;
            border-bottom: 1px solid #ebe7df;
        }

        .tst-bubble-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-decoration: none;
            color: #111111;
            min-width: 90px;
        }

        .tst-bubble-img-wrap {
            width: 86px;
            height: 86px;
            border-radius: 50%;
            overflow: hidden;
            border: 2px solid #ffffff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            margin-bottom: 10px;
            transition: transform 0.3s ease;
        }

        .tst-bubble-item:hover .tst-bubble-img-wrap {
            transform: scale(1.08);
        }

        .tst-bubble-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .tst-bubble-name {
            font-size: 11px;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            font-weight: 600;
        }

        /* Hero 2 - Mid-Page Visual Break Banner (Matching Image 2) */
        .tst-hero-mid {
            position: relative;
            height: 520px;
            background-color: #222;
            background-image: url('https://images.unsplash.com/photo-1509631179647-0177331693ae?w=1800&auto=format&fit=crop&q=85');
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            margin: 60px 0;
        }

        /* Split Spotlight Feature: Willa Playsuit (Matching Image 2) */
        .tst-split-spotlight {
            max-width: 1300px;
            margin: 0 auto;
            padding: 60px 24px;
            display: grid;
            grid-template-columns: 1fr 1.2fr;
            gap: 60px;
            align-items: center;
        }

        @media (max-width: 860px) {
            .tst-split-spotlight { grid-template-columns: 1fr; gap: 30px; }
        }

        .tst-spotlight-text h4 {
            font-size: 11px;
            letter-spacing: 0.3em;
            text-transform: uppercase;
            color: #8c8277;
            font-weight: 600;
            margin-bottom: 12px;
        }

        .tst-spotlight-text h2 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 48px;
            font-weight: 400;
            margin: 0 0 20px 0;
            line-height: 1.1;
        }

        .tst-spotlight-text p {
            font-size: 15px;
            color: #555555;
            line-height: 1.7;
            margin-bottom: 26px;
        }

        .tst-spotlight-img-wrap {
            aspect-ratio: 4 / 5;
            overflow: hidden;
            background: #eee;
            box-shadow: 0 8px 30px rgba(0,0,0,0.06);
        }

        .tst-spotlight-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.8s ease;
        }

        .tst-spotlight-img-wrap:hover .tst-spotlight-img {
            transform: scale(1.04);
        }

        /* Split Buy Box Showcase (Matching Image 2 middle) */
        .tst-split-buybox {
            background: #faf8f5;
            border-top: 1px solid #ebe7df;
            border-bottom: 1px solid #ebe7df;
            padding: 80px 24px;
        }

        .tst-split-buybox-inner {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1.2fr 1fr;
            gap: 60px;
            align-items: center;
        }

        @media (max-width: 860px) {
            .tst-split-buybox-inner { grid-template-columns: 1fr; }
        }

        /* Editorial Journal / Blog Grid (Matching Image 2) */
        .tst-journal-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 32px;
        }

        @media (max-width: 860px) {
            .tst-journal-grid { grid-template-columns: 1fr; }
        }

        .tst-journal-card {
            display: flex;
            flex-direction: column;
            text-decoration: none;
            color: inherit;
        }

        .tst-journal-img-wrap {
            aspect-ratio: 16 / 10;
            overflow: hidden;
            background: #eee;
            margin-bottom: 16px;
        }

        .tst-journal-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.7s ease;
        }

        .tst-journal-card:hover .tst-journal-img {
            transform: scale(1.05);
        }

        .tst-journal-date {
            font-size: 10px;
            letter-spacing: 0.22em;
            text-transform: uppercase;
            color: #8c8277;
            margin-bottom: 8px;
        }

        .tst-journal-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 22px;
            font-weight: 500;
            margin: 0 0 10px 0;
            line-height: 1.25;
            transition: color 0.2s;
        }

        .tst-journal-card:hover .tst-journal-title {
            color: #b83b32;
        }

        .tst-journal-desc {
            font-size: 13px;
            color: #666666;
            line-height: 1.6;
            margin: 0;
        }

        /* Instagram Grid (Matching Image 2) */
        .tst-insta-grid {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 12px;
            margin-top: 36px;
        }

        @media (max-width: 900px) {
            .tst-insta-grid { grid-template-columns: repeat(3, 1fr); }
        }

        .tst-insta-item {
            aspect-ratio: 1 / 1;
            overflow: hidden;
            position: relative;
            display: block;
        }

        .tst-insta-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.6s ease;
        }

        .tst-insta-item:hover .tst-insta-img {
            transform: scale(1.08);
        }

        /* Newsletter Split (Matching Image 2 Bottom) */
        .tst-newsletter-split {
            display: grid;
            grid-template-columns: 1fr 1.2fr;
            border-top: 1px solid #ebe7df;
            background: #faf8f5;
        }

        @media (max-width: 860px) {
            .tst-newsletter-split { grid-template-columns: 1fr; }
        }

        .tst-newsletter-img {
            width: 100%;
            height: 100%;
            min-height: 400px;
            object-fit: cover;
        }

        .tst-newsletter-form-wrap {
            padding: 80px 60px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .tst-newsletter-form-wrap h2 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 44px;
            margin: 0 0 16px 0;
            font-weight: 400;
        }

        .tst-newsletter-form-wrap p {
            font-size: 15px;
            color: #666666;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .tst-input-row {
            display: flex;
            max-width: 460px;
        }

        .tst-input-email {
            flex: 1;
            padding: 15px 18px;
            border: 1px solid #111111;
            border-right: none;
            font-family: 'Jost', sans-serif;
            font-size: 13px;
            outline: none;
        }

        .tst-input-btn {
            background: #111111;
            color: #ffffff;
            border: 1px solid #111111;
            padding: 15px 28px;
            font-size: 11px;
            letter-spacing: 0.22em;
            text-transform: uppercase;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .tst-input-btn:hover {
            background: #ffffff;
            color: #111111;
        }

        /* Luxury Dark Footer (Matching Image 2) */
        .tst-footer {
            background: #111111;
            color: #ffffff;
            padding: 80px 24px 30px 24px;
        }

        .tst-footer-grid {
            max-width: 1380px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1.5fr 1fr 1fr 1.2fr;
            gap: 50px;
            padding-bottom: 60px;
            border-bottom: 1px solid #222222;
        }

        @media (max-width: 900px) {
            .tst-footer-grid { grid-template-columns: 1fr; gap: 36px; }
        }

        .tst-footer h4 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 22px;
            letter-spacing: 0.1em;
            margin: 0 0 20px 0;
            font-weight: 500;
        }

        .tst-footer p, .tst-footer a {
            font-size: 13px;
            color: #999999;
            text-decoration: none;
            line-height: 1.8;
            display: block;
            transition: color 0.2s;
        }

        .tst-footer a:hover {
            color: #ffffff;
        }

        .tst-footer-bottom {
            max-width: 1380px;
            margin: 30px auto 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 12px;
            color: #666666;
            flex-wrap: wrap;
            gap: 16px;
        }
    </style>
@endpush

<x-shop::layouts :has-header="false" :has-footer="false">
    <!-- Page Title -->
    <x-slot:title>
        The Testament | Responsive Shopify Theme Demo
    </x-slot>

    <div class="tst-theme-wrap">

        <!-- ================================================================= -->
        <!-- 1. ANNOUNCEMENT BAR                                               -->
        <!-- ================================================================= -->
        <div class="tst-announcement">
            COMPLIMENTARY WORLDWIDE SHIPPING ON ORDERS OVER $150 • CODE: SUMMER50
        </div>

        <!-- ================================================================= -->
        <!-- 2. LUXURY TESTAMENT HEADER (Matching Image 2)                    -->
        <!-- ================================================================= -->
        <header class="tst-header">
            <div class="tst-header-inner">
                <!-- Left: Currency -->
                <div style="font-size: 11px; letter-spacing: 0.18em; font-weight: 500; text-transform: uppercase;">
                    USD $ &nbsp; | &nbsp; EN
                </div>

                <!-- Center: Serif Brand Logo -->
                <a href="{{ route('shop.home.index') }}" class="tst-brand-logo">
                    T E S T A M E N T
                </a>

                <!-- Right: Icons -->
                <div style="display: flex; align-items: center; gap: 20px; font-size: 12px; letter-spacing: 0.15em; text-transform: uppercase; font-weight: 500;">
                    <a href="{{ route('shop.search.index') }}" style="color: inherit; text-decoration: none;">SEARCH</a>
                    <a href="{{ route('shop.customer.session.index') }}" style="color: inherit; text-decoration: none;">ACCOUNT</a>
                    <a href="{{ route('shop.checkout.cart.index') }}" style="color: inherit; text-decoration: none; font-weight: 600;">BAG ({{ \Webkul\Checkout\Facades\Cart::getCart()?->items_count ?? 0 }})</a>
                </div>
            </div>

            <!-- Centered Nav Menu (Dynamic Categories + Lookbook/Sale) -->
            <nav class="tst-nav-links">
                <a href="{{ route('shop.search.index', ['term' => 'new']) }}" class="tst-nav-link">NEW IN</a>
                @foreach ($categoriesBubbles->take(4) as $navCat)
                    <a href="{{ $navCat['url'] }}" class="tst-nav-link">{{ strtoupper($navCat['name']) }}</a>
                @endforeach
                <a href="#lookbook" class="tst-nav-link">LOOKBOOK</a>
                <a href="#sale" class="tst-nav-link sale">SALE - UP TO 50%</a>
            </nav>
        </header>


        <!-- ================================================================= -->
        <!-- 3. HERO SECTION (Matching Image 2 Top Full Banner)                -->
        <!-- ================================================================= -->
        <section class="tst-hero-banner">
            <div class="tst-hero-overlay"></div>
            <div class="tst-hero-content">
                <span class="tst-hero-subtitle">SUMMER CAPSULE 2026</span>
                <h1 class="tst-hero-title">
                    Fresh New Arrivals <br/>
                    <em>Effortless Elegance</em>
                </h1>
                <div class="tst-btn-group">
                    <a href="#trending" class="tst-btn-white">Shop New In</a>
                    <a href="#summer-styles" class="tst-btn-black">Explore Lookbook</a>
                </div>
            </div>
        </section>


        <!-- ================================================================= -->
        <!-- 4. SALE NOW ON BAR WITH LIVE COUNTDOWN TIMER                      -->
        <!-- ================================================================= -->
        <section id="sale" class="tst-sale-bar">
            <div class="tst-sale-inner">
                <div class="tst-sale-headline">
                    <span class="tst-sale-pill">LIMITED TIME</span>
                    SALE NOW ON
                </div>

                <!-- Live Countdown Timer -->
                <div class="tst-timer-wrap">
                    <div class="tst-timer-box">
                        <span id="tst-days" class="tst-timer-num">00</span>
                        <span class="tst-timer-lbl">Days</span>
                    </div>
                    <span>:</span>
                    <div class="tst-timer-box">
                        <span id="tst-hours" class="tst-timer-num">16</span>
                        <span class="tst-timer-lbl">Hours</span>
                    </div>
                    <span>:</span>
                    <div class="tst-timer-box">
                        <span id="tst-mins" class="tst-timer-num">43</span>
                        <span class="tst-timer-lbl">Mins</span>
                    </div>
                    <span>:</span>
                    <div class="tst-timer-box red">
                        <span id="tst-secs" class="tst-timer-num">54</span>
                        <span class="tst-timer-lbl">Secs</span>
                    </div>
                </div>

                <a href="{{ route('shop.search.index') }}" style="color: #ffffff; font-size: 11px; letter-spacing: 0.22em; text-transform: uppercase; font-weight: 600; text-decoration: underline;">
                    Shop The Flash Sale &rarr;
                </a>
            </div>
        </section>


        <!-- ================================================================= -->
        <!-- 5. TRENDING NOW (DYNAMIC REAL PRODUCTS - 4-COLUMN)               -->
        <!-- ================================================================= -->
        <section id="trending" class="tst-section">
            <div class="tst-section-header">
                <span class="tst-section-subtitle">SUMMER FAVORITES</span>
                <h2 class="tst-section-title">Trending Now</h2>
            </div>

            <div class="tst-product-grid">
                @foreach ($trendingCollection as $item)
                    <a href="{{ $item['url'] }}" class="tst-card">
                        <div class="tst-card-img-wrap">
                            <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="tst-card-img" loading="lazy" />
                            <span class="tst-card-badge" style="background-color: {{ $item['badge_color'] }};">
                                {{ $item['badge'] }}
                            </span>
                            <div class="tst-card-hover-action">
                                View Product
                            </div>
                        </div>
                        <div class="tst-card-body">
                            <h3 class="tst-card-title">{{ $item['name'] }}</h3>
                            <div class="tst-card-price">
                                {{ $item['price'] }}
                                @if (!empty($item['orig_price']))
                                    <span class="tst-card-orig-price">{{ $item['orig_price'] }}</span>
                                @endif
                            </div>
                            <div class="tst-card-colors">
                                @foreach ($item['colors'] as $dot)
                                    <span class="tst-card-color-dot" style="background-color: {{ $dot }};"></span>
                                @endforeach
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>


        <!-- ================================================================= -->
        <!-- 6. 3-COLUMN CATEGORY TILES (Matching Image 2 middle)              -->
        <!-- ================================================================= -->
        <section class="tst-section" style="padding-top: 0;">
            <div class="tst-3tiles-grid">
                <!-- Tile 1 -->
                <div class="tst-tile-card">
                    <img src="https://images.unsplash.com/photo-1502716119720-b23a93e5fe1b?w=800&auto=format&fit=crop&q=80" alt="Shop Tops" class="tst-tile-img" />
                    <a href="{{ route('shop.search.index', ['term' => 'top']) }}" class="tst-tile-btn">Shop Tops</a>
                </div>
                <!-- Tile 2 -->
                <div class="tst-tile-card">
                    <img src="https://images.unsplash.com/photo-1496747611176-843222e1e57c?w=800&auto=format&fit=crop&q=80" alt="Shop Knitwear" class="tst-tile-img" />
                    <a href="{{ route('shop.search.index', ['term' => 'knitwear']) }}" class="tst-tile-btn">Shop Knitwear</a>
                </div>
                <!-- Tile 3 -->
                <div class="tst-tile-card">
                    <img src="https://images.unsplash.com/photo-1529139574466-a303027c1d8b?w=800&auto=format&fit=crop&q=80" alt="Shop Playsuits" class="tst-tile-img" />
                    <a href="{{ route('shop.search.index', ['term' => 'playsuit']) }}" class="tst-tile-btn">Shop Playsuits</a>
                </div>
            </div>
        </section>


        <!-- ================================================================= -->
        <!-- 7. SUMMER READY STYLES (EDITORIAL CALLOUT - Matching Image 2)    -->
        <!-- ================================================================= -->
        <section id="summer-styles" class="tst-editorial-callout">
            <div class="tst-editorial-callout-inner">
                <span class="tst-section-subtitle">THE NEW CAPSULE</span>
                <h3>Summer Ready Styles</h3>
                <p>
                    Embrace the season in style with our latest collection, curated to keep you looking 
                    <strong style="color: #111111; font-style: italic;">effortlessly chic</strong> 
                    all summer long. Save up to 50% in our end of season sale event.
                </p>
                <a href="{{ route('shop.search.index') }}" class="tst-btn-black">
                    Start Shopping
                </a>
            </div>
        </section>


        <!-- ================================================================= -->
        <!-- 8. CIRCULAR CATEGORY BUBBLES BAR (DYNAMIC CATEGORIES)             -->
        <!-- ================================================================= -->
        <div class="tst-bubbles-bar">
            @foreach ($categoriesBubbles as $bubble)
                <a href="{{ $bubble['url'] }}" class="tst-bubble-item">
                    <div class="tst-bubble-img-wrap">
                        <img src="{{ $bubble['img'] }}" alt="{{ $bubble['name'] }}" class="tst-bubble-img" />
                    </div>
                    <span class="tst-bubble-name">{{ $bubble['name'] }}</span>
                </a>
            @endforeach
        </div>


        <!-- ================================================================= -->
        <!-- 9. FULL-WIDTH EDITORIAL BREAK BANNER (HERO 2 - Image 2)          -->
        <!-- ================================================================= -->
        <section class="tst-hero-mid">
            <div class="tst-hero-overlay" style="background: rgba(0,0,0,0.3);"></div>
            <div class="tst-hero-content">
                <span class="tst-hero-subtitle">TIMELESS ELEGANCE</span>
                <h2 class="tst-hero-title" style="font-size: 52px;">
                    Modern Classics <br/>
                    <em>Resort &amp; Evening</em>
                </h2>
                <div class="tst-btn-group">
                    <a href="{{ route('shop.search.index') }}" class="tst-btn-white">Shop The Collection</a>
                </div>
            </div>
        </section>


        <!-- ================================================================= -->
        <!-- 10. SPLIT SPOTLIGHT FEATURE: WILLA / FEATURED PRODUCT (Image 2)   -->
        <!-- ================================================================= -->
        <section class="tst-split-spotlight">
            <div class="tst-spotlight-text">
                <h4>THERES MORE TO EXPLORE</h4>
                <h2>{{ $willaData['name'] }}</h2>
                <p>
                    {{ $willaData['desc'] }}
                </p>
                <div style="font-size: 26px; font-family: 'Cormorant Garamond', serif; font-weight: 700; color: #b83b32; margin-bottom: 24px;">
                    {{ $willaData['price'] }} 
                    @if ($willaData['orig_price'])
                        <span style="font-size: 16px; text-decoration: line-through; color: #999; margin-left: 8px;">{{ $willaData['orig_price'] }}</span>
                    @endif
                </div>
                <a href="{{ $willaData['url'] }}" class="tst-btn-black">
                    Shop Now
                </a>
            </div>

            <div class="tst-spotlight-img-wrap">
                <img src="{{ $willaData['image'] }}" alt="{{ $willaData['name'] }}" class="tst-spotlight-img" />
            </div>
        </section>


        <!-- ================================================================= -->
        <!-- 11. JUST ARRIVED / NEW PRODUCTS (DYNAMIC REAL PRODUCTS)          -->
        <!-- ================================================================= -->
        <section class="tst-section">
            <div class="tst-section-header">
                <span class="tst-section-subtitle">NEW SILHOUETTES</span>
                <h2 class="tst-section-title">Just Arrived</h2>
            </div>

            <div class="tst-product-grid">
                @foreach ($justArrivedCollection as $dress)
                    <a href="{{ $dress['url'] }}" class="tst-card">
                        <div class="tst-card-img-wrap">
                            <img src="{{ $dress['image'] }}" alt="{{ $dress['name'] }}" class="tst-card-img" loading="lazy" />
                            <span class="tst-card-badge" style="background-color: {{ $dress['badge_color'] ?? '#111111' }};">
                                {{ $dress['badge'] }}
                            </span>
                            <div class="tst-card-hover-action">
                                View Product
                            </div>
                        </div>
                        <div class="tst-card-body">
                            <h3 class="tst-card-title">{{ $dress['name'] }}</h3>
                            <div class="tst-card-price">
                                {{ $dress['price'] }}
                                @if (!empty($dress['orig_price']))
                                    <span class="tst-card-orig-price">{{ $dress['orig_price'] }}</span>
                                @endif
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>


        <!-- ================================================================= -->
        <!-- 12. SPLIT BUY BOX / PRODUCT SHOWCASE (Image 2)                   -->
        <!-- ================================================================= -->
        <section class="tst-split-buybox">
            <div class="tst-split-buybox-inner">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <img src="https://images.unsplash.com/photo-1572804013309-59a88b7e92f1?w=800&auto=format&fit=crop&q=80" style="width: 100%; aspect-ratio: 3/4; object-fit: cover;" alt="Lookbook 1" />
                    <img src="https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?w=800&auto=format&fit=crop&q=80" style="width: 100%; aspect-ratio: 3/4; object-fit: cover;" alt="Lookbook 2" />
                </div>
                <div>
                    <span class="tst-section-subtitle">FEATURED EDIT</span>
                    <h3 style="font-family: 'Cormorant Garamond', serif; font-size: 38px; margin: 0 0 16px 0; font-weight: 500;">
                        Linen Resort Co-Ord Set
                    </h3>
                    <p style="font-size: 15px; color: #555; line-height: 1.7; margin-bottom: 20px;">
                        The quintessential relaxed summer outfit. Tailored from 100% natural European flax linen with shell buttons and relaxed pleating.
                    </p>
                    <div style="font-size: 24px; font-weight: 600; margin-bottom: 26px;">$94.00</div>
                    <a href="{{ route('shop.search.index') }}" class="tst-btn-black">
                        Explore Collection
                    </a>
                </div>
            </div>
        </section>


        <!-- ================================================================= -->
        <!-- 13. EDITORIAL FROM THE JOURNAL (Matching Image 2)                -->
        <!-- ================================================================= -->
        <section id="lookbook" class="tst-section">
            <div class="tst-section-header">
                <span class="tst-section-subtitle">STORIES &amp; INSPIRATION</span>
                <h2 class="tst-section-title">From The Journal</h2>
            </div>

            <div class="tst-journal-grid">
                @foreach ($journalArticles as $article)
                    <div class="tst-journal-card">
                        <div class="tst-journal-img-wrap">
                            <img src="{{ $article['img'] }}" alt="{{ $article['title'] }}" class="tst-journal-img" loading="lazy" />
                        </div>
                        <span class="tst-journal-date">{{ $article['date'] }}</span>
                        <h4 class="tst-journal-title">{{ $article['title'] }}</h4>
                        <p class="tst-journal-desc">{{ $article['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </section>


        <!-- ================================================================= -->
        <!-- 14. INSTAGRAM SOCIAL FEED GRID (Matching Image 2)                -->
        <!-- ================================================================= -->
        <section style="padding: 40px 24px 80px 24px; max-width: 1400px; margin: 0 auto;">
            <div class="tst-section-header" style="margin-bottom: 20px;">
                <span class="tst-section-subtitle">JOIN OUR COMMUNITY</span>
                <h2 class="tst-section-title" style="font-size: 32px;">@TESTAMENT_STYLE</h2>
            </div>

            <div class="tst-insta-grid">
                @foreach ($instagramPhotos as $insta)
                    <div class="tst-insta-item">
                        <img src="{{ $insta }}" alt="Instagram Lifestyle" class="tst-insta-img" loading="lazy" />
                    </div>
                @endforeach
            </div>
        </section>


        <!-- ================================================================= -->
        <!-- 15. SPLIT NEWSLETTER SIGNUP (Matching Image 2 Bottom)            -->
        <!-- ================================================================= -->
        <section class="tst-newsletter-split">
            <img src="https://images.unsplash.com/photo-1502716119720-b23a93e5fe1b?w=900&auto=format&fit=crop&q=85" alt="Newsletter Editorial" class="tst-newsletter-img" />
            <div class="tst-newsletter-form-wrap">
                <span class="tst-section-subtitle">JOIN THE TESTAMENT CLUB</span>
                <h2>Receive 15% Off Your First Order</h2>
                <p>
                    Be the first to know about new resort capsule arrivals, private showroom sales, and editorial styling stories.
                </p>
                <form onsubmit="event.preventDefault(); alert('Thank you for subscribing to Testament!');" class="tst-input-row">
                    <input type="email" placeholder="Enter your email address" class="tst-input-email" required />
                    <button type="submit" class="tst-input-btn">Subscribe</button>
                </form>
            </div>
        </section>


        <!-- ================================================================= -->
        <!-- 16. LUXURY DARK FOOTER (Matching Image 2)                        -->
        <!-- ================================================================= -->
        <footer class="tst-footer">
            <div class="tst-footer-grid">
                <!-- Col 1 -->
                <div>
                    <h4>THE TESTAMENT</h4>
                    <p>
                        A tribute to relaxed elegance, mindful craftsmanship, and enduring resort silhouettes crafted for summer escapes.
                    </p>
                </div>
                <!-- Col 2 -->
                <div>
                    <h4>SHOP</h4>
                    <a href="{{ route('shop.search.index') }}">New Arrivals</a>
                    <a href="{{ route('shop.search.index', ['term' => 'dress']) }}">Dresses</a>
                    <a href="{{ route('shop.search.index', ['term' => 'playsuit']) }}">Playsuits</a>
                    <a href="{{ route('shop.search.index', ['term' => 'linen']) }}">The Linen Capsule</a>
                    <a href="#sale">End of Season Sale</a>
                </div>
                <!-- Col 3 -->
                <div>
                    <h4>CUSTOMER CARE</h4>
                    <a href="#">Shipping &amp; Delivery</a>
                    <a href="#">Returns &amp; Exchanges</a>
                    <a href="#">Size Guide</a>
                    <a href="#">Track Order</a>
                    <a href="#">Contact Us</a>
                </div>
                <!-- Col 4 -->
                <div>
                    <h4>CONNECT</h4>
                    <p>hello@testament.com</p>
                    <p>Mon - Fri: 9:00 AM - 6:00 PM EST</p>
                    <div style="margin-top: 14px; display: flex; gap: 16px; font-size: 14px;">
                        <span>Instagram</span> &bull; <span>Pinterest</span> &bull; <span>TikTok</span>
                    </div>
                </div>
            </div>

            <div class="tst-footer-bottom">
                <div>&copy; {{ date('Y') }} THE TESTAMENT. Powered by Bagisto E-Commerce.</div>
                <div style="display: flex; gap: 16px;">
                    <span>Privacy Policy</span>
                    <span>Terms of Service</span>
                    <span>Accessibility</span>
                </div>
            </div>
        </footer>

    </div>

    <!-- Live Countdown Script -->
    @push ('scripts')
        <script>
            (function() {
                var countdownDuration = (16 * 3600) + (43 * 60) + 54;
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

                    var elDays = document.getElementById('tst-days');
                    var elHours = document.getElementById('tst-hours');
                    var elMins = document.getElementById('tst-mins');
                    var elSecs = document.getElementById('tst-secs');

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
