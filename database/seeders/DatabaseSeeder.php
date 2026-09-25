<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Coupon;
use App\Models\Review;
use App\Models\Store;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(AdminSeeder::class);

        $categoryNames = [
            'Arts & Crafts','Beauty & Personal Care','Business & Marketing Software','Electric Bikes & Transportation',
            'Fashion & Accessories','Health & Wellness','Home Fragrance','Outdoor & Camping','Tools & Home Improvement','Electronics'
        ];

        $categories = collect($categoryNames)->mapWithKeys(function ($name, $i) {
            $category = Category::create([
                'name'=>$name,
                'slug'=>Str::slug($name),
                'description'=>"Explore trusted stores, current offers and practical shopping advice for {$name}.",
                'sort_order'=>$i + 1,
            ]);
            return [$category->slug => $category];
        });

        $storeData = [
            ['3F UL Gear','Outdoor & Camping'], ['7Artisans','Electronics'], ['ARCCAPTAIN','Tools & Home Improvement'], ['Aroma360','Home Fragrance'],
            ['ATK','Electronics'], ['Auri Nutrition','Health & Wellness'], ['Bodegacooler','Outdoor & Camping'], ['Boderry','Fashion & Accessories'],
            ['CANNI Official','Beauty & Personal Care'], ['Carpuride','Electronics'], ['Comfier','Health & Wellness'], ['Cutebee','Arts & Crafts'],
            ['Wattcycle','Electric Bikes & Transportation'], ['Meraki Medicinal','Health & Wellness'], ['Peak Design','Outdoor & Camping'], ['Lumina Home','Home Fragrance']
        ];

        $stores = collect();
        foreach ($storeData as $i => [$name, $categoryName]) {
            $store = Store::create([
                'name'=>$name, 'slug'=>Str::slug($name),
                'description'=>"Find verified {$name} coupons, promo codes, product offers and shopping guidance in one clean place.",
                'website_url'=>'https://example.com', 'is_featured'=>$i < 8, 'sort_order'=>$i + 1,
            ]);
            $category = $categories->first(fn($c) => $c->name === $categoryName);
            $store->categories()->attach($category);
            $stores->push($store);
        }

        foreach ($stores as $i => $store) {
            $category = $store->categories()->first();
            Coupon::create([
                'store_id'=>$store->id, 'category_id'=>$category?->id,
                'title'=>($i % 2 ? 'Save 15% on selected products' : 'Extra 10% off your next order'),
                'slug'=>Str::slug($store->name.' primary offer'), 'type'=>$i % 3 === 0 ? 'deal' : 'code',
                'code'=>$i % 3 === 0 ? null : strtoupper(substr(preg_replace('/[^A-Za-z]/','',$store->name),0,5)).'10',
                'discount_label'=>$i % 2 ? '15% Off' : '10% Off',
                'description'=>"Use this current {$store->name} offer on eligible items. Terms and availability may change.",
                'destination_url'=>'https://example.com', 'is_verified'=>true, 'is_featured'=>$i < 6,
                'expires_at'=>now()->addDays(20 + $i),
            ]);
            Coupon::create([
                'store_id'=>$store->id, 'category_id'=>$category?->id,
                'title'=>'Limited-time seasonal deal', 'slug'=>Str::slug($store->name.' seasonal deal'), 'type'=>'deal',
                'discount_label'=>'Save now', 'description'=>"A limited-time {$store->name} deal on popular products in this category.",
                'destination_url'=>'https://example.com', 'is_verified'=>true,
                'expires_at'=>now()->addDays(45 + $i),
            ]);
        }

        $reviewSeeds = [
            ['BODEGACOOLER 95L Dual-Zone Car Fridge Review','Bodegacooler','Outdoor & Camping'],
            ['3F UL Gear Review: Is It Worth It?','3F UL Gear','Outdoor & Camping'],
            ['Best Portable Displays for Work and Travel','Carpuride','Electronics'],
            ['A Practical Guide to Home Fragrance Diffusers','Aroma360','Home Fragrance'],
            ['How to Choose a Massage Device for Home Use','Comfier','Health & Wellness'],
            ['What to Look for in a Compact Camping Setup','Peak Design','Outdoor & Camping'],
        ];

        foreach ($reviewSeeds as $i => [$title,$storeName,$categoryName]) {
            $store = $stores->firstWhere('name',$storeName);
            $category = $categories->first(fn($c) => $c->name === $categoryName);
            Review::create([
                'store_id'=>$store?->id, 'category_id'=>$category?->id, 'title'=>$title, 'slug'=>Str::slug($title),
                'excerpt'=>'A practical, shopper-first look at the product, the important trade-offs, and who it makes the most sense for.',
                'content'=>"Overview\n\nThis review focuses on the details that matter before buying: usability, value, build quality, limitations and alternatives.\n\nWhat stands out\n\nThe strongest products are not simply the ones with the longest feature lists. We look at whether those features solve real problems and whether the price is reasonable for the intended buyer.\n\nWho should consider it\n\nThis product is best for shoppers who value practical features and clear value. Compare current offers before purchasing because pricing and availability can change.\n\nFinal notes\n\nCheck the store page for updated coupons and deals, and confirm current terms on the merchant website before checkout.",
                'type'=>$i % 3 === 2 ? 'guide' : 'review', 'rating'=>$i % 3 === 2 ? null : 4.5,
                'is_featured'=>$i === 0, 'published_at'=>now()->subDays($i * 5 + 1),
            ]);
        }
    }
}
