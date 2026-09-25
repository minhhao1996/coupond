<?php

namespace Tests\Feature;

use App\Models\AnalyticsEvent;
use App\Models\Coupon;
use App\Models\Review;
use App\Models\Store;
use App\Models\User;
use App\Support\AnalyticsCountry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AnalyticsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Http::preventStrayRequests();
    }

    public function test_public_visits_capture_country_and_store_without_external_requests(): void
    {
        config(['analytics.enabled' => true, 'analytics.country_lookup_enabled' => true]);
        Http::fake();
        [$review, $coupon] = $this->trackingContent();
        $review->update(['store_id' => $coupon->store_id]);
        $this->withServerVariables(['REMOTE_ADDR' => '8.8.8.8']);

        $this->get(route('reviews.show', $review))->assertOk();
        $this->get(route('stores.show', $coupon->store))->assertOk();
        $this->postJson(route('analytics.copy', $coupon->id))->assertNoContent();

        foreach (['review_view', 'page_view', 'coupon_copy'] as $event) {
            $this->assertDatabaseHas('analytics_events', [
                'event' => $event, 'store_id' => $coupon->store_id, 'country_code' => 'US', 'country_name' => 'United States',
            ]);
        }
        Http::assertNothingSent();
    }

    public function test_ipv6_country_lookup_uses_local_database(): void
    {
        config(['analytics.country_lookup_enabled' => true]);
        Http::fake();
        $country = app(AnalyticsCountry::class)->lookup('2001:4860:4860::8888');
        $this->assertMatchesRegularExpression('/^[A-Z]{2}$/', $country['country_code']);
        $this->assertNotEmpty($country['country_name']);
        Http::assertNothingSent();
    }

    public function test_existing_events_can_be_enriched_without_external_requests(): void
    {
        config(['analytics.enabled' => true, 'analytics.country_lookup_enabled' => false]);
        Http::fake();
        [$review] = $this->trackingContent();
        $this->withServerVariables(['REMOTE_ADDR' => '8.8.8.8'])->get(route('reviews.show', $review))->assertOk();
        $this->assertDatabaseHas('analytics_events', ['event' => 'review_view', 'country_code' => null]);
        config(['analytics.country_lookup_enabled' => true]);
        $this->artisan('analytics:enrich-countries')->assertSuccessful();
        $this->assertDatabaseHas('analytics_events', ['event' => 'review_view', 'country_code' => 'US']);
        Http::assertNothingSent();
    }

    public function test_private_ips_and_disabled_lookup_do_not_contact_provider(): void
    {
        config(['analytics.country_lookup_enabled' => true]);
        Http::fake();
        foreach (['127.0.0.1', '::1', '10.0.0.1', '192.168.1.1', 'fd00::1', null] as $ip) {
            $this->assertSame([], app(AnalyticsCountry::class)->lookup($ip));
        }
        config(['analytics.country_lookup_enabled' => false]);
        $this->assertSame([], app(AnalyticsCountry::class)->lookup('8.8.8.8'));
        Http::assertNothingSent();
    }

    public function test_missing_country_database_keeps_events_and_unknown_country(): void
    {
        config(['analytics.enabled' => true, 'analytics.country_lookup_enabled' => true]);
        config(['analytics.country_database' => storage_path('app/geoip/missing.mmdb')]);
        Http::fake();
        [$review, $coupon] = $this->trackingContent();
        $this->withServerVariables(['REMOTE_ADDR' => '8.8.8.8'])->get(route('reviews.show', $review))->assertOk();
        $this->withServerVariables(['REMOTE_ADDR' => '1.1.1.1'])->postJson(route('analytics.copy', $coupon->id))->assertNoContent();
        $this->assertDatabaseCount('analytics_events', 2);
        $this->assertSame(2, AnalyticsEvent::whereNull('country_code')->count());
        Http::assertNothingSent();
    }

    public function test_store_and_country_filters_apply_to_totals_rankings_logs_and_pagination(): void
    {
        [$review, $coupon] = $this->trackingContent();
        $other = Store::create(['name' => 'Other store', 'slug' => 'other-store']);
        foreach (range(1, 34) as $number) {
            AnalyticsEvent::create([
                'event' => 'coupon_copy', 'subject_id' => $number,
                'title' => $number <= 31 ? 'Matching coupon' : 'Excluded coupon '.$number,
                'path' => '/example', 'visitor' => hash('sha256', (string) $number),
                'dedupe_key' => hash('sha256', (string) $number), 'created_at' => now(),
                'store_id' => $number === 32 ? $other->id : $coupon->store_id,
                'country_code' => $number === 34 ? null : ($number === 33 ? 'US' : 'VN'),
                'country_name' => $number === 34 ? null : ($number === 33 ? 'United States' : 'Vietnam'),
            ]);
        }
        $this->signInAdmin();
        $query = http_build_query(['store_id' => $coupon->store_id, 'country' => 'VN']);
        $this->get('/admin/analytics?'.$query)->assertOk()
            ->assertSee('Vietnam')->assertSee('Tracking store')->assertDontSee('Excluded coupon')
            ->assertViewHas('stats', fn ($stats) => $stats['Successful copies'] === 31 && $stats['Visitor sessions'] === 31)
            ->assertViewHas('topCoupons', fn ($items) => $items->sum('total') === 10)
            ->assertViewHas('logs', fn ($logs) => $logs->total() === 31 && str_contains($logs->nextPageUrl(), 'country=VN') && str_contains($logs->nextPageUrl(), 'store_id='.$coupon->store_id));
        $this->get('/admin/analytics?'.$query.'&page=2')->assertOk()
            ->assertViewHas('logs', fn ($logs) => $logs->count() === 1);
        $this->get('/admin/analytics?country=unknown')->assertOk()
            ->assertViewHas('stats', fn ($stats) => $stats['Successful copies'] === 1);
        $this->getJson('/admin/analytics?store_id=999999&country=invalid')->assertUnprocessable()->assertJsonValidationErrors(['store_id', 'country']);
    }

    public function test_guest_review_views_and_coupon_copies_are_recorded(): void
    {
        config(['analytics.enabled' => true]);
        [$review, $coupon] = $this->trackingContent();

        $this->get(route('reviews.show', $review))->assertOk();
        $this->postJson(route('analytics.copy', $coupon->id))->assertNoContent();

        $this->assertDatabaseHas('analytics_events', ['event' => 'review_view', 'subject_id' => $review->id]);
        $this->assertDatabaseHas('analytics_events', ['event' => 'coupon_copy', 'subject_id' => $coupon->id]);
        $this->assertDatabaseCount('analytics_events', 2);

        $this->signInAdmin();
        $this->get('/admin/analytics')->assertOk()
            ->assertSee('Tracking article')->assertSee('Tracking coupon')
            ->assertViewHas('stats', fn ($stats) => $stats['Article views'] === 1 && $stats['Successful copies'] === 1);
    }

    public function test_admin_activity_is_excluded_and_dashboard_explains_how_to_test(): void
    {
        config(['analytics.enabled' => true]);
        [$review, $coupon] = $this->trackingContent();
        $this->signInAdmin();

        $this->get(route('reviews.show', $review))->assertOk();
        $this->postJson(route('analytics.copy', $coupon->id))->assertNoContent();
        $this->assertDatabaseCount('analytics_events', 0);
        $this->get('/admin/analytics')->assertOk()->assertSee('private/incognito window');
    }

    private function trackingContent(): array
    {
        $store = Store::create(['name' => 'Tracking store', 'slug' => 'tracking-store', 'is_active' => true]);
        $review = Review::create(['title' => 'Tracking article', 'slug' => 'tracking-article', 'content' => 'Article body', 'is_active' => true, 'published_at' => now()->subDay()]);
        $coupon = Coupon::create(['store_id' => $store->id, 'title' => 'Tracking coupon', 'slug' => 'tracking-coupon', 'type' => 'code', 'code' => 'SAVE10', 'is_active' => true]);

        return [$review, $coupon];
    }

    public function test_admin_can_open_analytics_without_activity(): void
    {
        $this->signInAdmin();

        $this->get('/admin/analytics')->assertOk()
            ->assertViewIs('admin.analytics')
            ->assertSee('No activity found.')
            ->assertSee('No activity in this date range.');
    }

    public function test_analytics_renders_totals_rankings_and_filtered_paginated_logs(): void
    {
        $this->signInAdmin();

        foreach (range(1, 32) as $number) {
            AnalyticsEvent::create([
                'event' => $number === 32 ? 'review_view' : 'coupon_copy',
                'subject_id' => 1,
                'title' => $number === 32 ? 'Example article' : 'Example coupon',
                'path' => '/example',
                'visitor' => hash('sha256', 'visitor'),
                'ip' => '192.0.2.1',
                'dedupe_key' => hash('sha256', (string) $number),
                'created_at' => '2026-09-20 12:00:00',
            ]);
        }

        $response = $this->get('/admin/analytics?from=2026-09-20&to=2026-09-20&event=coupon_copy&ip=192.0.2.1');
        $response->assertOk()->assertSee('Example article')->assertSee('Example coupon')
            ->assertSee('31 copies')->assertSee('page=2', false)
            ->assertViewHas('stats', fn ($stats) => $stats === [
                'Page views' => 1, 'Article views' => 1, 'Successful copies' => 31, 'Visitor sessions' => 1,
            ])
            ->assertViewHas('logs', fn ($logs) => $logs->total() === 31 && $logs->count() === 30);

        $this->get('/admin/analytics?from=2026-09-21&to=2026-09-21')
            ->assertOk()->assertSee('No activity found.');
    }

    private function signInAdmin(): void
    {
        $user = new User(['name' => 'Admin', 'email' => 'admin@example.test', 'password' => 'TestPassword123!']);
        $user->is_admin = true;
        $user->save();
        $this->actingAs($user);
    }
}
