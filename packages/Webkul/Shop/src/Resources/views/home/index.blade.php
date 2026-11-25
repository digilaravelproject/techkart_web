@php
    $channel = core()->getCurrentChannel();
@endphp

<!-- SEO Meta Content -->
@push ('meta')
    <meta
        name="title"
        content="{{ $channel->home_seo['meta_title'] ?? '' }}"
    />

    <meta
        name="description"
        content="{{ $channel->home_seo['meta_description'] ?? '' }}"
    />

    <meta
        name="keywords"
        content="{{ $channel->home_seo['meta_keywords'] ?? '' }}"
    />
@endPush

@push('scripts')
    @if(! empty($categories))
        <script>
            localStorage.setItem('categories', JSON.stringify(@json($categories)));
        </script>
    @endif
@endpush

@push('styles')
    <style>
        .home-static-gallery {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 24px;
            margin: 48px auto;
            padding: 0 60px;
            align-items: stretch;
        }
        .home-static-gallery img {
            width: 100%;
            height: 280px;
            object-fit: cover;
            border-radius: 16px;
            background-color: #f5f5f5;
            border: 1px solid #e5e7eb;
            transition: transform 300ms ease, box-shadow 300ms ease;
        }
        .home-static-gallery img:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.08);
        }
        .bold-collections .inline-col-image-wrapper img {
            width: 100%;
            max-width: 632px;
            height: auto;
            object-fit: cover;
            border-radius: 16px;
        }
        #main > img,
        #main > p > img {
            width: calc(100% - 30px);
            max-width: 1180px;
            max-height: 360px;
            object-fit: contain;
            margin: 24px auto;
            display: block;
            border-radius: 16px;
            background-color: #f8fafc;
            border: 1px solid #e5e7eb;
            padding: 16px;
        }
        @media (max-width: 1024px) {
            .home-static-gallery {
                padding: 0 30px;
                gap: 16px;
            }
        }
        @media (max-width: 640px) {
            .home-static-gallery {
                grid-template-columns: repeat(2, 1fr);
                padding: 0 15px;
                gap: 12px;
                margin: 28px auto;
            }
            .home-static-gallery img {
                height: 180px;
                border-radius: 12px;
            }
        }
    </style>
@endpush

<x-shop::layouts>
    <!-- Page Title -->
    <x-slot:title>
        {{  $channel->home_seo['meta_title'] ?? '' }}
    </x-slot>

    <!-- Loop over the storefront sections -->
    @foreach ($sections as $section)
        @php ($data = $section->options) @endphp

        {{-- The layout marks the types it draws on every page, so this page marks the rest. --}}
        @php ($marks = ($preview ?? false) && ! $section->getTypeInstance()?->rendersInLayout())

        @if ($marks)
            <div
                data-section-id="{{ $section->id }}"
                data-section-name="{{ $section->name }}"
            >
        @endif

        <!-- Static Content -->
        @switch ($section->type)
            @case (\Webkul\Theme\Enums\SectionTypeEnum::IMAGE_CAROUSEL->value)
                <!-- Image Carousel -->
                <x-shop::carousel
                    :options="$section->getTypeInstance()?->sanitize((array) $data) ?? $data"
                    aria-label="{{ trans('shop::app.home.index.image-carousel') }}"
                />

                @break
            @case (\Webkul\Theme\Enums\SectionTypeEnum::STATIC_CONTENT->value)
                <!-- Push Style -->
                @if (! empty($data['css']))
                    @push ('styles')
                        <style>
                            {!! $data['css'] !!}
                        </style>
                    @endpush
                @endif

                <!-- Render HTML -->
                @if (! empty($data['html']))
                    {!! preg_replace('/^(\s*<img\b[^>]*\/?>\s*)+/i', '<div class="home-static-gallery container">$0</div>', $data['html']) !!}
                @endif

                @break
            @case (\Webkul\Theme\Enums\SectionTypeEnum::CATEGORY_CAROUSEL->value)
                <!-- Categories carousel -->
                <x-shop::categories.carousel
                    :title="$data['title'] ?? ''"
                    :src="route('shop.api.categories.index', $data['filters'] ?? [])"
                    :navigation-link="route('shop.home.index')"
                    aria-label="{{ trans('shop::app.home.index.categories-carousel') }}"
                />

                @break
            @case (\Webkul\Theme\Enums\SectionTypeEnum::PRODUCT_CAROUSEL->value)
                <!-- Product Carousel -->
                <x-shop::products.carousel
                    :title="$data['title'] ?? ''"
                    :src="route('shop.api.products.index', $data['filters'] ?? [])"
                    :navigation-link="route('shop.search.index', $data['filters'] ?? [])"
                    aria-label="{{ trans('shop::app.home.index.product-carousel') }}"
                />

                @break
        @endswitch

        @if ($marks)
            </div>
        @endif
    @endforeach

    @if ($preview ?? false)
        @include('shop::home.preview-bridge')
    @endif
</x-shop::layouts>
