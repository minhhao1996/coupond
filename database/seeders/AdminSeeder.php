<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AdminSeeder extends Seeder
{
    
    public function run(): void
    {
        if (! app()->environment('local', 'testing')) {
            $this->command?->warn('Demo admin seeding is local only. Use php artisan admin:create instead.');

            return;
        }

        $email = 'admin@couponhub.local';
        if (User::where('email', $email)->exists()) {
            $this->command?->info('Admin account already exists; password and permissions were not changed.');

            return;
        }

        $password = 'admin123';
        $user = new User([
            'name' => 'CouponHub Admin',
            'email' => $email,
            'password' => $password,
        ]);
        $user->is_admin = true;
        $user->save();

        $this->command?->info('Admin account created: '.$email);
        $this->command?->line('Temporary password: '.$password);
        $this->command?->line('Sign in at /login and change your password at /admin/account.');
    }
}
