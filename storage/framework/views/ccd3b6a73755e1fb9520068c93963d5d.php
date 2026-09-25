<article
    <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'coupon-card-'.e($coupon->id).''; ?>wire:key="coupon-card-<?php echo e($coupon->id); ?>"
    x-data="{
        opening: false,
        copyMessage: '',
        async copyCode(code) {
            if (!code) {
                this.copyMessage = 'No code available';
                return;
            }
            try {
                await navigator.clipboard.writeText(code);
                this.copyMessage = 'Copied!';
                fetch(<?php echo \Illuminate\Support\Js::from(route('analytics.copy', $coupon->id))->toHtml() ?>, {
                    method: 'POST', keepalive: true,
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept': 'application/json' }
                }).catch(() => {});
            } catch {
                this.copyMessage = 'Could not copy automatically. Tap the code to retry.';
            }
        },
        async showCode(code, destination) {
            if (this.opening) return;
            this.opening = true;
            this.copyCode(code);
            if (destination) window.open(destination, '_blank', 'noopener,noreferrer');
            try {
                await this.$wire.reveal();
            } finally {
                this.opening = false;
            }
        }
    }"
    class="group relative overflow-hidden rounded-3xl border border-stone-200 bg-white p-5 shadow-[0_8px_30px_rgba(28,25,23,.05)] transition duration-300 hover:-translate-y-1 hover:shadow-[0_18px_45px_rgba(28,25,23,.10)]"
>
    <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r <?php echo e($coupon->type === 'code' ? 'from-emerald-800 to-emerald-400' : 'from-orange-600 to-orange-300'); ?>"></div>

    <div class="flex items-start gap-3">
        <div class="grid size-12 shrink-0 place-items-center rounded-2xl border border-stone-200 bg-stone-50 text-lg font-black text-emerald-900">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($coupon->store->logo_url): ?><img src="<?php echo e($coupon->store->logo_url); ?>" alt="<?php echo e($coupon->store->name); ?>" loading="lazy" width="96" height="96" class="size-full rounded-[inherit] object-contain"><?php else: ?><?php echo e(mb_strtoupper(mb_substr($coupon->store->name, 0, 2))); ?><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <div class="min-w-0 flex-1">
            <div class="flex items-center gap-2">
                <a href="<?php echo e(route('stores.show', $coupon->store)); ?>" class="truncate font-bold hover:text-emerald-800"><?php echo e($coupon->store->name); ?></a>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($coupon->is_verified): ?>
                    <span class="rounded-full bg-emerald-50 px-2 py-1 text-[10px] font-black uppercase tracking-wide text-emerald-800">Verified</span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <div class="mt-1 text-xs font-bold uppercase tracking-[.16em] text-stone-400">
                <?php echo e($coupon->type === 'code' ? 'Coupon code' : 'Online deal'); ?>

            </div>
        </div>
    </div>

    <h3 class="mt-5 text-xl font-black leading-snug tracking-tight text-stone-950"><?php echo e($coupon->title); ?></h3>
    <p class="mt-2 line-clamp-2 text-sm leading-6 text-stone-600"><?php echo e($coupon->description); ?></p>

    <div class="mt-5 flex items-center justify-between gap-3 border-t border-stone-100 pt-4">
        <span class="rounded-full bg-stone-100 px-3 py-1.5 text-xs font-black text-stone-700"><?php echo e($coupon->discount_label ?: 'Limited offer'); ?></span>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($coupon->type === 'deal'): ?>
            <a
                href="<?php echo e(route('coupons.go', $coupon)); ?>"
                target="_blank"
                rel="nofollow sponsored noopener"
                class="rounded-xl bg-orange-600 px-4 py-2.5 text-sm font-black text-white transition hover:bg-orange-500"
            >
                Get deal
            </a>
        <?php elseif($revealed): ?>
            <div class="flex items-center gap-2">
                <button
                    type="button"
                    x-on:click.prevent.stop="copyCode(<?php echo \Illuminate\Support\Js::from($coupon->code)->toHtml() ?>)"
                    title="Copy coupon code"
                    class="rounded-xl border-2 border-dashed border-emerald-700 bg-emerald-50 px-4 py-2.5 font-mono text-sm font-black tracking-wider text-emerald-950 hover:bg-emerald-100"
                >
                    <?php echo e($coupon->code ?: 'NO CODE'); ?>

                </button>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($coupon->destination_url): ?>
                    <a
                        href="<?php echo e(route('coupons.go', $coupon)); ?>"
                        target="_blank"
                        rel="nofollow sponsored noopener"
                        class="rounded-xl bg-emerald-900 px-3 py-2.5 text-sm font-black text-white hover:bg-emerald-800"
                    >
                        Shop
                    </a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        <?php else: ?>
            <button
                type="button"
                x-on:click.prevent.stop="showCode(<?php echo \Illuminate\Support\Js::from($coupon->code)->toHtml() ?>, <?php echo \Illuminate\Support\Js::from(in_array(strtolower((string) parse_url(trim((string) $coupon->destination_url), PHP_URL_SCHEME)), ['http', 'https'], true) ? trim($coupon->destination_url) : null)->toHtml() ?>)"
                x-bind:disabled="opening"
                wire:loading.attr="disabled"
                wire:target="reveal"
                class="rounded-xl bg-emerald-900 px-4 py-2.5 text-sm font-black text-white transition hover:bg-emerald-800 disabled:cursor-wait disabled:opacity-70"
            >
                <span wire:loading.remove wire:target="reveal">Show code</span>
                <span wire:loading wire:target="reveal">Opening…</span>
            </button>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
    <p x-text="copyMessage" role="status" aria-live="polite" class="mt-2 text-right text-xs font-semibold text-emerald-800"></p>
</article>
<?php /**PATH /Applications/ServBay/www/couponhub/resources/views/livewire/coupon-card.blade.php ENDPATH**/ ?>