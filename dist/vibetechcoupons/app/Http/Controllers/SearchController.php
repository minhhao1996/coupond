<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Review;
use App\Models\Store;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __invoke(Request $request)
    {
        $query = trim((string) $request->query('q', ''));
        $like = '%' . addcslashes($query, '%_\\') . '%';
        $enabled = mb_strlen($query) >= 2;

        return view('pages.search', [
            'query' => $query,
            'stores' => $enabled
                ? Store::query()->where('is_active', true)->where('name', 'like', $like)->orderBy('name')->limit(24)->get()
                : collect(),
            'coupons' => $enabled
                ? Coupon::query()->with('store:id,name,slug,logo')->where('is_active', true)->where('title', 'like', $like)->latest()->limit(24)->get()
                : collect(),
            'reviews' => $enabled
                ? Review::published()->with('store:id,name,slug,logo')->where('is_active', true)->where('title', 'like', $like)->latest('published_at')->limit(24)->get()
                : collect(),
            'title' => $query !== '' ? "Search: {$query} — VibeTechCoupons" : 'Search — VibeTechCoupons',
            'description' => 'Search VibeTechCoupons stores, coupons, deals and shopping reviews.',
            'robots' => 'noindex,follow',
        ]);
    }
}
