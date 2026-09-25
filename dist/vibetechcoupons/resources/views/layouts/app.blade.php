<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php($seo = \App\Support\Seo::forPage(get_defined_vars()))
    <title>{{ $seo['title'] }}</title>
    <meta name="description" content="{{ $seo['description'] }}">
    <meta name="robots" content="{{ $seo['robots'] }}">
    <link rel="canonical" href="{{ $seo['canonical'] }}">
    <meta property="og:site_name" content="VibeTechCoupons">
    <meta property="og:title" content="{{ $seo['title'] }}">
    <meta property="og:description" content="{{ $seo['description'] }}">
    <meta property="og:type" content="{{ $seo['article'] ? 'article' : 'website' }}">
    <meta property="og:url" content="{{ $seo['canonical'] }}">
    <meta name="twitter:card" content="{{ $seo['image'] ? 'summary_large_image' : 'summary' }}">
    <meta name="twitter:title" content="{{ $seo['title'] }}">
    <meta name="twitter:description" content="{{ $seo['description'] }}">
    @if($seo['image'])
        <meta property="og:image" content="{{ $seo['image'] }}">
        <meta property="og:image:alt" content="{{ $seo['article']->title }}">
        <meta name="twitter:image" content="{{ $seo['image'] }}">
    @endif
    @if($seo['article'])
        <meta property="article:published_time" content="{{ $seo['article']->published_at?->toAtomString() }}">
        <meta property="article:modified_time" content="{{ $seo['article']->updated_at?->toAtomString() }}">
    @endif
    <script type="application/ld+json">{!! $seo['json'] !!}</script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <link rel="stylesheet" href="{{ asset('brand.css') }}?v=1">
</head>
<body class="bg-stone-50 text-stone-950 antialiased">
    <x-header />
    @if(count($seo['breadcrumbs']) > 1)
        <nav aria-label="Breadcrumb" class="mx-auto max-w-7xl px-5 pt-6 text-sm text-stone-500 lg:px-8"><ol class="flex flex-wrap items-center gap-2">@foreach($seo['breadcrumbs'] as $crumb)<li>@if(!$loop->first)<span aria-hidden="true" class="mr-2">/</span>@endif @if($loop->last)<span aria-current="page">{{ $crumb['name'] }}</span>@else<a class="hover:text-emerald-800" href="{{ $crumb['url'] }}">{{ $crumb['name'] }}</a>@endif</li>@endforeach</ol></nav>
    @endif
    <main>{{ $slot ?? '' }}@yield('content')</main>
    <x-footer />
    @livewireScripts
</body>
</html>
