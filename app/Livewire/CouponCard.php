<?php

namespace App\Livewire;

use App\Models\Coupon;
use Livewire\Attributes\Locked;
use Livewire\Component;

class CouponCard extends Component
{
    #[Locked]
    public int $couponId;

    public bool $revealed = false;

    public function mount(Coupon $coupon): void
    {
        $this->couponId = $coupon->getKey();
    }

    public function reveal(): void
    {
        if ($this->revealed) {
            return;
        }

        $coupon = Coupon::query()
            ->whereKey($this->couponId)
            ->where('is_active', true)
            ->firstOrFail();

        if ($coupon->type !== 'code') {
            return;
        }

        $coupon->increment('clicks');
        $this->revealed = true;
    }

    public function render()
    {
        return view('livewire.coupon-card', [
            'coupon' => Coupon::query()
                ->with('store:id,name,slug,logo')
                ->whereKey($this->couponId)
                ->where('is_active', true)
                ->firstOrFail(),
        ]);
    }
}
