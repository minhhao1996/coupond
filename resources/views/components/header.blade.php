<header class="sticky top-0 z-40 border-b border-stone-200/80 bg-stone-50/90 backdrop-blur-xl">
    <div class="h-1 bg-gradient-to-r from-emerald-800 via-emerald-400 to-orange-400"></div>
    <div class="site-header-row mx-auto flex max-w-7xl items-center gap-5 px-5 py-4 lg:px-8">
        <a href="{{ route('home') }}" aria-label="VibeTechCoupons home" class="group flex shrink-0 items-center gap-2.5">
            <x-brand-logo />
        </a>

        <nav class="hidden items-center rounded-2xl bg-stone-200/60 p-1 lg:flex">
            <a href="{{ route('home') }}#coupons" class="nav-pill">Coupons</a>
            <a href="{{ route('stores.index') }}" class="nav-pill {{ request()->routeIs('stores.*') ? 'nav-pill-active' : '' }}">Stores</a>
            <a href="{{ route('categories.index') }}" class="nav-pill {{ request()->routeIs('categories.*') ? 'nav-pill-active' : '' }}">Categories</a>
            <a href="{{ route('reviews.index') }}" class="nav-pill {{ request()->routeIs('reviews.*') ? 'nav-pill-active' : '' }}">Reviews</a>
        </nav>

        <div class="site-header-search ml-auto w-full max-w-md">
            <livewire:global-search />
        </div>
        <a href="{{ auth()->user()?->is_admin ? route('admin.dashboard') : route('login') }}" class="site-header-login shrink-0 text-sm font-bold text-emerald-900 hover:text-emerald-700">{{ auth()->user()?->is_admin ? 'Manage' : 'Login' }}</a>
    </div>
</header>
