<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Review;

class ReviewController extends Controller
{
    public function index()
    {
        $reviews = Review::published()->with(['store', 'category'])->where('is_active', true)->latest('published_at')->paginate(12);
        abort_if($reviews->currentPage() > $reviews->lastPage(), 404);

        return view('pages.reviews', [
            'featured' => Review::published()->with(['store', 'category'])->where('is_active', true)->where('is_featured', true)->latest('published_at')->first(),
            'reviews' => $reviews,
            'categories' => Category::where('is_active', true)->whereHas('reviews')->orderBy('name')->get(),
        ]);
    }

    public function show(Review $review)
    {
        abort_unless($review->is_active && $review->published_at && $review->published_at->lte(now()), 404);
        $review->load(['store', 'category']);

        return view('pages.review-show', [
            'review' => $review,
            'related' => Review::published()->with(['store', 'category'])
                ->where('is_active', true)->whereKeyNot($review->id)
                ->when($review->category_id, fn ($q) => $q->where('category_id', $review->category_id))
                ->latest('published_at')->limit(3)->get(),
        ]);
    }
}
