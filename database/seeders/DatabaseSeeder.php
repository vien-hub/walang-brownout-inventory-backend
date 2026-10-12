<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Creates the primary administrator once.
     *
     * Railway runs the seeder on every deploy, so this only creates the account
     * when it does not exist yet. It never resets an existing password.
     * Set ADMIN_EMAIL / ADMIN_PASSWORD in the environment to choose the login.
     */
    public function run(): void
    {
        $email = strtolower(trim(config('inventory.admin_email')));

        User::firstOrCreate(
            ['email' => $email],
            [
                'name' => config('inventory.admin_name'),
                'password' => Hash::make(config('inventory.admin_password')),
                'role' => 'Administrator',
                'status' => 'Active',
            ]
        );
    }
}
