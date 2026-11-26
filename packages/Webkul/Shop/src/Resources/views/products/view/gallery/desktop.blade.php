<!-- For large screens greater than 1180px. -->
<div class="sticky top-20 flex h-max gap-6 max-1180:hidden">
    <!-- Product Image and Videos Slider (Thumbnails) -->
    <div class="flex min-w-[85px] max-w-[85px] flex-col items-center gap-2">
        <!-- Arrow Up -->
        <span
            class="icon-arrow-up cursor-pointer text-xl text-slate-600 transition hover:text-black"
            role="button"
            aria-label="@lang('shop::app.components.products.carousel.previous')"
            tabindex="0"
            @click="swipeDown"
            v-if="lengthOfMedia"
        >
        </span>

        <!-- Swiper Container -->
        <div
            ref="swiperContainer"
            class="flex flex-col max-h-[520px] gap-2.5 overflow-y-auto scroll-smooth scrollbar-hide py-1"
        >
            <template v-for="(media, index) in [...media.images, ...media.videos]">
                <video
                    v-if="media.type == 'videos'"
                    :class="`h-[78px] w-[78px] min-w-[78px] cursor-pointer rounded-xl border-2 object-cover ${isActiveMedia(index) ? 'pointer-events-none border-navyBlue shadow-md' : 'border-slate-200 hover:border-slate-400'}`"
                    @click="change(media, index)"
                    alt="{{ $product->name }}"
                    tabindex="0"
                >
                    <source
                        :src="media.video_url"
                        type="video/mp4"
                    />
                </video>

                <div
                    v-else
                    :class="`h-[78px] w-[78px] min-w-[78px] cursor-pointer rounded-xl border-2 overflow-hidden bg-slate-50 p-1 flex items-center justify-center transition ${isActiveMedia(index) ? 'pointer-events-none border-navyBlue shadow-md' : 'border-slate-200 hover:border-slate-400'}`"
                    tabindex="0"
                    @click="change(media, index)"
                >
                    <img
                        class="h-full w-full object-contain"
                        :src="media.small_image_url"
                        :alt="media.alt"
                        width="70"
                        height="70"
                    />
                </div>
            </template>
        </div>

        <!-- Arrow Down -->
        <span
            class="icon-arrow-down cursor-pointer text-xl text-slate-600 transition hover:text-black"
            v-if="lengthOfMedia"
            role="button"
            aria-label="@lang('shop::app.components.products.carousel.previous')"
            tabindex="0"
            @click="swipeTop"
        >
        </span>
    </div>

    <!-- Product Base Image and Video with Shimmer-->
    <div
        class="product-gallery-img-container"
        v-show="isMediaLoading"
    >
        <div class="shimmer h-full w-full rounded-2xl bg-zinc-200"></div>
    </div>

    <div
        class="product-gallery-img-container"
        v-show="! isMediaLoading"
    >
        <img
            class="cursor-pointer transition-transform duration-300 hover:scale-105"
            :src="baseFile.path"
            v-if="baseFile.type == 'image'"
            :alt="baseFile.alt"
            width="560"
            height="580"
            tabindex="0"
            @click="isImageZooming = !isImageZooming"
            @load="onMediaLoad()"
            v-on:error="onMediaError($event)"
            fetchpriority="high"
        />

        <div
            class="flex h-full w-full items-center justify-center cursor-pointer rounded-xl"
            tabindex="0"
            v-if="baseFile.type == 'video'"
        >
            <video
                controls
                class="max-h-full max-w-full rounded-xl"
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
    </div>
</div>
