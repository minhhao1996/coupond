<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginSessionTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_is_not_cacheable_and_posts_to_the_same_origin(): void
    {
        $response = $this->get('/login')->assertOk()
            ->assertHeader('X-LiteSpeed-Cache-Control', 'no-cache')
            ->assertSee('action="/login"', false);
        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));
        $this->assertStringContainsString('private', $response->headers->get('Cache-Control'));
    }

    public function test_csrf_remains_required_for_login(): void
    {
        $this->app->instance('env', 'production');
        $this->withSession(['_token' => 'valid-session-token'])->post('/login', [
            '_token' => 'wrong-token', 'email' => 'test@example.test', 'password' => 'WrongPassword123!',
        ])->assertStatus(419);
        $this->withSession(['_token' => 'valid-session-token'])->post('/login', [
            '_token' => 'valid-session-token', 'email' => 'test@example.test', 'password' => 'WrongPassword123!',
        ])->assertSessionHasErrors('email')->assertRedirect();
    }
}
