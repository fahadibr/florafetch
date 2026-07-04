<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@florafetch.com'],
            [
                'name'     => 'FloraFetch Admin',
                'email'    => 'admin@florafetch.com',
                'password' => Hash::make('Admin1234'),
                'role'     => 'admin',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
    }
}
