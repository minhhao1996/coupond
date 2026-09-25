<?php $__env->startSection('content'); ?>
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(request()->routeIs('admin.reviews.preview')): ?><div class="mx-auto mt-6 max-w-4xl rounded-xl border border-emerald-200 bg-emerald-50 px-5 py-3 text-sm text-emerald-900">Preview of the saved article · <a class="font-bold underline" href="<?php echo e(route('admin.edit', ['reviews', $review->id])); ?>">Back to editor</a></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<article class="review-article mx-auto max-w-4xl px-5 py-12 lg:px-8">
    <div class="text-center"><div class="eyebrow"><?php echo e(strtoupper($review->category?->name ?? $review->type)); ?></div><h1 class="mx-auto mt-3 max-w-3xl text-4xl font-black leading-tight tracking-[-0.035em] md:text-6xl"><?php echo e($review->title); ?></h1><p class="mx-auto mt-5 max-w-2xl text-lg leading-8 text-stone-600"><?php echo e($review->excerpt); ?></p><div class="mt-5 text-sm text-stone-500"><?php echo e($review->published_at ? 'Published '.$review->published_at->format('F j, Y') : 'Unpublished draft'); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($review->store): ?> · <?php echo e($review->store->name); ?><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div></div>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($review->image_url): ?>
        <img src="<?php echo e($review->image_url); ?>" <?php if($review->image_srcset): ?>srcset="<?php echo e($review->image_srcset); ?>" sizes="(min-width: 1280px) 1200px, 100vw" <?php endif; ?> alt="<?php echo e($review->title); ?>" width="1200" height="600" fetchpriority="high" decoding="async" class="mt-10 aspect-[16/8] w-full rounded-[2rem] object-cover">

    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php if (isset($component)) { $__componentOriginal5a57fc891d4d5516aa513e04d51b8eab = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5a57fc891d4d5516aa513e04d51b8eab = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.review-offer','data' => ['review' => $review]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('review-offer'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['review' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($review)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5a57fc891d4d5516aa513e04d51b8eab)): ?>
<?php $attributes = $__attributesOriginal5a57fc891d4d5516aa513e04d51b8eab; ?>
<?php unset($__attributesOriginal5a57fc891d4d5516aa513e04d51b8eab); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5a57fc891d4d5516aa513e04d51b8eab)): ?>
<?php $component = $__componentOriginal5a57fc891d4d5516aa513e04d51b8eab; ?>
<?php unset($__componentOriginal5a57fc891d4d5516aa513e04d51b8eab); ?>
<?php endif; ?>
    <div class="review-content prose prose-stone mx-auto mt-10 max-w-3xl text-base leading-8 text-stone-700"><?php echo \App\Support\ReviewContent::render($review->content, $review->content_format); ?></div>
    <?php if (isset($component)) { $__componentOriginal5a57fc891d4d5516aa513e04d51b8eab = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5a57fc891d4d5516aa513e04d51b8eab = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.review-offer','data' => ['review' => $review]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('review-offer'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['review' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($review)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5a57fc891d4d5516aa513e04d51b8eab)): ?>
<?php $attributes = $__attributesOriginal5a57fc891d4d5516aa513e04d51b8eab; ?>
<?php unset($__attributesOriginal5a57fc891d4d5516aa513e04d51b8eab); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5a57fc891d4d5516aa513e04d51b8eab)): ?>
<?php $component = $__componentOriginal5a57fc891d4d5516aa513e04d51b8eab; ?>
<?php unset($__componentOriginal5a57fc891d4d5516aa513e04d51b8eab); ?>
<?php endif; ?>
</article>
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($related->isNotEmpty()): ?><section class="mx-auto max-w-7xl px-5 pb-12 lg:px-8"><h2 class="section-title">Related reading</h2><div class="mt-6 grid gap-5 md:grid-cols-3"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $related; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><a href="<?php echo e(route('reviews.show',$item)); ?>" class="soft-card p-5"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->image_url): ?><img src="<?php echo e($item->image_url); ?>" <?php if($item->image_srcset): ?>srcset="<?php echo e($item->image_srcset); ?>" sizes="(min-width: 1024px) 400px, (min-width: 640px) 50vw, 100vw" <?php endif; ?> alt="<?php echo e($item->title); ?>" loading="lazy" width="640" height="360" class="mb-4 aspect-video w-full rounded-xl object-cover"><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?><div class="text-xs font-black uppercase tracking-wide text-emerald-700"><?php echo e(ucfirst($item->type)); ?></div><h3 class="mt-2 text-xl font-black"><?php echo e($item->title); ?></h3><p class="mt-2 line-clamp-3 text-sm leading-6 text-stone-600"><?php echo e($item->excerpt); ?></p></a><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?></div></section><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/ServBay/www/couponhub/resources/views/pages/review-show.blade.php ENDPATH**/ ?>