<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'phone' => '0992453357',
            'password' => Hash::make('12345678'), // تشفير كلمة السر كما يفعل Laravel
            'role' => 'admin',
            'is_approved' => true,
            'otp_code' => '123456',
            'otp_expires_at' => now()->addYears(1),
        ]);
    }
}