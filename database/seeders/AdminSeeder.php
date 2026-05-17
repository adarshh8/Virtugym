<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('ADMIN_EMAIL', 'admin@virtugym.com');

        $admin = User::firstOrNew(['email' => $email]);
        $admin->forceFill([
            'name' => env('ADMIN_NAME', 'VirtuGym Admin'),
            'email' => $email,
            'password' => Hash::make(env('ADMIN_PASSWORD', 'admin123')),
            'role' => 'admin',
            'email_verified_at' => now(),
        ])->save();

        $this->command?->info('Admin user seeded: '.$email);
    }
}
