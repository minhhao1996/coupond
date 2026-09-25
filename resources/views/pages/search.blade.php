@extends('layouts.app')

@section('content')
<section class="mx-auto max-w-7xl px-5 py-12 lg:px-8">
    <div class="rounded-[2rem] border border-stone-200 bg-gradient-to-br from-stone-100 to-emerald-50/60 p-8 md:p-12">
        <div class="eyebrow">Search</div>
        <h1 class="mt-3 text-4xl font-black tracking-tight text-stone-950 md:text-5xl">Find stores, coupons & reviews</h1>
        <p class="mt-4 max-w-2xl text-stone-600">Search the directory without relying on JavaScript. Live suggestions in the header are an enhancement; this page remains the reliable fallback.</p>

        <form action="{{ route('search') }}" method="GET" class="mt-7 flex max-w-2xl gap-3">
            <input name="q" value="{{ $query }}" type="search" minlength="2" placeholder="Try: Wattcycle, camping, 10% off..." class="min-w-0 flex-1 rounded-2xl border border-stone-300 bg-white px-5 py-3 outline-none focus:border-emerald-700 focus:ring-4 focus:ring-emerald-700/10">
            <button class="rounded-2xl bg-emerald-900 px-6 py-3 font-black text-white hover:bg-emerald-800">Search</button>
        </form>
    </div>

    @if(mb_strlen($query) < 2)
        <div class="mt-10 rounded-2xl border border-stone-200 bg-white p-6 text-stone-600">Enter at least 2 characters to search.</div>
    @else
        <div class="mt-12 space-y-12">
            <section>
                <div class="flex items-end justify-between gap-4">
                    <div>
                        <div class="eyebrow">Stores</div>
                        <h2 class="section-title mt-2">Store results</h2>
                    </div>
                    <span class="text-sm font-semibold text-stone-500">{{ $stores->count() }} found</span>
                </div>

                <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    @forelse($stores as $store)
                        <a href="{{ route('stores.show', $store) }}" class="soft-card p-5 transition hover:-translate-y-0.5 hover:border-emerald-300">
                            <div class="grid size-12 place-items-center rounded-2xl bg-emerald-50 font-black text-emerald-900">{{ mb_strtoupper(mb_substr($store->name, 0, 2)) }}</div>
                            <h3 class="mt-4 font-black">{{ $store->name }}</h3>
                            <p class="mt-2 line-clamp-2 text-sm leading-6 text-stone-600">{{ $store->description }}</p>
                        </a>
                    @empty
                        <p class="text-stone-500">No matching stores.</p>
                    @endforelse
                </div>
            </section>

            <section>
                <div class="flex items-end justify-between gap-4">
                    <div>
                        <div class="eyebrow">Coupons</div>
                        <h2 class="section-title mt-2">Coupon & deal results</h2>
                    </div>
                    <span class="text-sm font-semibold text-stone-500">{{ $coupons->count() }} found</span>
                </div>

                <div class="mt-6 grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                    @forelse($coupons as $coupon)
                        <livewire:coupon-card :coupon="$coupon" :key="'search-coupon-'.$coupon->id" />
                    @empty
                        <p class="text-stone-500">No matching coupons.</p>
                    @endforelse
                </div>
            </section>

            <section>
                <div class="flex items-end justify-between gap-4">
                    <div>
                        <div class="eyebrow">Reviews</div>
                        <h2 class="section-title mt-2">Review results</h2>
                    </div>
                    <span class="text-sm font-semibold text-stone-500">{{ $reviews->count() }} found</span>
                </div>

                <div class="mt-6 grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                    @forelse($reviews as $review)
                        <a href="{{ route('reviews.show', $review) }}" class="soft-card p-6 transition hover:-translate-y-0.5 hover:border-emerald-300">
                            <div class="text-xs font-black uppercase tracking-[.18em] text-emerald-800">{{ ucfirst($review->type) }}</div>
                            <h3 class="mt-3 text-xl font-black tracking-tight">{{ $review->title }}</h3>
                            <p class="mt-3 line-clamp-3 text-sm leading-6 text-stone-600">{{ $review->excerpt }}</p>
                        </a>
                    @empty
                        <p class="text-stone-500">No matching reviews.</p>
                    @endforelse
                </div>
            </section>
        </div>
    @endif
</section>
@endsection
