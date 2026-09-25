@extends('layouts.app')
@section('content')
@if(request()->routeIs('admin.reviews.preview'))<div class="mx-auto mt-6 max-w-4xl rounded-xl border border-emerald-200 bg-emerald-50 px-5 py-3 text-sm text-emerald-900">Preview of the saved article · <a class="font-bold underline" href="{{ route('admin.edit', ['reviews', $review->id]) }}">Back to editor</a></div>@endif
<article class="mx-auto max-w-4xl px-5 py-12 lg:px-8">
    <div class="text-center"><div class="eyebrow">{{ strtoupper($review->category?->name ?? $review->type) }}</div><h1 class="mx-auto mt-3 max-w-3xl text-4xl font-black leading-tight tracking-[-0.035em] md:text-6xl">{{ $review->title }}</h1><p class="mx-auto mt-5 max-w-2xl text-lg leading-8 text-stone-600">{{ $review->excerpt }}</p><div class="mt-5 text-sm text-stone-500">{{ $review->published_at ? 'Published '.$review->published_at->format('F j, Y') : 'Unpublished draft' }}@if($review->store) · {{ $review->store->name }}@endif</div></div>
    @if($review->image_url)
        <img src="{{ $review->image_url }}" @if($review->image_srcset)srcset="{{ $review->image_srcset }}" sizes="(min-width: 1280px) 1200px, 100vw" @endif alt="{{ $review->title }}" width="1200" height="600" fetchpriority="high" decoding="async" class="mt-10 aspect-[16/8] w-full rounded-[2rem] object-cover">

    @endif
    <div class="review-content prose prose-stone mx-auto mt-10 max-w-3xl text-base leading-8 text-stone-700">{!! \App\Support\ReviewContent::render($review->content, $review->content_format) !!}</div>
</article>
@if($related->isNotEmpty())<section class="mx-auto max-w-7xl px-5 pb-12 lg:px-8"><h2 class="section-title">Related reading</h2><div class="mt-6 grid gap-5 md:grid-cols-3">@foreach($related as $item)<a href="{{ route('reviews.show',$item) }}" class="soft-card p-5">@if($item->image_url)<img src="{{ $item->image_url }}" @if($item->image_srcset)srcset="{{ $item->image_srcset }}" sizes="(min-width: 1024px) 400px, (min-width: 640px) 50vw, 100vw" @endif alt="{{ $item->title }}" loading="lazy" width="640" height="360" class="mb-4 aspect-video w-full rounded-xl object-cover">@endif<div class="text-xs font-black uppercase tracking-wide text-emerald-700">{{ ucfirst($item->type) }}</div><h3 class="mt-2 text-xl font-black">{{ $item->title }}</h3><p class="mt-2 line-clamp-3 text-sm leading-6 text-stone-600">{{ $item->excerpt }}</p></a>@endforeach</div></section>@endif
@endsection
