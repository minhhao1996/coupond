<?php $__env->startSection('title', 'Delete '.$definition['singular']); ?>
<?php $__env->startSection('content'); ?>
    <div class="max-w-xl rounded-2xl border border-stone-200 bg-white p-8">
        <p class="text-xs font-bold uppercase tracking-widest text-red-700">Confirm deletion</p>
        <h1 class="mt-4 text-2xl font-black">Delete this <?php echo e($definition['singular']); ?>?</h1>
        <p class="mt-4 font-semibold"><?php echo e($item->{$definition['title']}); ?></p>
        <p class="mt-3 text-sm leading-6 text-stone-500">This permanently removes this entry. To keep the content and hide it from the website, edit it and uncheck Published instead.</p>
        <form method="POST" action="<?php echo e(route('admin.destroy', [$resource, $item->id])); ?>" class="mt-7 flex flex-wrap gap-3"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?><button class="rounded-xl bg-red-700 px-5 py-3 text-sm font-bold text-white hover:bg-red-600">Delete permanently</button><a class="admin-secondary" href="<?php echo e(route('admin.edit', [$resource, $item->id])); ?>">Keep this item</a></form>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/ServBay/www/couponhub/resources/views/admin/delete.blade.php ENDPATH**/ ?>