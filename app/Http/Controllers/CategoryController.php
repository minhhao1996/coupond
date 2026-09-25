<?php

namespace App\Http\Controllers;

use App\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        return view('pages.categories', [
            'categories' => Category::withCount(['stores', 'coupons'])->where('is_active', true)->orderBy('sort_order')->get(),
        ]);
    }

    public function show(Category $category)
    {
        abort_unless($category->is_active, 404);

        $coupons = $category->coupons()->with('store')->where('is_active', true)->latest()->paginate(12);
        abort_if($coupons->currentPage() > $coupons->lastPage(), 404);

        return view('pages.category-show', [
            'category' => $category,
            'stores' => $category->stores()->where('is_active', true)->limit(8)->get(),
            'coupons' => $coupons,
            'reviews' => $category->reviews()->published()->with('store')->latest('published_at')->limit(4)->get(),
        ]);
    }
}
