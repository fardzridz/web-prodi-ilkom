<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $name = (string) config('initial-data.admin.name');
        $email = (string) config('initial-data.admin.email');
        $password = (string) config('initial-data.admin.password');

        if ($password === '') {
            throw new RuntimeException('INITIAL_ADMIN_PASSWORD must not be empty.');
        }

        if (app()->isProduction() && $password === 'password') {
            throw new RuntimeException('Set a secure INITIAL_ADMIN_PASSWORD before seeding production.');
        }

        User::query()->firstOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'email_verified_at' => now(),
                'password' => $password,
                'role' => User::ROLE_ADMIN,
            ],
        );
    }
}
