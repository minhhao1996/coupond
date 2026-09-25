<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title>Sign in · VibeTechCoupons Admin</title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <link rel="stylesheet" href="<?php echo e(asset('brand.css')); ?>?v=1">
</head>
<body class="bg-stone-100 text-stone-950 antialiased">
    <main class="grid min-h-screen lg:grid-cols-2">
        <section class="relative hidden flex-col justify-between overflow-hidden bg-emerald-950 p-14 text-white lg:flex">
            <a href="<?php echo e(route('home')); ?>" aria-label="VibeTechCoupons home"><?php if (isset($component)) { $__componentOriginal8741a05e11b0c77d19ec61b6b35b26b3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8741a05e11b0c77d19ec61b6b35b26b3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.brand-logo','data' => ['panel' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('brand-logo'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['panel' => true]); ?>
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
            <div class="relative z-10 max-w-lg">
                <span class="text-xs font-bold uppercase tracking-[.25em] text-emerald-300">Behind every great deal</span>
                <h1 class="mt-6 text-6xl font-black leading-[1.08] tracking-tight">Good offers.<br>Great experiences.</h1>
                <p class="mt-7 max-w-sm text-lg leading-8 text-emerald-100/70">Your space to curate coupons, grow your store directory, and publish stories that help people shop better.</p>
            </div>
            <p class="text-sm text-emerald-200/60">The VibeTechCoupons management studio</p>
            <div aria-hidden="true" class="pointer-events-none absolute -bottom-40 -right-40 size-[500px] rounded-full border-[70px] border-emerald-900/70"></div>
        </section>
        <section class="flex items-center justify-center px-6 py-12">
            <div class="w-full max-w-md">
                <a href="<?php echo e(route('home')); ?>" class="text-sm font-bold text-emerald-800">← Back to VibeTechCoupons</a>
                <p class="mt-12 text-xs font-black uppercase tracking-[.2em] text-stone-400">Admin access</p>
                <h2 class="mt-3 text-4xl font-black tracking-tight">Welcome back.</h2>
                <p class="mt-3 text-stone-500">Sign in to manage your content.</p>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->any()): ?><div role="alert" class="mt-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800"><?php echo e($errors->first()); ?></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <form action="<?php echo e(route('login', [], false)); ?>" method="POST" class="mt-8 space-y-5">
                    <?php echo csrf_field(); ?>
                    <div><label for="email" class="admin-label">Email address</label><input id="email" name="email" type="email" value="<?php echo e(old('email')); ?>" required autofocus autocomplete="username" class="admin-input" placeholder="you@company.com"></div>
                    <div><label for="password" class="admin-label">Password</label><input id="password" name="password" type="password" required autocomplete="current-password" class="admin-input" placeholder="Enter your password"></div>
                    <label class="flex items-center gap-2 text-sm text-stone-600"><input type="checkbox" name="remember" value="1" class="size-4 accent-emerald-800" <?php if(old('remember')): echo 'checked'; endif; ?>> Keep me signed in</label>
                    <button type="submit" class="admin-primary w-full justify-center py-3.5">Sign in →</button>
                </form>
                <p class="mt-7 text-sm leading-6 text-stone-400">Access is limited to authorized administrators. Contact your site owner if you need access.</p>
            </div>
        </section>
    </main>
</body>
</html>
<?php /**PATH /Applications/ServBay/www/couponhub/resources/views/auth/login.blade.php ENDPATH**/ ?>