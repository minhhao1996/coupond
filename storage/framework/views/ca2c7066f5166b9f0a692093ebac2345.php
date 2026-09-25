<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['review']));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['review']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($review->affiliate_url && filter_var($review->affiliate_url, FILTER_VALIDATE_URL) && in_array(strtolower((string) parse_url($review->affiliate_url, PHP_URL_SCHEME)), ['http', 'https'])): ?>
    <aside class="review-offer" aria-label="Product offer">
        <div class="review-offer-copy">
            <p class="review-offer-title">Ready to try it?</p>
            <p>Check the current price and availability on the seller’s website.</p>
        </div>
        <a class="review-offer-button" href="<?php echo e($review->affiliate_url); ?>" target="_blank" rel="sponsored nofollow noopener noreferrer"><?php echo e($review->affiliate_label ?: 'Buy now'); ?> <span aria-hidden="true">↗</span></a>
        <p class="review-offer-disclosure">We may earn a commission if you buy through this link, at no extra cost to you.</p>
    </aside>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH /Applications/ServBay/www/couponhub/resources/views/components/review-offer.blade.php ENDPATH**/ ?>