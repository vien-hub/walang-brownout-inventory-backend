<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Primary administrator. Change this password after your first login.
        User::updateOrCreate(
            ['email' => 'justineralph107@gmail.com'],
            [
                'name' => 'Justin Ralph',
                'password' => Hash::make('Example123'),
                'role' => 'Administrator',
                'status' => 'Active',
            ]
        );
    }
}
