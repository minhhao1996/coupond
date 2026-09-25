@extends('layouts.admin')
@section('title', 'Delete '.$definition['singular'])
@section('content')
    <div class="max-w-xl rounded-2xl border border-stone-200 bg-white p-8">
        <p class="text-xs font-bold uppercase tracking-widest text-red-700">Confirm deletion</p>
        <h1 class="mt-4 text-2xl font-black">Delete this {{ $definition['singular'] }}?</h1>
        <p class="mt-4 font-semibold">{{ $item->{$definition['title']} }}</p>
        <p class="mt-3 text-sm leading-6 text-stone-500">This permanently removes this entry. To keep the content and hide it from the website, edit it and uncheck Published instead.</p>
        <form method="POST" action="{{ route('admin.destroy', [$resource, $item->id]) }}" class="mt-7 flex flex-wrap gap-3">@csrf @method('DELETE')<button class="rounded-xl bg-red-700 px-5 py-3 text-sm font-bold text-white hover:bg-red-600">Delete permanently</button><a class="admin-secondary" href="{{ route('admin.edit', [$resource, $item->id]) }}">Keep this item</a></form>
    </div>
@endsection
