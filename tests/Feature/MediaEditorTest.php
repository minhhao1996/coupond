<?php

namespace Tests\Feature;

use App\Livewire\CouponCard;
use App\Models\Coupon;
use App\Models\Review;
use App\Models\Store;
use App\Models\User;
use App\Support\ReviewContent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class MediaEditorTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['media.driver' => 'local']);
        Storage::fake('public');
        $user = new User(['name' => 'Admin', 'email' => 'media@example.test', 'password' => 'Password123!']);
        $user->is_admin = true;
        $user->save();
        $this->actingAs($user);
    }

    public function test_store_upload_is_stored_displayed_preserved_and_removable(): void
    {
        $this->post('/admin/stores', ['name' => 'Photo Store', 'is_active' => 1, 'logo_upload' => UploadedFile::fake()->image('logo.jpg', 300, 300)])->assertSessionHasNoErrors();
        $store = Store::firstOrFail();
        Storage::disk('public')->assertExists($store->logo);
        $this->get('/store/'.$store->slug)->assertOk()->assertSee('/storage/'.$store->logo, false);
        $coupon = Coupon::create(['store_id' => $store->id, 'title' => 'Offer', 'slug' => 'photo-offer', 'type' => 'code', 'code' => 'PHOTO']);
        Livewire::test(CouponCard::class, ['coupon' => $coupon])->assertSeeHtml('/storage/'.$store->logo);
        $this->put('/admin/stores/'.$store->id, ['name' => 'Photo Store', 'is_active' => 1])->assertSessionHasNoErrors();
        $this->assertSame($store->logo, $store->fresh()->logo);
        $this->put('/admin/stores/'.$store->id, ['name' => 'Photo Store', 'logo_upload' => UploadedFile::fake()->image('replacement.webp', 200, 200)])->assertSessionHasNoErrors();
        $this->assertNotSame($store->logo, $store->fresh()->logo);
        Storage::disk('public')->assertExists($store->fresh()->logo);
        $this->put('/admin/stores/'.$store->id, ['name' => 'Photo Store', 'logo_remove' => 1])->assertSessionHasNoErrors();
        $this->assertNull($store->fresh()->logo);
    }

    public function test_local_uploads_are_real_webp_and_resized_without_upscaling(): void
    {
        $this->post('/admin/stores', ['name' => 'Large Logo', 'logo_upload' => UploadedFile::fake()->image('large.png', 2560, 1440)])->assertSessionHasNoErrors();
        $store = Store::firstOrFail();
        $this->assertStringEndsWith('.webp', $store->logo);
        $image = getimagesize(Storage::disk('public')->path($store->logo));
        $this->assertSame('image/webp', $image['mime']);
        $this->assertSame([512, 288], [$image[0], $image[1]]);

        $response = $this->postJson('/admin/media/images', ['image' => UploadedFile::fake()->image('wide.jpg', 2400, 1200)])->assertCreated();
        $image = getimagesize(Storage::disk('public')->path(str_replace('/storage/', '', $response->json('url'))));
        $this->assertSame('image/webp', $image['mime']);
        $this->assertSame([1600, 800], [$image[0], $image[1]]);

        $response = $this->postJson('/admin/media/images', ['image' => UploadedFile::fake()->image('small.png', 100, 50)])->assertCreated();
        $image = getimagesize(Storage::disk('public')->path(str_replace('/storage/', '', $response->json('url'))));
        $this->assertSame([100, 50], [$image[0], $image[1]]);
    }

    public function test_review_image_and_safe_html_survive_save_and_edit(): void
    {
        $this->post('/admin/reviews', [
            'title' => 'Photo Review', 'type' => 'review', 'is_active' => 1,
            'content_format' => 'html',
            'content' => '<h2>Overview</h2><p><strong>Good</strong> camera.</p><ul><li>Lightweight</li></ul><script>alert(1)</script><a href="javascript:alert(1)">Bad link</a>',
            'image_upload' => UploadedFile::fake()->image('cover.png', 800, 400),
        ])->assertSessionHasNoErrors();
        $review = Review::firstOrFail();
        Storage::disk('public')->assertExists($review->image);
        $this->assertStringNotContainsString('<script', $review->content);
        $this->assertStringNotContainsString('javascript:', $review->content);
        $this->get('/reviews/'.$review->slug)->assertOk()->assertSee('<h2>Overview</h2>', false)->assertSee('<strong>Good</strong>', false)->assertSee('/storage/'.$review->image, false);
        $this->get('/admin/reviews/'.$review->id.'/edit')->assertOk()->assertSee('data-review-editor', false)->assertSee('data-format="html"', false);
    }

    public function test_invalid_uploads_and_empty_html_are_rejected(): void
    {
        $this->post('/admin/stores', ['name' => 'Unsafe', 'logo_upload' => UploadedFile::fake()->createWithContent('bad.svg', '<svg onload="alert(1)"></svg>')])->assertSessionHasErrors('logo_upload');
        $this->post('/admin/stores', ['name' => 'Too large', 'logo_upload' => UploadedFile::fake()->image('big.jpg')->size(6000)])->assertSessionHasErrors('logo_upload');
        $this->assertDatabaseCount('stores', 0);
        $this->post('/admin/reviews', ['title' => 'Empty', 'type' => 'review', 'content_format' => 'html', 'content' => '<p><br></p>'])->assertSessionHasErrors('content');
        $this->assertSame([], Storage::disk('public')->allFiles());
    }

    public function test_legacy_plain_text_is_escaped_with_line_breaks(): void
    {
        $html = ReviewContent::render("Old review\n<strong>literal</strong>", 'text');
        $this->assertStringContainsString('<br', $html);
        $this->assertStringContainsString('&lt;strong&gt;', $html);
    }

    public function test_review_affiliate_offer_saves_and_renders_in_public_and_preview(): void
    {
        $url = 'https://example.com/buy?aff=my-id&campaign=review';
        $this->post('/admin/reviews', [
            'title' => 'Affiliate article', 'type' => 'review', 'content' => 'Review content',
            'is_active' => 1, 'affiliate_url' => $url, 'affiliate_label' => 'See current offer',
        ])->assertSessionHasNoErrors();
        $review = Review::firstOrFail();
        $this->assertSame($url, $review->affiliate_url);
        foreach (['/reviews/'.$review->slug, '/admin/reviews/'.$review->id.'/preview'] as $path) {
            $this->get($path)->assertOk()->assertSee('See current offer')
                ->assertSee('href="'.e($url).'"', false)
                ->assertSee('rel="sponsored nofollow noopener noreferrer"', false)
                ->assertSee('review.css')->assertSee('review-article');
        }
        $this->put('/admin/reviews/'.$review->id, [
            'title' => 'Affiliate article', 'type' => 'review', 'content' => 'Review content',
            'is_active' => 1, 'affiliate_url' => '', 'affiliate_label' => '',
        ])->assertSessionHasNoErrors();
        $this->get('/reviews/'.$review->slug)->assertOk()->assertDontSee('Ready to try it?');
    }

    public function test_review_affiliate_link_rejects_unsafe_schemes(): void
    {
        $this->post('/admin/reviews', [
            'title' => 'Unsafe affiliate', 'type' => 'review', 'content' => 'Review content',
            'affiliate_url' => 'javascript:alert(1)',
        ])->assertSessionHasErrors('affiliate_url');
        $this->assertDatabaseCount('reviews', 0);
    }

    public function test_inline_images_require_admin_and_are_validated(): void
    {
        $response = $this->post('/admin/media/images', ['image' => UploadedFile::fake()->image('inline.png', 600, 300)], ['Accept' => 'application/json'])->assertCreated();
        $path = str_replace('/storage/', '', $response->json('url'));
        Storage::disk('public')->assertExists($path);
        $this->postJson('/admin/media/images', ['image' => UploadedFile::fake()->createWithContent('x.svg', '<svg onload="alert(1)"></svg>')])->assertUnprocessable();
        auth()->logout();
        $this->postJson('/admin/media/images', ['image' => UploadedFile::fake()->image('guest.jpg')])->assertUnauthorized();
    }

    public function test_editor_preserves_safe_figures_and_tables_in_private_preview(): void
    {
        $html = '<p>Introduction</p><figure><img src="/storage/uploads/reviews/demo.png" alt="Demo" onerror="alert(1)"><figcaption>Photo credit</figcaption></figure><table><caption>Compare</caption><thead><tr><th>Feature</th><th>A</th></tr></thead><tbody><tr><td>Price</td><td>100</td></tr></tbody></table>';
        $this->post('/admin/reviews', ['title' => 'Layout test', 'type' => 'review', 'content_format' => 'html', 'content' => $html])->assertSessionHasNoErrors();
        $review = Review::firstOrFail();
        $this->assertStringContainsString('<figcaption>Photo credit</figcaption>', $review->content);
        $this->assertStringContainsString('<th>Feature</th>', $review->content);
        $this->assertStringNotContainsString('onerror', $review->content);
        $this->get('/admin/reviews/'.$review->id.'/preview')->assertOk()->assertHeader('X-Robots-Tag', 'noindex, nofollow')->assertSee('<table>', false)->assertSee('<figure>', false);
        $this->get('/reviews/'.$review->slug)->assertNotFound();
        auth()->logout();
        $this->get('/admin/reviews/'.$review->id.'/preview')->assertRedirect('/login');
    }
}
