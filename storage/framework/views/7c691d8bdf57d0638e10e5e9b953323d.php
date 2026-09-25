<footer class="mt-24 border-t border-stone-200 bg-white">
    <div class="mx-auto grid max-w-7xl gap-10 px-5 py-12 md:grid-cols-[1.4fr_1fr_1fr] lg:px-8">
        <div>
            <a href="<?php echo e(route('home')); ?>" aria-label="VibeTechCoupons home"><?php if (isset($component)) { $__componentOriginal8741a05e11b0c77d19ec61b6b35b26b3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8741a05e11b0c77d19ec61b6b35b26b3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.brand-logo','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('brand-logo'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8741a05e11b0c77d19ec61b6b35b26b3)): ?>
<?php $attributes = $__attributesOriginal8741a05e11b0c77d19ec61b6b35b26b3; ?>
<?php unset($__attributesOriginal8741a05e11b0c77d19ec61b6b35b26b3); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8741a05e11b0c77d19ec61b6b35b26b3)): ?>
<?php $component = $__componentOriginal8741a05e11b0c77d19ec61b6b35b26b3; ?>
<?php unset($__componentOriginal8741a05e11b0c77d19ec61b6b35b26b3); ?>
<?php endif; ?></a>
            <p class="mt-4 max-w-md text-sm leading-6 text-stone-600">Find coupon codes, discover store offers, and read practical guides to make your next purchase a better one.</p>
        </div>
        <div><h3 class="font-bold">Explore</h3><div class="mt-4 grid gap-2 text-sm text-stone-600"><a href="<?php echo e(route('stores.index')); ?>">Stores</a><a href="<?php echo e(route('categories.index')); ?>">Categories</a><a href="<?php echo e(route('reviews.index')); ?>">Reviews</a></div></div>
        <div><h3 class="font-bold">About</h3><div class="mt-4 grid gap-2 text-sm text-stone-600"><span>Editorial policy</span><span>Coupon verification</span><span>Contact</span></div></div>
    </div>
    <div class="border-t border-stone-100 px-5 py-5 text-center text-xs text-stone-500">© <?php echo e(now()->year); ?> VibeTechCoupons. Helping you shop with confidence.</div>
</footer>
<?php /**PATH /Applications/ServBay/www/couponhub/resources/views/components/footer.blade.php ENDPATH**/ ?>