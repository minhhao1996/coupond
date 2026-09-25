@extends('layouts.app')

@section('content')
@if($latestReviews->isNotEmpty())
<section class="mx-auto max-w-7xl px-5 pt-8 pb-8 lg:px-8">
    <div class="rounded-[2rem] bg-emerald-950 p-7 text-white md:p-10">
        <div class="grid gap-8 lg:grid-cols-[.9fr_1.1fr] lg:items-end">
            <div><div class="text-xs font-black uppercase tracking-[.2em] text-emerald-300">Editorial</div><h2 class="mt-2 text-3xl font-black tracking-tight md:text-4xl">Reviews worth reading before you buy</h2><p class="mt-4 max-w-xl text-sm leading-7 text-emerald-100/75">Long-form product reviews and guides designed to complement the deal pages, not just repeat coupon listings.</p></div>
            <div class="grid gap-4 sm:grid-cols-2">
                @foreach($latestReviews as $review)
                    <a href="{{ route('reviews.show', $review) }}" class="rounded-2xl bg-white/8 p-4 ring-1 ring-white/10 transition hover:bg-white/12">
                        @if($review->image_url)<img src="{{ $review->image_url }}" @if($review->image_srcset)srcset="{{ $review->image_srcset }}" sizes="(min-width: 1024px) 400px, (min-width: 640px) 50vw, 100vw" @endif alt="{{ $review->title }}" loading="eager" @if($loop->first)fetchpriority="high" @endif decoding="async" width="640" height="360" class="mb-4 aspect-video w-full rounded-xl object-cover">@endif<div class="text-[10px] font-black uppercase tracking-[.18em] text-emerald-300">{{ $review->category?->name ?? 'Review' }}</div>
                        <div class="mt-2 font-black leading-snug">{{ $review->title }}</div>
                        <div class="mt-3 text-xs text-emerald-100/60">{{ optional($review->published_at)->format('M j, Y') }}</div>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif
<section class="mx-auto max-w-7xl px-5 pt-8 lg:px-8">
    <div class="relative overflow-hidden rounded-[2rem] border border-emerald-950/5 bg-[#eef2ea] px-6 py-14 md:px-10 md:py-20 lg:px-14">
        <div class="absolute -right-12 -top-20 size-80 rounded-full bg-emerald-200/45 blur-2xl"></div>
        <div class="absolute bottom-0 right-24 size-36 rounded-full bg-orange-200/30 blur-3xl"></div>
        <div class="relative max-w-3xl">
            <div class="eyebrow">VIBETECHCOUPONS</div>
            <h1 class="mt-3 text-4xl font-black leading-[1.02] tracking-[-0.04em] text-emerald-950 md:text-6xl">Find better coupons, deals & shopping guides</h1>
            <p class="mt-5 max-w-2xl text-base leading-7 text-stone-600 md:text-lg">Verified offers, useful store pages and practical reviews — designed to help shoppers save time before they spend.</p>
            <div class="mt-8 flex flex-wrap gap-3">
                <a href="#coupons" class="rounded-xl bg-emerald-900 px-5 py-3 text-sm font-black text-white shadow-sm hover:bg-emerald-800">Browse coupons</a>
                <a href="{{ route('stores.index') }}" class="rounded-xl border border-stone-300 bg-white/70 px-5 py-3 text-sm font-black hover:bg-white">Explore stores</a>
                <a href="{{ route('reviews.index') }}" class="rounded-xl border border-stone-300 bg-white/70 px-5 py-3 text-sm font-black hover:bg-white">Read reviews</a>
            </div>
        </div>
    </div>
</section>

<section id="coupons" class="mx-auto max-w-7xl px-5 py-16 lg:px-8">
    <div class="flex items-end justify-between gap-6">
        <div><div class="eyebrow">Fresh savings</div><h2 class="section-title mt-2">Latest coupons & deals</h2><p class="mt-2 text-sm text-stone-600">Recently added offers from stores in our directory.</p></div>
        <a href="{{ route('stores.index') }}" class="hidden text-sm font-black text-emerald-900 md:inline">Browse all stores →</a>
    </div>
    <div class="mt-7 grid gap-5 md:grid-cols-2 xl:grid-cols-3">
        @foreach($featuredCoupons as $coupon)
            <livewire:coupon-card :coupon="$coupon" :key="'coupon-card-'.$coupon->id" />
        @endforeach
    </div>
</section>

<section class="border-y border-stone-200 bg-white/70">
    <div class="mx-auto max-w-7xl px-5 py-16 lg:px-8">
        <div class="flex items-end justify-between"><div><div class="eyebrow">Popular brands</div><h2 class="section-title mt-2">Stores shoppers are browsing</h2></div><a href="{{ route('stores.index') }}" class="text-sm font-black text-emerald-900">View all →</a></div>
        <div class="mt-7 grid grid-cols-2 gap-4 md:grid-cols-4 lg:grid-cols-8">
            @foreach($featuredStores as $store)
                <a href="{{ route('stores.show', $store) }}" class="group soft-card flex min-h-28 flex-col items-center justify-center p-4 text-center transition hover:-translate-y-1 hover:border-emerald-300">
                    <div class="grid size-12 place-items-center rounded-2xl bg-stone-100 text-lg font-black text-emerald-900">@if($store->logo_url)<img src="{{ $store->logo_url }}" alt="{{ $store->name }}" loading="lazy" width="96" height="96" class="size-full rounded-[inherit] object-contain">@else{{ strtoupper(substr($store->name,0,2)) }}@endif</div>
                    <div class="mt-3 text-sm font-black group-hover:text-emerald-900">{{ $store->name }}</div>
                </a>
            @endforeach
        </div>
    </div>
</section>

<section class="mx-auto max-w-7xl px-5 py-16 lg:px-8">
    <div><div class="eyebrow">Browse smarter</div><h2 class="section-title mt-2">Shop by category</h2></div>
    <div class="mt-7 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @foreach($categories as $category)
            <a href="{{ route('categories.show', $category) }}" class="group overflow-hidden rounded-3xl border border-stone-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                <div class="h-20 bg-gradient-to-br from-emerald-100 via-stone-50 to-orange-50 p-5"><div class="grid size-10 place-items-center rounded-xl bg-white font-black text-emerald-800 shadow-sm">{{ strtoupper(substr($category->name,0,1)) }}</div></div>
                <div class="p-5"><h3 class="font-black tracking-tight">{{ $category->name }}</h3><p class="mt-2 line-clamp-2 text-sm leading-6 text-stone-600">{{ $category->description }}</p><div class="mt-4 text-xs font-black uppercase tracking-wide text-emerald-800">Explore category →</div></div>
            </a>
        @endforeach
    </div>
</section>


@endsection
