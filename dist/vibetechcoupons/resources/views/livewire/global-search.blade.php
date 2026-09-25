<div class="relative" wire:key="global-search">
    <div class="relative">
        <svg class="pointer-events-none absolute left-4 top-1/2 size-4 -translate-y-1/2 text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.35-4.35m1.35-5.65a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z"/>
        </svg>

        <input
            wire:model.live.debounce.350ms="query"
            wire:keydown.enter.prevent
            type="search"
            autocomplete="off"
            placeholder="Search stores, coupons or reviews"
            class="w-full rounded-full border border-stone-300 bg-white px-11 py-3 text-sm outline-none transition placeholder:text-stone-400 focus:border-emerald-700 focus:ring-4 focus:ring-emerald-700/10"
        >

        <div wire:loading.delay wire:target="query" class="absolute right-4 top-1/2 -translate-y-1/2" aria-label="Searching">
            <svg class="size-4 animate-spin text-emerald-800" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v4a4 4 0 0 0-4 4H4Z"></path>
            </svg>
        </div>

        @if($query !== '')
            <button
                type="button"
                wire:click.prevent.stop="clearSearch"
                wire:loading.remove
                wire:target="query,clearSearch"
                class="absolute right-3 top-1/2 grid size-7 -translate-y-1/2 place-items-center rounded-full text-stone-400 hover:bg-stone-100 hover:text-stone-700"
                aria-label="Clear search"
            >
                ×
            </button>
        @endif
    </div>

    @if(mb_strlen(trim($query)) >= 2)
        <div class="absolute right-0 top-full z-50 mt-2 w-full min-w-[320px] overflow-hidden rounded-2xl border border-stone-200 bg-white p-2 shadow-2xl shadow-stone-900/10">
            @if($stores->isEmpty() && $coupons->isEmpty() && $reviews->isEmpty())
                <div class="p-5 text-sm text-stone-500">
                    No results for “{{ $query }}”.
                    <a href="{{ route('search', ['q' => $query]) }}" class="ml-1 font-bold text-emerald-800 hover:underline">Search all</a>
                </div>
            @else
                @if($stores->isNotEmpty())
                    <div class="px-3 pb-1 pt-2 text-[11px] font-black uppercase tracking-[.18em] text-stone-400">Stores</div>
                    @foreach($stores as $store)
                        <a wire:key="search-store-{{ $store->id }}" href="{{ route('stores.show', $store) }}" class="flex items-center gap-3 rounded-xl px-3 py-2 hover:bg-stone-50">
                            <span class="grid size-9 place-items-center rounded-lg bg-emerald-50 font-black text-emerald-900">@if($store->logo_url)<img src="{{ $store->logo_url }}" alt="{{ $store->name }}" loading="lazy" width="96" height="96" class="size-full rounded-[inherit] object-contain">@else{{ mb_strtoupper(mb_substr($store->name, 0, 1)) }}@endif</span>
                            <span class="text-sm font-semibold">{{ $store->name }}</span>
                        </a>
                    @endforeach
                @endif

                @if($coupons->isNotEmpty())
                    <div class="px-3 pb-1 pt-3 text-[11px] font-black uppercase tracking-[.18em] text-stone-400">Coupons</div>
                    @foreach($coupons as $coupon)
                        <a wire:key="search-coupon-{{ $coupon->id }}" href="{{ route('stores.show', $coupon->store) }}" class="block rounded-xl px-3 py-2 hover:bg-stone-50">
                            <div class="text-sm font-semibold">{{ $coupon->title }}</div>
                            <div class="mt-0.5 text-xs text-stone-500">{{ $coupon->store->name }}</div>
                        </a>
                    @endforeach
                @endif

                @if($reviews->isNotEmpty())
                    <div class="px-3 pb-1 pt-3 text-[11px] font-black uppercase tracking-[.18em] text-stone-400">Reviews</div>
                    @foreach($reviews as $review)
                        <a wire:key="search-review-{{ $review->id }}" href="{{ route('reviews.show', $review) }}" class="block rounded-xl px-3 py-2 text-sm font-semibold hover:bg-stone-50">{{ $review->title }}</a>
                    @endforeach
                @endif

                <div class="mt-1 border-t border-stone-100 p-2">
                    <a href="{{ route('search', ['q' => $query]) }}" class="block rounded-xl px-3 py-2 text-center text-sm font-black text-emerald-800 hover:bg-emerald-50">
                        View all results
                    </a>
                </div>
            @endif
        </div>
    @endif
</div>
