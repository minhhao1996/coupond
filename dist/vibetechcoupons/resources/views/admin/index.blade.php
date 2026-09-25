@extends('layouts.admin')
@section('title', $definition['label'])
@section('content')
    <div class="flex flex-wrap items-center justify-between gap-4"><div><p class="eyebrow">Content library</p><h1 class="mt-3 text-3xl font-black tracking-tight">{{ $definition['label'] }}</h1><p class="mt-2 text-stone-500">Create, organize, and publish your {{ strtolower($definition['label']) }}.</p></div><a href="{{ route('admin.create', $resource) }}" class="admin-primary">+ Add {{ $definition['singular'] }}</a></div>
    <div class="mt-8 overflow-hidden rounded-2xl border border-stone-200 bg-white">
        <form method="GET" action="{{ route('admin.index', $resource) }}" class="flex flex-wrap items-end gap-3 border-b border-stone-100 p-5">
            <div class="min-w-0 flex-1"><label for="q" class="admin-label">Search {{ strtolower($definition['label']) }}</label><input id="q" name="q" value="{{ request('q') }}" class="admin-input" placeholder="Search by {{ $definition['title'] }}…"></div>
            <div><label for="status" class="admin-label">Status</label><select id="status" name="status" class="admin-input"><option value="">All statuses</option><option value="published" @selected(request('status') === 'published')>Published</option><option value="hidden" @selected(request('status') === 'hidden')>Hidden</option></select></div>
            <button class="admin-secondary">Filter</button>
            @if(request()->filled('q') || request()->filled('status'))<a href="{{ route('admin.index', $resource) }}" class="admin-secondary">Reset</a>@endif
        </form>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-stone-100 bg-stone-50 text-xs uppercase tracking-wider text-stone-500"><tr><th class="px-6 py-4">{{ ucfirst($definition['title']) }}</th><th class="px-6 py-4">Status</th><th class="px-6 py-4">Updated</th><th class="px-6 py-4 text-right">Actions</th></tr></thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($items as $item)
                        <tr class="hover:bg-stone-50/60"><td class="min-w-60 px-6 py-5">@php($thumbnail = $resource === 'stores' ? $item->logo_url : ($resource === 'reviews' ? $item->image_url : null))
                        @if($thumbnail)<img src="{{ $thumbnail }}" alt="{{ $item->{$definition['title']} }}" loading="lazy" width="56" height="56" class="float-left mr-3 size-14 rounded-xl border border-stone-200 object-cover">@endif<a href="{{ route('admin.edit', [$resource, $item->id]) }}" class="font-bold hover:text-emerald-800">{{ $item->{$definition['title']} }}</a><div class="mt-1 text-xs text-stone-400">{{ $item->slug }}</div></td><td class="px-6 py-5"><span class="rounded-full px-2.5 py-1 text-xs font-bold {{ $item->is_active ? 'bg-emerald-50 text-emerald-800' : 'bg-stone-100 text-stone-500' }}">{{ $item->is_active ? 'Published' : 'Hidden' }}</span></td><td class="whitespace-nowrap px-6 py-5 text-stone-500">{{ $item->updated_at->format('M j, Y') }}</td><td class="whitespace-nowrap px-6 py-5 text-right"><a href="{{ route('admin.edit', [$resource, $item->id]) }}" class="font-bold text-emerald-800">Edit</a><a href="{{ route('admin.confirm-delete', [$resource, $item->id]) }}" class="ml-4 text-stone-400 hover:text-red-700">Delete</a></td></tr>
                    @empty<tr><td colspan="4" class="p-12 text-center text-stone-500">No items found. Adjust your filters or add a new {{ $definition['singular'] }}.</td></tr>@endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-stone-100 p-5">{{ $items->links() }}</div>
    </div>
@endsection
