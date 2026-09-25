<?php $__env->startSection('content'); ?>
<section class="mx-auto max-w-7xl px-5 py-12 lg:px-8">
    <div class="rounded-[2rem] border border-stone-200 bg-gradient-to-br from-stone-100 to-emerald-50/60 p-8 md:p-12">
        <div class="eyebrow">Search</div>
        <h1 class="mt-3 text-4xl font-black tracking-tight text-stone-950 md:text-5xl">Find stores, coupons & reviews</h1>
        <p class="mt-4 max-w-2xl text-stone-600">Search the directory without relying on JavaScript. Live suggestions in the header are an enhancement; this page remains the reliable fallback.</p>

        <form action="<?php echo e(route('search')); ?>" method="GET" class="mt-7 flex max-w-2xl gap-3">
            <input name="q" value="<?php echo e($query); ?>" type="search" minlength="2" placeholder="Try: Wattcycle, camping, 10% off..." class="min-w-0 flex-1 rounded-2xl border border-stone-300 bg-white px-5 py-3 outline-none focus:border-emerald-700 focus:ring-4 focus:ring-emerald-700/10">
            <button class="rounded-2xl bg-emerald-900 px-6 py-3 font-black text-white hover:bg-emerald-800">Search</button>
        </form>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(mb_strlen($query) < 2): ?>
        <div class="mt-10 rounded-2xl border border-stone-200 bg-white p-6 text-stone-600">Enter at least 2 characters to search.</div>
    <?php else: ?>
        <div class="mt-12 space-y-12">
            <section>
                <div class="flex items-end justify-between gap-4">
                    <div>
                        <div class="eyebrow">Stores</div>
                        <h2 class="section-title mt-2">Store results</h2>
                    </div>
                    <span class="text-sm font-semibold text-stone-500"><?php echo e($stores->count()); ?> found</span>
                </div>

                <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $stores; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $store): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <a href="<?php echo e(route('stores.show', $store)); ?>" class="soft-card p-5 transition hover:-translate-y-0.5 hover:border-emerald-300">
                            <div class="grid size-12 place-items-center rounded-2xl bg-emerald-50 font-black text-emerald-900"><?php echo e(mb_strtoupper(mb_substr($store->name, 0, 2))); ?></div>
                            <h3 class="mt-4 font-black"><?php echo e($store->name); ?></h3>
                            <p class="mt-2 line-clamp-2 text-sm leading-6 text-stone-600"><?php echo e($store->description); ?></p>
                        </a>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <p class="text-stone-500">No matching stores.</p>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </section>

            <section>
                <div class="flex items-end justify-between gap-4">
                    <div>
                        <div class="eyebrow">Coupons</div>
                        <h2 class="section-title mt-2">Coupon & deal results</h2>
                    </div>
                    <span class="text-sm font-semibold text-stone-500"><?php echo e($coupons->count()); ?> found</span>
                </div>

                <div class="mt-6 grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $coupons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $coupon): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('coupon-card', ['coupon' => $coupon]);

$__keyOuter = $__key ?? null;

$__key = 'search-coupon-'.$coupon->id;
$__componentSlots = [];

$__key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-1211762122-0', $__key);

$__html = app('livewire')->mount($__name, $__params, $__key, $__componentSlots);

echo $__html;

unset($__html);
unset($__key);
$__key = $__keyOuter;
unset($__keyOuter);
unset($__name);
unset($__params);
unset($__componentSlots);
unset($__split);
?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <p class="text-stone-500">No matching coupons.</p>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </section>

            <section>
                <div class="flex items-end justify-between gap-4">
                    <div>
                        <div class="eyebrow">Reviews</div>
                        <h2 class="section-title mt-2">Review results</h2>
                    </div>
                    <span class="text-sm font-semibold text-stone-500"><?php echo e($reviews->count()); ?> found</span>
                </div>

                <div class="mt-6 grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $reviews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $review): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <a href="<?php echo e(route('reviews.show', $review)); ?>" class="soft-card p-6 transition hover:-translate-y-0.5 hover:border-emerald-300">
                            <div class="text-xs font-black uppercase tracking-[.18em] text-emerald-800"><?php echo e(ucfirst($review->type)); ?></div>
                            <h3 class="mt-3 text-xl font-black tracking-tight"><?php echo e($review->title); ?></h3>
                            <p class="mt-3 line-clamp-3 text-sm leading-6 text-stone-600"><?php echo e($review->excerpt); ?></p>
                        </a>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <p class="text-stone-500">No matching reviews.</p>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </section>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/ServBay/www/couponhub/resources/views/pages/search.blade.php ENDPATH**/ ?>