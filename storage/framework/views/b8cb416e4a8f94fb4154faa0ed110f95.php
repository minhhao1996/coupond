<header class="sticky top-0 z-40 border-b border-stone-200/80 bg-stone-50/90 backdrop-blur-xl">
    <div class="h-1 bg-gradient-to-r from-emerald-800 via-emerald-400 to-orange-400"></div>
    <div class="site-header-row mx-auto flex max-w-7xl items-center gap-5 px-5 py-4 lg:px-8">
        <a href="<?php echo e(route('home')); ?>" aria-label="VibeTechCoupons home" class="group flex shrink-0 items-center gap-2.5">
            <?php if (isset($component)) { $__componentOriginal8741a05e11b0c77d19ec61b6b35b26b3 = $component; } ?>
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
<?php endif; ?>
        </a>

        <nav class="hidden items-center rounded-2xl bg-stone-200/60 p-1 lg:flex">
            <a href="<?php echo e(route('home')); ?>#coupons" class="nav-pill">Coupons</a>
            <a href="<?php echo e(route('stores.index')); ?>" class="nav-pill <?php echo e(request()->routeIs('stores.*') ? 'nav-pill-active' : ''); ?>">Stores</a>
            <a href="<?php echo e(route('categories.index')); ?>" class="nav-pill <?php echo e(request()->routeIs('categories.*') ? 'nav-pill-active' : ''); ?>">Categories</a>
            <a href="<?php echo e(route('reviews.index')); ?>" class="nav-pill <?php echo e(request()->routeIs('reviews.*') ? 'nav-pill-active' : ''); ?>">Reviews</a>
        </nav>

        <div class="site-header-search ml-auto w-full max-w-md">
            <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('global-search', []);

$__keyOuter = $__key ?? null;

$__key = null;
$__componentSlots = [];

$__key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-22633533-0', $__key);

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
        </div>
        <a href="<?php echo e(auth()->user()?->is_admin ? route('admin.dashboard') : route('login')); ?>" class="site-header-login shrink-0 text-sm font-bold text-emerald-900 hover:text-emerald-700"><?php echo e(auth()->user()?->is_admin ? 'Manage' : 'Login'); ?></a>
    </div>
</header>
<?php /**PATH /Applications/ServBay/www/couponhub/resources/views/components/header.blade.php ENDPATH**/ ?>