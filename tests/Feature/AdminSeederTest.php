<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\AdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_admin_without_resetting_existing_credentials(): void
    {
        $this->seed(AdminSeeder::class);
        $user = User::where('email', 'admin@couponhub.local')->firstOrFail();
        $this->assertTrue($user->is_admin);
        $password = $user->password;
        $this->seed(AdminSeeder::class);
        $this->assertDatabaseCount('users', 1);
        $this->assertSame($password, $user->fresh()->password);
    }

    public function test_it_does_not_promote_existing_users(): void
    {
        $user = User::create(['name' => 'Existing', 'email' => 'admin@couponhub.local', 'password' => 'ExistingPassword123!']);
        $this->seed(AdminSeeder::class);
        $this->assertFalse($user->fresh()->is_admin);
    }

    public function test_it_does_not_create_demo_accounts_in_production(): void
    {
        $this->app->instance('env', 'production');
        (new AdminSeeder)->run();
        $this->assertDatabaseCount('users', 0);
    }
}
