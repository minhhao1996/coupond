@extends('layouts.admin')
@section('title', 'Traffic & copies')
@section('content')
    <div><p class="eyebrow">Audience activity</p><h1 class="mt-3 text-3xl font-black tracking-tight">Traffic & copies</h1><p class="mt-2 text-stone-500">Explore page visits, article views, and successful coupon copies.</p></div>
    <div class="mt-5 rounded-xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm text-emerald-900">
        @if(config('analytics.enabled'))
            <p>Activity from signed-in administrators is excluded. To test tracking, open the public website in a private/incognito window without signing in, view an article, and copy a coupon code. Copies count only when the clipboard action succeeds. Then refresh this page.</p>
        @else
            <p>Analytics tracking is currently disabled. New visits and coupon copies will not appear until tracking is enabled.</p>
        @endif
    </div>

    <form method="GET" action="{{ route('admin.analytics') }}" class="mt-8 flex flex-wrap items-end gap-3 rounded-2xl border border-stone-200 bg-white p-5">
        <div><label for="from" class="admin-label">From</label><input type="date" id="from" name="from" value="{{ $from }}" class="admin-input"></div>
        <div><label for="to" class="admin-label">To</label><input type="date" id="to" name="to" value="{{ $to }}" class="admin-input"></div>
        <div><label for="event" class="admin-label">Activity type</label><select id="event" name="event" class="admin-input"><option value="">All activity</option>@foreach(['page_view' => 'Page view', 'review_view' => 'Article view', 'coupon_copy' => 'Coupon copy'] as $value => $label)<option value="{{ $value }}" @selected(request('event') === $value)>{{ $label }}</option>@endforeach</select></div>
        <div><label for="ip" class="admin-label">IP address</label><input id="ip" name="ip" value="{{ request('ip') }}" class="admin-input" placeholder="All IP addresses"></div>
        <div><label for="store_id" class="admin-label">Store</label><select id="store_id" name="store_id" class="admin-input"><option value="">All stores</option>@foreach($stores as $store)<option value="{{ $store->id }}" @selected((string) request('store_id') === (string) $store->id)>{{ $store->name }}</option>@endforeach</select></div>
        <div><label for="country" class="admin-label">Country</label><select id="country" name="country" class="admin-input"><option value="">All countries</option><option value="unknown" @selected(request('country') === 'unknown')>Unknown</option>@foreach($countries as $country)<option value="{{ $country->country_code }}" @selected(request('country') === $country->country_code)>{{ $country->country_name ?: $country->country_code }} ({{ $country->country_code }})</option>@endforeach</select></div>
        <button class="admin-primary">Filter</button><a href="{{ route('admin.analytics') }}" class="admin-secondary">Reset</a>
    </form>

    <p class="mt-5 text-sm text-stone-500">Totals and rankings follow the date, store, and country filters. Activity type and IP filters apply to the activity log.</p>
    <div class="mt-4 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach($stats as $label => $total)
            <div class="rounded-2xl border border-stone-200 bg-white p-6"><p class="text-sm font-bold text-stone-500">{{ $label }}</p><p class="mt-5 text-4xl font-black tracking-tight">{{ number_format($total) }}</p></div>
        @endforeach
    </div>

    <div class="mt-7 grid gap-6 lg:grid-cols-2">
        @foreach(['Top articles' => $topReviews, 'Top copied coupons' => $topCoupons] as $heading => $items)
            <section class="overflow-hidden rounded-2xl border border-stone-200 bg-white">
                <h2 class="border-b border-stone-100 p-6 text-lg font-black">{{ $heading }}</h2>
                <ol class="divide-y divide-stone-100">
                    @forelse($items as $item)
                        <li class="flex items-center justify-between gap-4 px-6 py-4"><span class="min-w-0 break-words text-sm font-semibold">{{ $item->title ?: 'Untitled' }}</span><span class="shrink-0 text-sm font-bold text-emerald-800">{{ number_format($item->total) }} {{ $heading === 'Top articles' ? 'views' : 'copies' }}</span></li>
                    @empty
                        <li class="p-6 text-sm text-stone-500">No activity in this date range.</li>
                    @endforelse
                </ol>
            </section>
        @endforeach
    </div>

    <p class="mt-5 text-sm text-stone-500">Country is estimated from the visitor’s IP. Local/private IP addresses and failed lookups appear as Unknown. <a href="https://db-ip.com" target="_blank" rel="noopener noreferrer" class="text-emerald-800 hover:underline">IP Geolocation by DB-IP</a>.</p>

    <section class="mt-7 overflow-hidden rounded-2xl border border-stone-200 bg-white">
        <h2 class="border-b border-stone-100 p-6 text-lg font-black">Activity log</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-stone-100 bg-stone-50 text-xs uppercase tracking-wider text-stone-500"><tr><th class="px-6 py-4">Time</th><th class="px-6 py-4">Activity</th><th class="px-6 py-4">Page or coupon</th><th class="px-6 py-4">IP address</th><th class="px-6 py-4">Store</th><th class="px-6 py-4">Country</th></tr></thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($logs as $log)
                        <tr><td class="whitespace-nowrap px-6 py-5 text-stone-500">{{ $log->created_at->format('M j, Y H:i:s') }}</td><td class="whitespace-nowrap px-6 py-5">{{ ['page_view' => 'Page view', 'review_view' => 'Article view', 'coupon_copy' => 'Coupon copy'][$log->event] ?? $log->event }}</td><td class="px-6 py-5"><p class="font-bold">{{ $log->title }}</p><p class="mt-1 break-all text-xs text-stone-500">{{ $log->path }}</p></td><td class="whitespace-nowrap px-6 py-5 text-stone-500">{{ $log->ip ?? 'Unavailable' }}</td><td class="px-6 py-5">{{ $log->store?->name ?? '—' }}</td><td class="whitespace-nowrap px-6 py-5 text-stone-500">{{ $log->country_name ?? $log->country_code ?? 'Unknown' }}</td></tr>
                    @empty
                        <tr><td colspan="6" class="p-12 text-center text-stone-500">No activity found. Try adjusting your filters.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-stone-100 p-5">{{ $logs->links() }}</div>
    </section>
@endsection
