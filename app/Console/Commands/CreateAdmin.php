<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class CreateAdmin extends Command
{
    protected $signature = 'admin:create {email?} {--name=}';

    protected $description = 'Create an administrator with an interactively entered password';

    public function handle(): int
    {
        $data = [
            'name' => $this->option('name') ?: $this->ask('Name'),
            'email' => Str::lower(trim($this->argument('email') ?: $this->ask('Email'))),
            'password' => $this->secret('Password (at least 12 characters, including letters and numbers)'),
            'password_confirmation' => $this->secret('Confirm password'),
        ];
        $validator = Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(12)->letters()->numbers(), 'max:255'],
        ]);
        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        $user = new User(collect($data)->only(['name', 'email', 'password'])->all());
        $user->is_admin = true;
        $user->save();
        $this->info('Administrator created. Sign in at /login.');

        return self::SUCCESS;
    }
}
