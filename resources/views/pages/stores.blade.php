@extends('layouts.app')
@section('content')
<section class="mx-auto max-w-7xl px-5 pt-10 lg:px-8">
    <div class="rounded-[2rem] bg-[#eef2ea] p-8 md:p-10"><div class="eyebrow">DIRECTORY</div><h1 class="mt-2 text-4xl font-black tracking-tight">Browse by store</h1><p class="mt-3 max-w-2xl text-stone-600">Explore brands and the current coupon offers available in our directory.</p></div>
</section>
<section class="mx-auto max-w-7xl px-5 py-12 lg:px-8">
    <div class="mb-8 flex flex-wrap gap-2">
        @foreach($stores->keys() as $letter)<a href="#letter-{{ $letter }}" class="grid size-9 place-items-center rounded-xl border border-stone-200 bg-white text-xs font-black hover:border-emerald-700 hover:text-emerald-800">{{ $letter }}</a>@endforeach
    </div>
    <div class="space-y-12">
        @foreach($stores as $letter => $items)
            <section id="letter-{{ $letter }}"><div class="mb-4 flex items-center gap-4"><div class="text-3xl font-black text-emerald-900">{{ $letter }}</div><div class="h-px flex-1 bg-stone-200"></div></div>
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    @foreach($items as $store)
                        <a href="{{ route('stores.show', $store) }}" class="soft-card group p-5 transition hover:-translate-y-1 hover:border-emerald-300">
                            <div class="flex items-center gap-3"><div class="grid size-12 place-items-center rounded-2xl bg-stone-100 font-black text-emerald-900">@if($store->logo_url)<img src="{{ $store->logo_url }}" alt="{{ $store->name }}" loading="lazy" width="96" height="96" class="size-full rounded-[inherit] object-contain">@else{{ strtoupper(substr($store->name,0,2)) }}@endif</div><div><h2 class="font-black">{{ $store->name }}</h2><div class="mt-1 text-xs text-stone-500">{{ $store->coupons_count }} active offers</div></div></div>
                            <p class="mt-4 line-clamp-3 text-sm leading-6 text-stone-600">{{ $store->description }}</p><div class="mt-4 text-xs font-black uppercase tracking-wide text-emerald-800">View offers →</div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endforeach
    </div>
</section>
@endsection
