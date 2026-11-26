<!-- Desktop Gallery (Vertical Thumbnails on Left + Main Hero Card on Right) -->
<div class="sticky top-20 flex gap-4 max-1180:hidden" style="width: 610px; max-width: 100%;">
    <!-- Left: Vertical Thumbnails Slider -->
    <div class="aurora-v-thumb-col" v-if="[...media.images, ...media.videos].length > 1">
        <!-- Up Arrow -->
        <button
            type="button"
            class="aurora-thumb-arrow-btn"
            aria-label="@lang('shop::app.components.products.carousel.previous')"
            @click="swipeTop"
            v-if="lengthOfMedia"
        >
            <span class="icon-arrow-up text-xs"></span>
        </button>

        <!-- Vertical Swiper Container -->
        <div
            ref="swiperContainer"
            class="aurora-v-thumb-track"
        >
            <template v-for="(media, index) in [...media.images, ...media.videos]">
                <!-- Video Thumbnail -->
                <div
                    v-if="media.type == 'videos'"
                    :class="['aurora-v-thumb-slot', isActiveMedia(index) ? 'is-active' : '']"
                    @click="change(media, index)"
                >
                    <video
                        style="width: 100%; height: 100%; object-fit: cover;"
                        alt="{{ $product->name }}"
                    >
                        <source :src="media.video_url" type="video/mp4" />
                    </video>
                </div>

                <!-- Image Thumbnail -->
                <div
                    v-else
                    :class="['aurora-v-thumb-slot', isActiveMedia(index) ? 'is-active' : '']"
                    @click="change(media, index)"
                >
                    <img
                        :src="media.small_image_url"
                        :alt="media.alt"
                        style="width: 100%; height: 100%; object-fit: contain;"
                    />
                </div>
            </template>
        </div>

        <!-- Down Arrow -->
        <button
            type="button"
            class="aurora-thumb-arrow-btn"
            aria-label="@lang('shop::app.components.products.carousel.next')"
            @click="swipeDown"
            v-if="lengthOfMedia"
        >
            <span class="icon-arrow-down text-xs"></span>
        </button>
    </div>

    <!-- Right: Main Hero Big Image Container -->
    <div style="flex: 1 1 auto; max-width: 515px;">
        <div
            class="aurora-main-image-card"
            v-show="isMediaLoading"
        >
            <div class="shimmer" style="width: 100%; height: 100%; border-radius: 20px; background-color: #f1f5f9;"></div>
        </div>

        <div
            class="aurora-main-image-card"
            v-show="! isMediaLoading"
        >
            <img
                class="aurora-hero-image"
                :src="baseFile.path"
                v-if="baseFile.type == 'image'"
                :alt="baseFile.alt"
                tabindex="0"
                @click="isImageZooming = !isImageZooming"
                @load="onMediaLoad()"
                v-on:error="onMediaError($event)"
                fetchpriority="high"
            />

            <div
                style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;"
                tabindex="0"
                v-if="baseFile.type == 'video'"
            >
                <video
                    controls
                    style="max-width: 100%; max-height: 100%; border-radius: 16px;"
                    alt="{{ $product->name }}"
                    @click="isImageZooming = !isImageZooming"
                    @loadeddata="onMediaLoad()"
                    :key="baseFile.path"
                >
                    <source
                        :src="baseFile.path"
                        type="video/mp4"
                    />
                </video>
            </div>

            <!-- Zoom Icon Overlay -->
            <span
                class="aurora-zoom-btn"
                @click="isImageZooming = !isImageZooming"
                title="Click to zoom"
            >
                <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7"/>
                </svg>
            </span>
        </div>
    </div>
</div>
