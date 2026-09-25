@extends('layouts.admin')
@section('title', 'Overview')
@section('content')
    <div class="flex flex-wrap items-end justify-between gap-5">
        <div><p class="eyebrow">Your content, at a glance</p><h1 class="mt-3 text-3xl font-black tracking-tight sm:text-4xl">Welcome, {{ auth()->user()->name }}.</h1><p class="mt-3 text-stone-500">Keep your offers fresh and your readers inspired.</p></div>
        <a href="{{ route('admin.create', 'coupons') }}" class="admin-primary">+ Add coupon</a>
    </div>
    <div class="mt-8 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach($stats as $key => $stat)
            <a href="{{ route('admin.index', $key) }}" class="rounded-2xl border border-stone-200 bg-white p-6 transition hover:border-emerald-400 hover:shadow-sm">
                <div class="flex justify-between text-sm font-bold text-stone-500"><span>{{ $stat['label'] }}</span><span class="text-emerald-700">↗</span></div>
                <div class="mt-5 text-4xl font-black tracking-tight">{{ number_format($stat['total']) }}</div>
                <div class="mt-3 text-xs font-semibold text-emerald-700">{{ number_format($stat['published']) }} published <span class="text-stone-400">· {{ number_format($stat['total'] - $stat['published']) }} hidden</span></div>
            </a>
        @endforeach
    </div>
    <div class="mt-7 grid gap-6 xl:grid-cols-[1fr_300px]">
        <section class="overflow-hidden rounded-2xl border border-stone-200 bg-white">
            <div class="flex items-center justify-between border-b border-stone-100 p-6"><h2 class="text-lg font-black">Recently updated offers</h2><a href="{{ route('admin.index', 'coupons') }}" class="text-sm font-bold text-emerald-800">View all →</a></div>
            @forelse($recent as $coupon)
                <a href="{{ route('admin.edit', ['coupons', $coupon->id]) }}" class="flex items-center justify-between gap-4 border-b border-stone-100 px-6 py-5 last:border-0 hover:bg-stone-50">
                    <div class="min-w-0"><p class="truncate text-sm font-bold">{{ $coupon->title }}</p><p class="mt-1 text-xs text-stone-500">{{ $coupon->store->name }} · {{ $coupon->updated_at->diffForHumans() }}</p></div>
                    <span class="shrink-0 rounded-full px-2.5 py-1 text-xs font-semibold {{ $coupon->is_active ? 'bg-emerald-50 text-emerald-800' : 'bg-stone-100 text-stone-500' }}">{{ $coupon->is_active ? 'Published' : 'Hidden' }}</span>
                </a>
            @empty<p class="p-8 text-sm text-stone-500">No offers yet. Add your first coupon to get started.</p>@endforelse
        </section>
        <aside class="space-y-5">
            <div class="rounded-2xl bg-emerald-950 p-6 text-white"><p class="text-sm font-semibold text-emerald-200">Offer interactions</p><p class="mt-4 text-4xl font-black">{{ number_format($clicks) }}</p><p class="mt-3 text-sm leading-6 text-emerald-100/65">Total code reveals and tracked offer-link clicks.</p></div>
            <div class="rounded-2xl border border-stone-200 bg-white p-6"><h2 class="font-black">Create something new</h2><div class="mt-4 space-y-3">@foreach(['stores' => 'Add a store', 'categories' => 'Add a category', 'reviews' => 'Write an article'] as $key => $label)<a class="block text-sm font-semibold text-emerald-800 hover:underline" href="{{ route('admin.create', $key) }}">{{ $label }} →</a>@endforeach</div></div>
        </aside>
    </div>
@endsection
