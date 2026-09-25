<?php

namespace App\Support;

use App\Models\Category;
use App\Models\Coupon;
use App\Models\Review;
use App\Models\Store;

class AdminResources
{
    public static function all(): array
    {
        $identity = ['name' => 'Name', 'slug' => 'URL slug'];
        $seo = ['seo_title' => 'SEO title', 'seo_description' => 'SEO description|textarea'];
        $flags = ['is_active' => 'Published|checkbox', 'is_featured' => 'Featured|checkbox'];

        return [
            'coupons' => [
                'model' => Coupon::class, 'label' => 'Coupons', 'singular' => 'coupon', 'title' => 'title',
                'fields' => [
                    'title' => 'Title', 'slug' => 'URL slug', 'store_id' => 'Store|store',
                    'category_id' => 'Category|category', 'type' => 'Offer type|offer_type',
                    'code' => 'Coupon code', 'discount_label' => 'Discount label',
                    'destination_url' => 'Offer link|url', 'description' => 'Description|textarea',
                    'starts_at' => 'Starts at|datetime-local', 'expires_at' => 'Expires at|datetime-local',
                    'is_verified' => 'Verified|checkbox', ...$flags,
                ],
            ],
            'stores' => [
                'model' => Store::class, 'label' => 'Stores', 'singular' => 'store', 'title' => 'name',
                'fields' => [
                    ...$identity, 'website_url' => 'Website|url', 'logo' => 'Store image|image',
                    'description' => 'Description|textarea', 'category_ids' => 'Categories|categories',
                    'sort_order' => 'Sort order|number', ...$seo, ...$flags,
                ],
            ],
            'categories' => [
                'model' => Category::class, 'label' => 'Categories', 'singular' => 'category', 'title' => 'name',
                'fields' => [
                    ...$identity, 'description' => 'Description|textarea', 'icon' => 'Icon',
                    'accent' => 'Accent color', 'sort_order' => 'Sort order|number',
                    ...$seo, 'is_active' => 'Published|checkbox',
                ],
            ],
            'reviews' => [
                'model' => Review::class, 'label' => 'Reviews & guides', 'singular' => 'article', 'title' => 'title',
                'fields' => [
                    'title' => 'Title', 'slug' => 'URL slug', 'type' => 'Article type|article_type',
                    'store_id' => 'Store|store', 'category_id' => 'Category|category',
                    'excerpt' => 'Excerpt|textarea', 'content' => 'Content|textarea',
                    'image' => 'Cover image|image', 'rating' => 'Rating (0–5)|number',
                    'published_at' => 'Publication date|datetime-local', ...$seo, ...$flags,
                ],
            ],
        ];
    }

    public static function get(string $resource): array
    {
        return self::all()[$resource] ?? abort(404);
    }
}
