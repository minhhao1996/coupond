<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Coupon;
use App\Models\Review;
use App\Models\Store;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoTest extends TestCase
{
    use RefreshDatabase;

    private function content(): array
    {
        $store = Store::create(['name' => 'Camera Shop', 'slug' => 'camera-shop', 'seo_title' => 'Camera Deals', 'seo_description' => 'Save on camera equipment.']);
        $category = Category::create(['name' => 'Cameras', 'slug' => 'cameras', 'seo_title' => 'Camera Offers', 'seo_description' => 'Compare camera offers.']);
        $review = Review::create(['title' => 'Camera Buying Guide', 'slug' => 'camera-buying-guide', 'content' => 'Compare the cameras.', 'published_at' => now()->subDay(), 'seo_title' => 'Choosing a Camera', 'seo_description' => 'A guide to choosing cameras.', 'image' => 'https://example.test/camera.jpg']);

        return [$store, $category, $review];
    }

    public function test_sitemap_contains_only_public_canonical_pages(): void
    {
        [$store, $category, $review] = $this->content();
        Store::create(['name' => 'Hidden', 'slug' => 'hidden', 'is_active' => false]);
        Review::create(['title' => 'Draft', 'slug' => 'draft', 'content' => 'Draft']);
        Review::create(['title' => 'Future', 'slug' => 'future', 'content' => 'Scheduled', 'published_at' => now()->addDay()]);
        config(['seo.url' => 'https://couponhub.example']);
        $index = $this->get('/sitemap.xml')->assertOk()->assertHeader('Content-Type', 'application/xml; charset=UTF-8');
        $this->assertNotFalse(simplexml_load_string($index->getContent()));
        $index->assertSee('https://couponhub.example/sitemaps/stores-1.xml', false);
        $this->get('/sitemaps/stores-1.xml')->assertOk()->assertSee('/store/camera-shop', false)->assertDontSee('/store/hidden', false);
        $this->get('/sitemaps/categories-1.xml')->assertOk()->assertSee('/category/cameras', false);
        $this->get('/sitemaps/reviews-1.xml')->assertOk()->assertSee('/reviews/camera-buying-guide', false)->assertSee('<lastmod>', false)->assertDontSee('/reviews/draft', false)->assertDontSee('/reviews/future', false);
        $this->get('/sitemaps/pages-1.xml')->assertOk()->assertDontSee('/login')->assertDontSee('/admin')->assertDontSee('/search');
        $this->get('/sitemaps/reviews-0.xml')->assertNotFound();
        $this->get('/sitemaps/reviews-2.xml')->assertNotFound();
        $this->get('/robots.txt')->assertOk()->assertSee('Sitemap: https://couponhub.example/sitemap.xml', false);
    }

    public function test_detail_metadata_uses_admin_seo_fields_and_valid_json(): void
    {
        $this->content();
        foreach (['/store/camera-shop' => 'Camera Deals', '/category/cameras' => 'Camera Offers', '/reviews/camera-buying-guide' => 'Choosing a Camera'] as $url => $title) {
            $response = $this->get($url)->assertOk()->assertSee('<title>'.$title.'</title>', false)->assertSee('aria-label="Breadcrumb"', false);
            preg_match('/<script type="application\/ld\+json">(.*?)<\/script>/s', $response->getContent(), $matches);
            $graph = json_decode($matches[1], true, 512, JSON_THROW_ON_ERROR)['@graph'];
            $this->assertContains('BreadcrumbList', array_column($graph, '@type'));
        }
        $this->get('/reviews/camera-buying-guide')->assertSee('content="article"', false)->assertSee('https://example.test/camera.jpg', false)->assertSee('"@type":"Article"', false);
    }

    public function test_index_titles_do_not_inherit_loop_items_and_pagination_has_own_canonical(): void
    {
        [$store] = $this->content();
        for ($i = 1; $i <= 13; $i++) {
            Coupon::create(['store_id' => $store->id, 'title' => 'Offer '.$i, 'slug' => 'offer-'.$i, 'type' => 'deal']);
        }
        $this->get('/')->assertOk()->assertSee('<title>VibeTechCoupons — Coupons, Deals &amp; Shopping Guides</title>', false);
        $this->get('/stores')->assertOk()->assertSee('<title>Store Coupons &amp; Promo Codes — VibeTechCoupons</title>', false);
        config(['seo.url' => 'https://couponhub.example']);
        $this->get('/store/camera-shop?page=2&utm_source=ad')->assertOk()->assertSee('href="https://couponhub.example/store/camera-shop?page=2"', false)->assertSee('Camera Deals — Page 2', false)->assertDontSee('utm_source=ad');
        $this->get('/store/camera-shop?page=1')->assertSee('rel="canonical" href="https://couponhub.example/store/camera-shop"', false);
        $this->get('/store/camera-shop?page=99')->assertNotFound();
        $this->get('/reviews?page=99')->assertNotFound();
        $this->get('/category/cameras?page=99')->assertNotFound();
        $this->get('/search?q=camera')->assertOk()->assertSee('content="noindex,follow"', false);
    }

    public function test_unpublished_articles_are_not_public_or_discoverable(): void
    {
        Review::create(['title' => 'Private Draft', 'slug' => 'private-draft', 'content' => 'Hidden draft']);
        Review::create(['title' => 'Future Article', 'slug' => 'future-article', 'content' => 'Hidden future', 'published_at' => now()->addWeek()]);
        $this->get('/reviews/private-draft')->assertNotFound();
        $this->get('/reviews/future-article')->assertNotFound();
        $this->get('/reviews')->assertOk()->assertDontSee('Private Draft')->assertDontSee('Future Article');
        $this->get('/search?q=Private')->assertOk()->assertDontSee('Private Draft');
    }
}
