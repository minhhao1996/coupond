<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Coupon;
use App\Models\Review;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    private function admin(bool $admin = true): User
    {
        $user = new User(['name' => 'Test Admin', 'email' => 'admin@example.test', 'password' => 'TestPassword123!']);
        $user->is_admin = $admin;
        $user->save();

        return $user;
    }

    public function test_guests_cannot_read_or_modify_admin_content(): void
    {
        foreach (['/admin', '/admin/coupons', '/admin/stores/create', '/admin/account'] as $url) {
            $this->get($url)->assertRedirect('/login');
        }
        $this->post('/admin/categories', ['name' => 'Unauthorized'])->assertRedirect('/login');
        $this->put('/admin/categories/1', [])->assertRedirect('/login');
        $this->delete('/admin/categories/1')->assertRedirect('/login');
        $this->assertDatabaseCount('categories', 0);
    }

    public function test_non_admin_is_rejected_even_when_authenticated(): void
    {
        $this->actingAs($this->admin(false));
        $this->get('/admin')->assertForbidden();
        $this->post('/admin/categories', ['name' => 'Forbidden'])->assertForbidden();
        $this->delete('/admin/coupons/1')->assertForbidden();
    }

    public function test_admin_can_sign_in_and_sign_out(): void
    {
        $user = $this->admin();
        $this->get('/login')->assertOk()->assertSee('Welcome back.');
        $this->post('/login', ['email' => 'ADMIN@example.test', 'password' => 'TestPassword123!'])->assertRedirect('/admin');
        $this->assertAuthenticatedAs($user);
        $this->get('/admin')->assertOk()->assertSee('Recently updated offers');
        $this->post('/logout')->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_regular_users_cannot_sign_in_to_admin(): void
    {
        $this->admin(false);
        $this->post('/login', ['email' => 'admin@example.test', 'password' => 'TestPassword123!'])->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_failed_login_is_rate_limited(): void
    {
        $this->admin();
        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', ['email' => 'admin@example.test', 'password' => 'incorrect'])->assertSessionHasErrors('email');
        }
        $this->post('/login', ['email' => 'admin@example.test', 'password' => 'TestPassword123!'])
            ->assertSessionHasErrors('email')
            ->assertSessionHas('errors', fn ($errors) => str_starts_with($errors->first('email'), 'Too many attempts. Try again in '));
        $this->assertGuest();
    }

    public function test_admin_forms_and_empty_lists_render(): void
    {
        $this->actingAs($this->admin());
        foreach (['categories', 'stores', 'coupons', 'reviews'] as $resource) {
            $this->get('/admin/'.$resource)->assertOk()->assertSee('No items found');
            $this->get('/admin/'.$resource.'/create')->assertOk()->assertSee('Save');
        }
        $this->get('/admin/account')->assertOk();
        $this->get('/admin/users')->assertNotFound();
    }

    public function test_category_and_store_crud_syncs_relationships_and_preserves_content(): void
    {
        $this->actingAs($this->admin());
        $this->post('/admin/categories', ['name' => 'Camping', 'is_active' => 1])->assertSessionHasNoErrors();
        $category = Category::firstOrFail();
        $this->assertSame('camping', $category->slug);
        $this->post('/admin/stores', ['name' => 'Outdoor Shop', 'category_ids' => [$category->id], 'is_active' => 1])->assertSessionHasNoErrors();
        $store = Store::firstOrFail();
        $this->assertTrue($store->categories->contains($category));
        $this->get('/admin/stores/'.$store->id.'/edit')->assertOk()->assertSee('Outdoor Shop');
        $this->delete('/admin/categories/'.$category->id)->assertSessionHasErrors('delete');
        $this->assertDatabaseHas('categories', ['id' => $category->id]);
        $this->put('/admin/stores/'.$store->id, ['name' => 'New Shop Name', 'slug' => $store->slug, 'is_active' => 0])->assertSessionHasNoErrors();
        $this->assertSame(0, $store->fresh()->categories()->count());
        $this->assertFalse($store->fresh()->is_active);
        $this->get('/admin/stores?status=hidden&q=New')->assertOk()->assertSee('New Shop Name');
        $this->delete('/admin/categories/'.$category->id)->assertRedirect('/admin/categories');
        $this->delete('/admin/stores/'.$store->id)->assertRedirect('/admin/stores');
    }

    public function test_coupon_validation_rejects_bad_code_urls_dates_and_relations(): void
    {
        $this->actingAs($this->admin());
        $store = Store::create(['name' => 'Shop', 'slug' => 'shop']);
        $this->post('/admin/coupons', [
            'title' => 'Bad coupon', 'store_id' => $store->id, 'type' => 'code',
            'destination_url' => 'javascript:alert(1)', 'category_id' => 999,
            'starts_at' => '2026-09-23', 'expires_at' => '2026-09-22',
        ])->assertSessionHasErrors(['code', 'destination_url', 'category_id', 'expires_at']);
        $this->assertDatabaseCount('coupons', 0);
    }

    public function test_coupon_can_be_created_updated_and_deleted_without_mass_assigning_clicks(): void
    {
        $this->actingAs($this->admin());
        $store = Store::create(['name' => 'Shop', 'slug' => 'shop']);
        $data = ['title' => 'Save ten', 'store_id' => $store->id, 'type' => 'code', 'code' => 'TEN', 'is_active' => 1, 'clicks' => 999];
        $this->post('/admin/coupons', $data)->assertSessionHasNoErrors();
        $coupon = Coupon::firstOrFail();
        $this->assertEquals(0, $coupon->clicks);
        $this->get('/admin/coupons/'.$coupon->id.'/edit')->assertOk()->assertSee('TEN');
        $this->put('/admin/coupons/'.$coupon->id, [...$data, 'code' => 'TWENTY'])->assertSessionHasNoErrors();
        $this->assertSame('TWENTY', $coupon->fresh()->code);
        $this->delete('/admin/stores/'.$store->id)->assertSessionHasErrors('delete');
        $this->get('/admin/coupons/'.$coupon->id.'/delete')->assertOk()->assertSee('Delete permanently');
        $this->delete('/admin/coupons/'.$coupon->id)->assertRedirect('/admin/coupons');
        $this->assertDatabaseCount('coupons', 0);
    }

    public function test_duplicate_slugs_and_invalid_articles_are_rejected(): void
    {
        $this->actingAs($this->admin());
        Category::create(['name' => 'Existing', 'slug' => 'existing']);
        $this->post('/admin/categories', ['name' => 'Existing'])->assertSessionHasErrors('slug');
        $this->post('/admin/reviews', ['title' => 'Test', 'type' => 'guide', 'rating' => 6])->assertSessionHasErrors(['content', 'rating']);
        $this->post('/admin/reviews', ['title' => 'Test Guide', 'type' => 'guide', 'content' => 'A helpful guide.', 'rating' => 4.5])->assertSessionHasNoErrors();
        $article = Review::firstOrFail();
        $this->get('/admin/reviews/'.$article->id.'/edit')->assertOk()->assertSee('A helpful guide.');
        $this->put('/admin/reviews/'.$article->id, ['title' => 'Updated', 'type' => 'guide', 'content' => 'Updated article.'])->assertSessionHasNoErrors();
        $this->assertSame('Updated', $article->fresh()->title);
        $this->delete('/admin/reviews/'.$article->id)->assertRedirect('/admin/reviews');
    }

    public function test_password_change_requires_current_password_and_confirmation(): void
    {
        $user = $this->admin();
        $this->actingAs($user);
        $this->put('/admin/account/password', ['current_password' => 'wrong', 'password' => 'short'])->assertSessionHasErrors(['current_password', 'password']);
        $this->put('/admin/account/password', ['current_password' => 'TestPassword123!', 'password' => 'ReplacementPassword123!', 'password_confirmation' => 'ReplacementPassword123!'])->assertSessionHasNoErrors();
        $this->assertTrue(Hash::check('ReplacementPassword123!', $user->fresh()->password));
    }
}
