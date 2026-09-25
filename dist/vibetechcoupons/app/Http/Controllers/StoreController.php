<?php

namespace App\Http\Controllers;

use App\Models\Store;

class StoreController extends Controller
{
    public function index()
    {
        return view('pages.stores', [
            'stores' => Store::withCount(['coupons' => fn ($q) => $q->where('is_active', true)])
                ->where('is_active', true)->orderBy('name')->get()->groupBy(fn ($store) => strtoupper(substr($store->name, 0, 1))),
        ]);
    }

    public function show(Store $store)
    {
        abort_unless($store->is_active, 404);
        $store->load(['categories', 'reviews' => fn ($q) => $q->published()->latest('published_at')->limit(3)]);

        $coupons = $store->coupons()->where('is_active', true)->latest()->paginate(12);
        abort_if($coupons->currentPage() > $coupons->lastPage(), 404);

        return view('pages.store-show', [
            'store' => $store,
            'coupons' => $coupons,
        ]);
    }
}
