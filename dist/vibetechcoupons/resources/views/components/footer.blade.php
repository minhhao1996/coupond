<footer class="mt-24 border-t border-stone-200 bg-white">
    <div class="mx-auto grid max-w-7xl gap-10 px-5 py-12 md:grid-cols-[1.4fr_1fr_1fr] lg:px-8">
        <div>
            <a href="{{ route('home') }}" aria-label="VibeTechCoupons home"><x-brand-logo /></a>
            <p class="mt-4 max-w-md text-sm leading-6 text-stone-600">Find coupon codes, discover store offers, and read practical guides to make your next purchase a better one.</p>
        </div>
        <div><h3 class="font-bold">Explore</h3><div class="mt-4 grid gap-2 text-sm text-stone-600"><a href="{{ route('stores.index') }}">Stores</a><a href="{{ route('categories.index') }}">Categories</a><a href="{{ route('reviews.index') }}">Reviews</a></div></div>
        <div><h3 class="font-bold">About</h3><div class="mt-4 grid gap-2 text-sm text-stone-600"><span>Editorial policy</span><span>Coupon verification</span><span>Contact</span></div></div>
    </div>
    <div class="border-t border-stone-100 px-5 py-5 text-center text-xs text-stone-500">© {{ now()->year }} VibeTechCoupons. Helping you shop with confidence.</div>
</footer>
