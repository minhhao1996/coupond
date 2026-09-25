<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Overview') · VibeTechCoupons Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('brand.css') }}?v=1">
</head>
<body class="bg-stone-100 text-stone-950 antialiased">
    <div class="min-h-screen lg:grid lg:grid-cols-[240px_1fr]">
        <aside class="bg-emerald-950 px-5 py-6 text-white lg:sticky lg:top-0 lg:h-screen">
            <a href="{{ route('admin.dashboard') }}" class="admin-brand" aria-label="VibeTechCoupons admin"><x-brand-logo :panel="true" /></a>
            <p class="mb-6 mt-3 text-[10px] font-bold uppercase tracking-[.22em] text-emerald-300">Management studio</p>
            <nav aria-label="Administration" class="flex flex-wrap gap-1.5 lg:flex-col">
                <a href="{{ route('admin.dashboard') }}" class="admin-nav {{ request()->routeIs('admin.dashboard') ? 'bg-white/15 text-white' : 'text-emerald-100/70' }}">Overview</a>
                <a href="{{ route('admin.analytics') }}" class="admin-nav {{ request()->routeIs('admin.analytics') ? 'bg-white/15 text-white' : 'text-emerald-100/70' }}">Traffic & copies</a>
                @foreach(\App\Support\AdminResources::all() as $key => $navResource)
                    <a href="{{ route('admin.index', $key) }}" class="admin-nav {{ request()->route('resource') === $key ? 'bg-white/15 text-white' : 'text-emerald-100/70' }}">{{ $navResource['label'] }}</a>
                @endforeach
                <a href="{{ route('admin.account') }}" class="admin-nav {{ request()->routeIs('admin.account') ? 'bg-white/15 text-white' : 'text-emerald-100/70' }}">Account & security</a>
            </nav>
            <div class="mt-7 border-t border-white/10 pt-5 lg:absolute lg:inset-x-5 lg:bottom-6">
                <a href="{{ route('home') }}" class="text-sm font-semibold text-emerald-200 hover:text-white">← View website</a>
            </div>
        </aside>
        <div class="min-w-0">
            <header class="flex items-center justify-between gap-3 border-b border-stone-200 bg-white px-6 py-4 lg:px-10">
                <span class="text-sm font-semibold text-stone-500">Workspace <span class="mx-2 text-stone-300">/</span> @yield('title', 'Overview')</span>
                <div class="flex items-center gap-4">
                    <span class="hidden text-sm font-bold sm:inline">{{ auth()->user()->name }}</span>
                    <form action="{{ route('logout') }}" method="POST">@csrf<button class="text-sm font-semibold text-stone-500 hover:text-emerald-800">Sign out</button></form>
                </div>
            </header>
            <main class="mx-auto max-w-7xl p-5 sm:p-8 lg:p-10">
                @if(session('status'))<div role="status" class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-900">{{ session('status') }}</div>@endif
                @if($errors->any())
                    <div role="alert" class="mb-6 rounded-xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-800">
                        <p class="font-bold">Please check the following:</p>
                        <ul class="mt-2 list-inside list-disc">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                    </div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
