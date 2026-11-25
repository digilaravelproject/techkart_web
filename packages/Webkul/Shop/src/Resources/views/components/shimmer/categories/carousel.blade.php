@props(['count' => 0])

<div class="mx-auto mt-11 max-w-[1360px] px-6 max-lg:mt-8 max-lg:px-4 max-sm:mt-6 max-sm:px-3">
    <div class="scrollbar-hide flex justify-center gap-7 overflow-auto py-2 max-lg:gap-5 max-sm:gap-3.5">
        @for ($i = 0; $i < min($count, 6); $i++)
            <div class="flex w-[112px] flex-shrink-0 flex-col items-center gap-3 max-lg:w-[92px] max-sm:w-[76px] max-sm:gap-2">
                <div class="shimmer h-[100px] w-[100px] overflow-hidden rounded-full max-lg:h-[84px] max-lg:w-[84px] max-sm:h-[68px] max-sm:w-[68px]"></div>

                <p class="shimmer h-4 w-[84px] rounded-full max-sm:h-3.5 max-sm:w-[64px]"></p>
            </div>
        @endfor
    </div>
</div>
