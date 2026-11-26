<v-product-gallery ref="gallery">
    <x-shop::shimmer.products.gallery />
</v-product-gallery>

@pushOnce('styles')
    <style>
        /* AURORA Modern E-Commerce Product Gallery */
        .aurora-main-image-card {
            position: relative;
            width: 100%;
            height: 580px;
            max-height: 580px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            border-radius: 24px;
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            padding: 16px;
            box-sizing: border-box;
            transition: box-shadow 0.25s ease;
        }
        .aurora-main-image-card:hover {
            box-shadow: 0 16px 36px rgba(15, 23, 42, 0.07);
        }
        .aurora-hero-image {
            max-height: 100% !important;
            max-width: 100% !important;
            width: auto !important;
            height: auto !important;
            object-fit: contain !important;
            object-position: center !important;
            display: block !important;
            border-radius: 16px;
            cursor: pointer;
            transition: transform 0.35s ease;
        }
        .aurora-main-image-card:hover .aurora-hero-image {
            transform: scale(1.03);
        }
        .aurora-zoom-btn {
            position: absolute;
            top: 16px;
            right: 16px;
            width: 36px;
            height: 36px;
            border-radius: 9999px;
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #334155;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            transition: all 0.2s ease;
        }
        .aurora-zoom-btn:hover {
            background: #0f172a;
            color: #ffffff;
            border-color: #0f172a;
        }
        /* Vertical Left Thumbnails Column */
        .aurora-v-thumb-col {
            width: 82px;
            min-width: 82px;
            max-width: 82px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            flex-shrink: 0;
        }
        .aurora-v-thumb-track {
            display: flex;
            flex-direction: column;
            gap: 10px;
            max-height: 520px;
            overflow-y: auto;
            scrollbar-width: none;
            -ms-overflow-style: none;
            width: 100%;
            align-items: center;
            padding: 2px 0;
        }
        .aurora-v-thumb-track::-webkit-scrollbar {
            display: none;
        }
        .aurora-v-thumb-slot {
            width: 78px !important;
            height: 78px !important;
            min-width: 78px !important;
            min-height: 78px !important;
            max-width: 78px !important;
            max-height: 78px !important;
            border-radius: 14px;
            border: 2px solid #e2e8f0;
            background: #f8fafc;
            overflow: hidden;
            padding: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
            box-sizing: border-box;
            flex-shrink: 0;
        }
        .aurora-v-thumb-slot:hover {
            border-color: #94a3b8;
            transform: translateY(-2px);
        }
        .aurora-v-thumb-slot.is-active {
            border-color: #0f172a !important;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.15);
            background: #ffffff;
        }
        .aurora-v-thumb-slot img {
            width: 100% !important;
            height: 100% !important;
            max-width: 100% !important;
            max-height: 100% !important;
            object-fit: contain !important;
            display: block !important;
        }
        .aurora-thumb-arrow-btn {
            width: 28px;
            height: 28px;
            min-width: 28px;
            border-radius: 9999px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #475569;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .aurora-thumb-arrow-btn:hover {
            background: #0f172a;
            color: #ffffff;
            border-color: #0f172a;
        }
    </style>
@endPushOnce

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-product-gallery-template"
    >
        <div>
            <!-- Desktop Gallery -->
            @include('shop::products.view.gallery.desktop')

            <!-- Mobile Gallery -->
            @include('shop::products.view.gallery.mobile')
            
            <!-- Gallery Images Zoomer -->
            <x-shop::image-zoomer
                ::attachments="attachments"
                ::is-image-zooming="isImageZooming"
                ::initial-index="`media_${activeIndex}`"
            />
        </div>
    </script>

    <script type="module">
        app.component('v-product-gallery', {
            template: '#v-product-gallery-template',

            data() {
                return {
                    isImageZooming: false,

                    isMediaLoading: true,

                    placeholderUrl: @js(product_image()->getPlaceholderUrl('large')),

                    media: {
                        images: @json(product_image()->getGalleryImages($product)),

                        videos: @json(product_video()->getVideos($product)),
                    },

                    baseFile: {
                        type: '',

                        path: '',

                        alt: "{{ $product->name }}"
                    },

                    activeIndex: 0,

                    containerOffset: 110,
                };
            },

            watch: {
                'media.images': {
                    deep: true,

                    handler(newImages, oldImages) {
                        let selectedImage = newImages?.[this.activeIndex];

                        if (JSON.stringify(newImages) !== JSON.stringify(oldImages) && selectedImage?.large_image_url) {
                            this.baseFile.path = selectedImage.large_image_url;

                            this.baseFile.alt = selectedImage.alt;
                        }
                    },
                },
            },
        
            mounted() {
                if (this.media.images.length) {

                    this.baseFile.type = 'image';

                    this.baseFile.path = this.media.images[0].large_image_url;

                    this.baseFile.alt = this.media.images[0].alt;
                } else if (this.media.videos.length) {

                    this.baseFile.type = 'video';

                    this.baseFile.path = this.media.videos[0].video_url;
                }
            },

            computed: {
                lengthOfMedia() {
                    if (this.media.images.length) {
                        return [...this.media.images, ...this.media.videos].length > 5;
                    }
                },

                attachments() {
                    return [...this.media.images, ...this.media.videos].map(media => ({
                        url: media.type === 'videos' ? media.video_url : media.original_image_url,

                        type: media.type === 'videos' ? 'video' : 'image',

                        alt: media.alt ?? "{{ $product->name }}",
                    }));
                },
            },

            methods: {
                isActiveMedia(index) {
                    return index === this.activeIndex;
                },
                
                onMediaLoad() {
                    this.isMediaLoading = false;
                },

                onMediaError(event) {
                    this.isMediaLoading = false;

                    if (event?.target && event.target.src !== this.placeholderUrl) {
                        event.target.src = this.placeholderUrl;
                    }
                },

                change(media, index) {
                    this.isMediaLoading = true;

                    if (media.type == 'videos') {
                        this.baseFile.type = 'video';

                        this.baseFile.path = media.video_url;

                        this.onMediaLoad();
                    } else {
                        this.baseFile.type = 'image';

                        this.baseFile.path = media.large_image_url;

                        this.baseFile.alt = media.alt;
                    }

                    if (index > this.activeIndex) {
                        this.swipeDown();
                    } else if (index < this.activeIndex) {
                        this.swipeTop();
                    }

                    this.activeIndex = index;
                },

                swipeTop() {
                    const container = this.$refs.swiperContainer;

                    if (container) {
                        container.scrollLeft -= this.containerOffset;
                        container.scrollTop -= this.containerOffset;
                    }
                },

                swipeDown() {
                    const container = this.$refs.swiperContainer;

                    if (container) {
                        container.scrollLeft += this.containerOffset;
                        container.scrollTop += this.containerOffset;
                    }
                },
            },
        });
    </script>
@endpushOnce
