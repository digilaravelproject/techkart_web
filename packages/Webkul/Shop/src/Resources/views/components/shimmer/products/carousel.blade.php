<div class="mx-auto mt-14 max-w-[1360px] px-6 max-md:mt-9 max-md:px-3.5">
    <div class="mb-6 flex items-center justify-between gap-4">
        <h3 class="shimmer h-9 w-[220px] rounded-lg max-sm:h-7 max-sm:w-[160px]"></h3>

        <div class="flex items-center gap-2.5">
            <span
                class="shimmer inline-block h-[38px] w-[38px] rounded-full max-md:hidden"
                role="presentation"
            ></span>

            <span
                class="shimmer inline-block h-[38px] w-[38px] rounded-full max-md:hidden"
                role="presentation"
            ></span>

            @if ($navigationLink)
                <span
                    class="shimmer inline-block h-[36px] w-[104px] rounded-full max-sm:h-[30px] max-sm:w-[84px]"
                    role="presentation"
                ></span>
            @endif
        </div>
    </div>

    <div class="scrollbar-hide flex gap-5 overflow-auto pb-3 max-md:gap-3">
        <x-shop::shimmer.products.cards.grid
            class="min-w-[240px] flex-[0_0_calc(25%-15px)] max-md:min-w-[168px] max-md:flex-[0_0_calc(50%-6px)]"
            :count="4"
        />
    </div>
</div>
