<?php $__env->startSection('content'); ?>
<section class="mx-auto max-w-7xl px-5 pt-10 lg:px-8">
    <div class="grid gap-8 rounded-[2rem] bg-[#eef2ea] p-8 md:grid-cols-[auto_1fr] md:items-center md:p-10">
        <div class="grid size-24 place-items-center rounded-3xl bg-white text-3xl font-black text-emerald-900 shadow-sm"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($store->logo_url): ?><img src="<?php echo e($store->logo_url); ?>" alt="<?php echo e($store->name); ?>" loading="lazy" width="96" height="96" class="size-full rounded-[inherit] object-contain"><?php else: ?><?php echo e(strtoupper(substr($store->name,0,2))); ?><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div>
        <div><div class="eyebrow">STORE</div><h1 class="mt-2 text-4xl font-black tracking-tight"><?php echo e($store->name); ?> coupons & deals</h1><p class="mt-3 max-w-3xl text-stone-600"><?php echo e($store->description); ?></p><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($store->website_url): ?><a class="mt-4 inline-block text-sm font-black text-emerald-800" href="<?php echo e($store->website_url); ?>" rel="nofollow sponsored" target="_blank">Visit official website ↗</a><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div>
    </div>
</section>
<section class="mx-auto max-w-7xl px-5 py-12 lg:px-8"><h2 class="section-title">Current offers</h2><div class="mt-6 grid gap-5 md:grid-cols-2 xl:grid-cols-3"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $coupons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $coupon): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('coupon-card', ['coupon' => $coupon]);

$__keyOuter = $__key ?? null;

$__key = 'coupon-card-'.$coupon->id;
$__componentSlots = [];

$__key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-4275392530-0', $__key);

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
?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?></div><div class="mt-8"><?php echo e($coupons->links()); ?></div></section>
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($store->reviews->isNotEmpty()): ?><section class="mx-auto max-w-7xl px-5 pb-10 lg:px-8"><h2 class="section-title">Latest <?php echo e($store->name); ?> reviews</h2><div class="mt-6 grid gap-5 md:grid-cols-3"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $store->reviews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $review): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><a href="<?php echo e(route('reviews.show',$review)); ?>" class="soft-card p-5"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($review->image_url): ?><img src="<?php echo e($review->image_url); ?>" <?php if($review->image_srcset): ?>srcset="<?php echo e($review->image_srcset); ?>" sizes="(min-width: 1024px) 400px, (min-width: 640px) 50vw, 100vw" <?php endif; ?> alt="<?php echo e($review->title); ?>" loading="lazy" width="640" height="360" class="mb-4 aspect-video w-full rounded-xl object-cover"><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?><div class="text-xs font-black uppercase tracking-wide text-emerald-700"><?php echo e(ucfirst($review->type)); ?></div><h3 class="mt-2 text-xl font-black"><?php echo e($review->title); ?></h3><p class="mt-2 line-clamp-3 text-sm leading-6 text-stone-600"><?php echo e($review->excerpt); ?></p></a><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?></div></section><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/ServBay/www/couponhub/resources/views/pages/store-show.blade.php ENDPATH**/ ?>