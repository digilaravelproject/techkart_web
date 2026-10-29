<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Webkul\Category\Repositories\CategoryRepository;
use Webkul\Core\Models\Channel;
use Webkul\Product\Models\Product;
use Webkul\Product\Models\ProductImage;
use Webkul\Product\Repositories\ProductRepository;

class TestamentThemeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command?->info('Beginning Testament Shopify Theme Setup & Seeding...');

        // 1. Create or ensure Categories exist
        $categoryRepo = app(CategoryRepository::class);
        $rootCategory = $categoryRepo->findWhere(['parent_id' => null])->first();
        $rootId = $rootCategory ? $rootCategory->id : 1;

        $categoriesData = [
            [
                'name' => 'Summer Ready Styles',
                'slug' => 'summer-ready-styles',
                'description' => 'Embrace the season in style with our latest summer collection.',
            ],
            [
                'name' => 'Trending Now',
                'slug' => 'trending-now',
                'description' => 'The hottest trending styles of the season.',
            ],
            [
                'name' => 'New Prints',
                'slug' => 'new-prints',
                'description' => 'Fresh designs and newly added prints.',
            ],
            [
                'name' => 'Playsuits & Dresses',
                'slug' => 'playsuits-dresses',
                'description' => 'Light, bright and easy relaxed playsuits and dresses.',
            ],
        ];

        $categoryIds = [];

        foreach ($categoriesData as $cat) {
            $existing = DB::table('category_translations')
                ->where('slug', $cat['slug'])
                ->first();

            if ($existing) {
                $categoryIds[$cat['slug']] = $existing->category_id;
            } else {
                try {
                    $newCategory = $categoryRepo->create([
                        'locale'       => 'all',
                        'name'         => $cat['name'],
                        'slug'         => $cat['slug'],
                        'description'  => $cat['description'],
                        'status'       => 1,
                        'position'     => 1,
                        'display_mode' => 'products_and_description',
                        'parent_id'    => $rootId,
                    ]);
                    $categoryIds[$cat['slug']] = $newCategory->id;
                } catch (\Throwable $e) {
                    $this->command?->warn("Category creation note: " . $e->getMessage());
                    $categoryIds[$cat['slug']] = $rootId;
                }
            }
        }

        // 2. Prepare Storage Directory for Images
        $storageDir = storage_path('app/public/products/testament');
        if (! File::isDirectory($storageDir)) {
            File::makeDirectory($storageDir, 0755, true, true);
        }

        // 3. Define the Testament Products
        $products = [
            [
                'sku'               => 'TESTAMENT-WILLA',
                'name'              => 'Willa Playsuit',
                'url_key'           => 'willa-playsuit',
                'price'             => 79.00,
                'special_price'     => 59.00,
                'badge'             => 'BESTSELLER',
                'short_description' => 'The Willa Playsuit is the perfect fit for summer. Light, bright and easy for relaxed occasions.',
                'description'       => 'The Willa Playsuit is the perfect fit for summer. Light, bright and easy for relaxed occasions. Tailored from premium breathable cotton-linen blend with an adjustable waist tie and relaxed leg opening.',
                'categories'        => [$categoryIds['playsuits-dresses'] ?? $rootId, $categoryIds['summer-ready-styles'] ?? $rootId],
                'image_url'         => 'https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?w=1000&auto=format&fit=crop',
                'filename'          => 'willa-playsuit.jpg',
            ],
            [
                'sku'               => 'TESTAMENT-SUMMER-01',
                'name'              => 'Summer Breeze Linen Maxi Dress',
                'url_key'           => 'summer-breeze-linen-maxi-dress',
                'price'             => 89.00,
                'special_price'     => 69.00,
                'badge'             => 'SAVE 22%',
                'short_description' => 'Flowing linen maxi dress designed for effortlessly chic summer style.',
                'description'       => 'Embrace the season in style with our latest collection, curated to keep you looking effortlessly chic all summer long. Crafted from 100% pure European flax linen.',
                'categories'        => [$categoryIds['summer-ready-styles'] ?? $rootId, $categoryIds['trending-now'] ?? $rootId],
                'image_url'         => 'https://images.unsplash.com/photo-1496747611176-843222e1e57c?w=1000&auto=format&fit=crop',
                'filename'          => 'summer-breeze-maxi.jpg',
            ],
            [
                'sku'               => 'TESTAMENT-PRINTS-02',
                'name'              => 'Fresh Botanical Print Playsuit',
                'url_key'           => 'fresh-botanical-print-playsuit',
                'price'             => 74.00,
                'special_price'     => 54.00,
                'badge'             => 'NEW PRINTS',
                'short_description' => 'Shop fresh designs: new botanical prints just added!',
                'description'       => 'Vibrant custom hand-painted botanical prints on crisp airy cotton poplin. Perfect for sunny escapes, weekend markets, and beachside lunches.',
                'categories'        => [$categoryIds['new-prints'] ?? $rootId, $categoryIds['playsuits-dresses'] ?? $rootId],
                'image_url'         => 'https://images.unsplash.com/photo-1572804013309-59a88b7e92f1?w=1000&auto=format&fit=crop',
                'filename'          => 'fresh-botanical-playsuit.jpg',
            ],
            [
                'sku'               => 'TESTAMENT-SILK-03',
                'name'              => 'Effortlessly Chic Silk Resort Blouse',
                'url_key'           => 'effortlessly-chic-silk-resort-blouse',
                'price'             => 65.00,
                'special_price'     => 49.00,
                'badge'             => 'FLASH SALE - 17%',
                'short_description' => 'Relaxed drape silk button-down shirt in champagne stone.',
                'description'       => 'A timeless summer staple. Designed with fluid silk drape, notched collar, mother-of-pearl buttons, and relaxed shoulders.',
                'categories'        => [$categoryIds['summer-ready-styles'] ?? $rootId, $categoryIds['trending-now'] ?? $rootId],
                'image_url'         => 'https://images.unsplash.com/photo-1539109136881-3be0616acf4b?w=1000&auto=format&fit=crop',
                'filename'          => 'effortless-silk-blouse.jpg',
            ],
            [
                'sku'               => 'TESTAMENT-RIVIERA-04',
                'name'              => 'Riviera Halter Neck Sundress',
                'url_key'           => 'riviera-halter-neck-sundress',
                'price'             => 95.00,
                'special_price'     => 75.00,
                'badge'             => 'TRENDING',
                'short_description' => 'Sophisticated Mediterranean-inspired backless sundress.',
                'description'       => 'Capturing the romance of the French Riviera. Features delicate halter neck straps, self-adjusting back ties, and soft tiered silhouette.',
                'categories'        => [$categoryIds['trending-now'] ?? $rootId, $categoryIds['summer-ready-styles'] ?? $rootId],
                'image_url'         => 'https://images.unsplash.com/photo-1509631179647-0177331693ae?w=1000&auto=format&fit=crop',
                'filename'          => 'riviera-halter-sundress.jpg',
            ],
            [
                'sku'               => 'TESTAMENT-COASTAL-05',
                'name'              => 'Coastal Wide-Leg Linen Trouser',
                'url_key'           => 'coastal-wide-leg-linen-trouser',
                'price'             => 69.00,
                'special_price'     => 52.00,
                'badge'             => '14% OFF TODAY',
                'short_description' => 'Relaxed high-rise trousers crafted from pure washed European linen.',
                'description'       => 'Effortless elegance for every warm weather occasion. Features a comfortable stretch drawstring waistband and deep side pockets.',
                'categories'        => [$categoryIds['trending-now'] ?? $rootId, $categoryIds['summer-ready-styles'] ?? $rootId],
                'image_url'         => 'https://images.unsplash.com/photo-1551803091-e20673f15770?w=1000&auto=format&fit=crop',
                'filename'          => 'coastal-wide-leg-trouser.jpg',
            ],
            [
                'sku'               => 'TESTAMENT-AMALFI-06',
                'name'              => 'Amalfi Cowl Neck Silk Slip Dress',
                'url_key'           => 'amalfi-cowl-neck-silk-slip-dress',
                'price'             => 88.00,
                'special_price'     => 68.00,
                'badge'             => 'PRE ORDER - 22%',
                'short_description' => 'Minimalist 90s-inspired slip dress with fluid satin finish.',
                'description'       => 'Cut on the bias for a contour-skimming drape. Styled with adjustable cross back straps and a graceful cowl neckline.',
                'categories'        => [$categoryIds['trending-now'] ?? $rootId, $categoryIds['playsuits-dresses'] ?? $rootId],
                'image_url'         => 'https://images.unsplash.com/photo-1502716119720-b23a93e5fe1b?w=1000&auto=format&fit=crop',
                'filename'          => 'amalfi-slip-dress.jpg',
            ],
            [
                'sku'               => 'TESTAMENT-SUNSET-07',
                'name'              => 'Sunset Terracotta Ruffle Playsuit',
                'url_key'           => 'sunset-terracotta-ruffle-playsuit',
                'price'             => 82.00,
                'special_price'     => 64.00,
                'badge'             => 'SUMMER READY',
                'short_description' => 'Playful ruffle trim playsuit in rich sunset terracotta.',
                'description'       => 'A joyful vacation essential with flutter sleeves, deep V-neck, and elasticated waist with removable belt sash.',
                'categories'        => [$categoryIds['playsuits-dresses'] ?? $rootId, $categoryIds['summer-ready-styles'] ?? $rootId],
                'image_url'         => 'https://images.unsplash.com/photo-1529139574466-a303027c1d8b?w=1000&auto=format&fit=crop',
                'filename'          => 'sunset-terracotta-playsuit.jpg',
            ],
        ];

        // 4. Download images or generate fallback SVG
        foreach ($products as &$pData) {
            $destPath = $storageDir . '/' . $pData['filename'];
            $relPath = 'products/testament/' . $pData['filename'];

            if (! File::exists($destPath)) {
                try {
                    $context = stream_context_create([
                        'http' => ['timeout' => 5, 'header' => "User-Agent: Mozilla/5.0\r\n"],
                        'ssl'  => ['verify_peer' => false, 'verify_peer_name' => false],
                    ]);
                    $content = @file_get_contents($pData['image_url'], false, $context);
                    if ($content && strlen($content) > 1000) {
                        File::put($destPath, $content);
                    }
                } catch (\Throwable $e) {
                    // Fallback to placeholder image
                }

                // If download failed or was empty, create placeholder image
                if (! File::exists($destPath) || File::size($destPath) === 0) {
                    // Copy existing product image if available, else create SVG
                    $fallback = storage_path('app/public/products/1/7AkJibFVtTLkq4DQdEaCBoJO8scuJbc4GMpGU1HR.webp');
                    if (File::exists($fallback)) {
                        File::copy($fallback, $destPath);
                    }
                }
            }

            $pData['db_image_path'] = $relPath;
        }

        // 5. Create or Update Products using ProductRepository
        $productRepo = app(ProductRepository::class);
        \Illuminate\Database\Eloquent\Model::reguard();

        foreach ($products as $item) {
            $existing = Product::where('sku', $item['sku'])->first();

            if ($existing && $existing->attribute_values()->exists()) {
                continue;
            }

            try {
                \Illuminate\Database\Eloquent\Model::reguard();

                if (! $existing) {
                    $product = $productRepo->create([
                        'type'                => 'simple',
                        'attribute_family_id' => 1,
                        'sku'                 => $item['sku'],
                    ]);
                } else {
                    $product = $existing;
                }

                $updateData = [
                    'sku'                  => $item['sku'],
                    'name'                 => $item['name'],
                    'url_key'              => $item['url_key'] . '-' . $product->id,
                    'new'                  => 1,
                    'featured'             => 1,
                    'status'               => 1,
                    'visible_individually' => 1,
                    'price'                => $item['price'],
                    'special_price'        => $item['special_price'],
                    'short_description'    => $item['short_description'],
                    'description'          => $item['description'],
                    'meta_title'           => $item['name'] . ' | The Testament',
                    'meta_keywords'        => 'testament, shopify, ' . $item['name'] . ', summer fashion',
                    'meta_description'     => $item['short_description'],
                    'channel'              => 'default',
                    'locale'               => 'en',
                    'channels'             => [1],
                    'categories'           => array_merge([$rootId], $item['categories']),
                    'inventories'          => [1 => 100],
                ];

                $productRepo->update($updateData, $product->id);

                // Add product image if not present
                $hasImage = ProductImage::where('product_id', $product->id)->exists();
                if (! $hasImage && File::exists(storage_path('app/public/' . $item['db_image_path']))) {
                    ProductImage::create([
                        'type'       => 'images',
                        'path'       => $item['db_image_path'],
                        'product_id' => $product->id,
                        'position'   => 1,
                    ]);
                }
            } catch (\Throwable $e) {
                $this->command?->warn("Product {$item['sku']} notice: " . $e->getMessage());
            }
        }

        // 6. Reindex Product Flat and Prices
        try {
            app(\Webkul\Product\Helpers\Indexers\Flat::class)->reindexFull();
            \Illuminate\Support\Facades\Artisan::call('indexer:index');
        } catch (\Throwable $e) {
            $this->command?->warn("Reindex note: " . $e->getMessage());
        }

        // 7. Activate 'testament' theme on Channel 1
        $channel = Channel::find(1);
        if ($channel) {
            $channel->theme = 'testament';
            $channel->save();
            $this->command?->info("Default channel theme updated to 'testament'!");
        }

        // 8. Seed Bagisto Admin Appearance Sections for Testament Theme
        $this->seedThemeSections($channel ? $channel->id : 1);

        $this->command?->info('Testament products, categories, images & theme activated successfully!');
    }

    /**
     * Seed Bagisto Admin Appearance Sections for 'testament' theme.
     */
    protected function seedThemeSections(int $channelId): void
    {
        $existing = DB::table('theme_sections')
            ->where('theme_code', 'testament')
            ->where('channel_id', $channelId)
            ->exists();

        if ($existing) {
            $this->command?->info("Theme sections for 'testament' already exist in admin. Skipping.");
            return;
        }

        $now = Carbon::now();
        $locales = ['en'];

        $sections = [
            [
                'type'       => 'static_content',
                'name'       => 'Top Announcement Bar',
                'sort_order' => 1,
                'options'    => [
                    'html' => 'COMPLIMENTARY WORLDWIDE SHIPPING ON ORDERS OVER $150 • CODE: SUMMER50',
                    'css'  => '',
                ],
            ],
            [
                'type'       => 'image_carousel',
                'name'       => 'Hero Main Banner',
                'sort_order' => 2,
                'options'    => [
                    'images' => [
                        [
                            'image' => 'https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?w=1920&auto=format&fit=crop&q=85',
                            'title' => "Fresh New Arrivals <br/> <em>Effortless Elegance</em>",
                            'link'  => '#trending',
                        ],
                    ],
                ],
            ],
            [
                'type'       => 'static_content',
                'name'       => 'Sale Countdown Bar',
                'sort_order' => 3,
                'options'    => [
                    'html' => 'SALE NOW ON - LIMITED TIME',
                    'css'  => '',
                ],
            ],
            [
                'type'       => 'product_carousel',
                'name'       => 'Trending Now',
                'sort_order' => 4,
                'options'    => [
                    'title'   => 'Trending Now',
                    'filters' => ['sort' => 'desc', 'limit' => 4],
                ],
            ],
            [
                'type'       => 'category_carousel',
                'name'       => 'Shop by Category',
                'sort_order' => 5,
                'options'    => [
                    'title'   => 'Shop by Category',
                    'filters' => [],
                ],
            ],
            [
                'type'       => 'static_content',
                'name'       => 'Summer Ready Styles Callout',
                'sort_order' => 6,
                'options'    => [
                    'html' => 'Embrace the season in style with our latest collection, curated to keep you looking effortlessly chic all summer long.',
                    'css'  => '',
                ],
            ],
            [
                'type'       => 'product_carousel',
                'name'       => 'Just Arrived',
                'sort_order' => 7,
                'options'    => [
                    'title'   => 'Just Arrived',
                    'filters' => ['sort' => 'desc', 'limit' => 4],
                ],
            ],
            [
                'type'       => 'static_content',
                'name'       => 'Willa Playsuit Spotlight',
                'sort_order' => 8,
                'options'    => [
                    'html' => 'The Willa Playsuit is the perfect fit for summer. Light, Bright and easy for relaxed occasions.',
                    'css'  => '',
                ],
            ],
            [
                'type'       => 'footer_links',
                'name'       => 'Footer Links',
                'sort_order' => 9,
                'options'    => [
                    'column_1' => [
                        ['title' => 'About Us', 'url' => '/page/about-us'],
                        ['title' => 'Contact Us', 'url' => '/contact-us'],
                        ['title' => 'Customer Service', 'url' => '/page/customer-service'],
                    ],
                    'column_2' => [
                        ['title' => 'Privacy Policy', 'url' => '/page/privacy-policy'],
                        ['title' => 'Terms of Use', 'url' => '/page/terms-of-use'],
                        ['title' => 'Shipping Policy', 'url' => '/page/shipping-policy'],
                    ],
                ],
            ],
        ];

        foreach ($sections as $sec) {
            $sectionId = DB::table('theme_sections')->insertGetId([
                'theme_code' => 'testament',
                'type'       => $sec['type'],
                'name'       => $sec['name'],
                'sort_order' => $sec['sort_order'],
                'status'     => 1,
                'channel_id' => $channelId,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            foreach ($locales as $locale) {
                DB::table('theme_section_translations')->insert([
                    'section_id' => $sectionId,
                    'locale'     => $locale,
                    'options'    => json_encode($sec['options']),
                ]);
            }
        }

        $this->command?->info("Seeded 9 Admin Manageable Appearance Sections for 'testament' theme!");
    }
}

