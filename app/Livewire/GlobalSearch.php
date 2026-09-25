<?php

namespace App\Livewire;

use App\Models\Coupon;
use App\Models\Review;
use App\Models\Store;
use Illuminate\Support\Collection;
use Livewire\Component;

class GlobalSearch extends Component
{
    public string $query = '';

    public function clearSearch(): void
    {
        $this->query = '';
    }

    public function render()
    {
        $query = trim($this->query);

        if (mb_strlen($query) < 2) {
            return view('livewire.global-search', [
                'stores' => collect(),
                'coupons' => collect(),
                'reviews' => collect(),
            ]);
        }

        // Escape LIKE wildcard characters so a literal "%" or "_" doesn't
        // unexpectedly turn into a broad search.
        $like = '%' . addcslashes($query, '%_\\') . '%';

        return view('livewire.global-search', [
            'stores' => Store::query()
                ->where('is_active', true)
                ->where('name', 'like', $like)
                ->orderBy('name')
                ->limit(5)
                ->get(),
            'coupons' => Coupon::query()
                ->with('store:id,name,slug,logo')
                ->where('is_active', true)
                ->where('title', 'like', $like)
                ->latest()
                ->limit(5)
                ->get(),
            'reviews' => Review::published()
                ->where('is_active', true)
                ->where('title', 'like', $like)
                ->latest('published_at')
                ->limit(4)
                ->get(),
        ]);
    }
}
