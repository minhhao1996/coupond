<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Coupon;
use App\Models\Review;
use App\Models\Store;

class HomeController extends Controller
{
    public function __invoke()
    {
        return view('pages.home', [
            'featuredCoupons' => Coupon::with(['store', 'category'])->where('is_active', true)->latest()->limit(6)->get(),
            'featuredStores' => Store::where('is_active', true)->orderByDesc('is_featured')->orderBy('sort_order')->limit(8)->get(),
            'categories' => Category::where('is_active', true)->orderBy('sort_order')->limit(8)->get(),
            'latestReviews' => Review::published()->with(['store', 'category'])->where('is_active', true)->whereNotNull('published_at')->latest('published_at')->limit(4)->get(),
        ]);
    }
}
