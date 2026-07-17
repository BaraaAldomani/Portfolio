<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Local convenience only. On production create the admin with
 * `php artisan app:create-admin` using ADMIN_EMAIL / ADMIN_PASSWORD.
 */
class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@example.com')],
            [
                'name' => env('ADMIN_NAME', 'Baraa'),
                'password' => Hash::make(env('ADMIN_PASSWORD', 'password')),
                'is_admin' => true,
            ],
        );
    }
}
