<?php

namespace Tests\Feature;

use App\Models\Review;
use App\Models\Store;
use App\Models\User;
use App\Support\ImageUpload;
use App\Support\Media;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CloudinaryMediaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config([
            'media.driver' => 'cloudinary', 'media.cloudinary.cloud_name' => 'test-cloud',
            'media.cloudinary.api_key' => 'test-key', 'media.cloudinary.api_secret' => 'test-secret',
        ]);
        Storage::fake('public');
        Http::preventStrayRequests();
        $user = new User(['name' => 'Admin', 'email' => 'cloud@example.test', 'password' => 'Password123!']);
        $user->is_admin = true;
        $user->save();
        $this->actingAs($user);
    }

    private function fakeUpload(): void
    {
        Http::fake(['api.cloudinary.com/v1_1/test-cloud/image/upload' => function (Request $request) {
            $fields = collect($request->data())->pluck('contents', 'name');

            return Http::response(['public_id' => $fields['public_id'], 'version' => 12345, 'format' => 'webp']);
        }]);
    }

    public function test_store_upload_uses_cloudinary_webp_and_sized_logo(): void
    {
        $this->fakeUpload();
        $this->post('/admin/stores', ['name' => 'Camera Store', 'is_active' => 1, 'logo_upload' => UploadedFile::fake()->image('logo.png')])->assertSessionHasNoErrors();
        $store = Store::firstOrFail();
        $this->assertStringStartsWith('https://res.cloudinary.com/test-cloud/image/upload/v12345/couponhub/stores/camera-store-', $store->logo);
        $this->assertStringEndsWith('.webp', $store->logo);
        $this->assertStringContainsString('f_webp,q_auto,c_limit,w_192/', $store->logo_url);
        $this->assertSame([], Storage::disk('public')->allFiles());
        Http::assertSent(function (Request $request) {
            $fields = collect($request->data())->pluck('contents', 'name');

            return $request->hasFile('file') && $fields['format'] === 'webp'
                && $fields['transformation'] === 'c_limit,w_512,h_512,q_auto'
                && $fields['overwrite'] === 'false'
                && $request->hasHeader('Authorization', 'Basic '.base64_encode('test-key:test-secret'));
        });
        $this->get('/store/'.$store->slug)->assertOk()->assertSee('f_webp,q_auto,c_limit,w_192/', false)->assertDontSee('test-secret');
    }

    public function test_cover_and_inline_uploads_are_webp_and_article_has_responsive_images(): void
    {
        $this->fakeUpload();
        $this->post('/admin/reviews', [
            'title' => 'Camera Guide', 'type' => 'review', 'content' => 'A helpful guide', 'is_active' => 1,
            'image_upload' => UploadedFile::fake()->image('cover.jpg', 1800, 900),
        ])->assertSessionHasNoErrors();
        $review = Review::firstOrFail();
        $this->assertStringEndsWith('.webp', $review->image);
        $this->get('/reviews/'.$review->slug)->assertOk()->assertSee('srcset=', false)
            ->assertSee('f_webp,q_auto,c_limit,w_480/', false)
            ->assertSee('f_webp,q_auto,c_limit,w_1200/', false)
            ->assertSee('alt="Camera Guide"', false)->assertSee('fetchpriority="high"', false);
        $this->postJson('/admin/media/images', ['image' => UploadedFile::fake()->image('camera-detail.png')])
            ->assertCreated()->assertJsonPath('url', fn ($url) => str_contains($url, '/f_webp,q_auto,c_limit,w_1600/') && str_ends_with($url, '.webp'));
        $this->assertSame([], Storage::disk('public')->allFiles());
        Http::assertSentCount(2);
    }

    public function test_upload_failure_preserves_existing_image_and_hides_provider_details(): void
    {
        Http::fake(['*' => Http::response(['error' => ['message' => 'private provider diagnostic']], 401)]);
        $store = Store::create(['name' => 'Existing', 'slug' => 'existing', 'logo' => 'https://example.test/old.jpg']);
        $response = $this->putJson('/admin/stores/'.$store->id, ['name' => 'Existing', 'logo_upload' => UploadedFile::fake()->image('new.jpg')]);
        $response->assertUnprocessable()->assertJsonValidationErrors('logo_upload')
            ->assertDontSee('private provider diagnostic')->assertDontSee('test-secret');
        $this->assertSame('https://example.test/old.jpg', $store->fresh()->logo);
        Http::assertSentCount(1);
    }

    public function test_missing_credentials_and_invalid_uploads_never_contact_cloudinary(): void
    {
        Http::fake();
        config(['media.cloudinary.api_secret' => null]);
        $this->postJson('/admin/media/images', ['image' => UploadedFile::fake()->image('photo.jpg')])->assertUnprocessable()->assertJsonValidationErrors('image');
        $this->postJson('/admin/media/images', ['image' => UploadedFile::fake()->createWithContent('bad.svg', '<svg/>')])->assertUnprocessable();
        Http::assertNothingSent();
    }

    public function test_legacy_external_and_signed_urls_are_preserved(): void
    {
        $this->assertSame(url('/storage/uploads/reviews/legacy.jpg'), Media::url('uploads/reviews/legacy.jpg'));
        foreach (['https://example.test/photo.jpg', 'https://res.cloudinary.com/other/image/upload/v1/photo.jpg', 'https://res.cloudinary.com/test-cloud/image/upload/s--signature--/v1/photo.jpg'] as $url) {
            $this->assertSame($url, Media::url($url));
            $this->assertNull(Media::srcset($url));
        }
        $this->assertNull(Media::url('javascript:alert(1)'));
    }

    public function test_failed_save_cleanup_targets_only_new_own_cloud_uploads(): void
    {
        Http::fake(['*' => Http::response(['result' => 'ok'])]);
        app(ImageUpload::class)->discardNewUpload('https://res.cloudinary.com/other/image/upload/v1/couponhub/stores/image.webp');
        Http::assertNothingSent();
        app(ImageUpload::class)->discardNewUpload('https://res.cloudinary.com/test-cloud/image/upload/v1/couponhub/stores/image-abc.webp');
        Http::assertSent(fn (Request $request) => str_ends_with($request->url(), '/image/destroy') && $request['public_id'] === 'couponhub/stores/image-abc');
    }
}
