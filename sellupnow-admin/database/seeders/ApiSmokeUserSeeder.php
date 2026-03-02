<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ApiSmokeUserSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::query()->firstOrCreate(
            ['email' => 'apitest@example.com'],
            [
                'name' => 'API Test User',
                'phone' => '9000000001',
                'password' => Hash::make('secret123'),
                'country' => 'US',
                'is_active' => true,
            ]
        );

        $user->update([
            'name' => $user->name ?: 'API Test User',
            'phone' => $user->phone ?: '9000000001',
            'password' => Hash::make('secret123'),
            'country' => $user->country ?: 'US',
            'is_active' => true,
        ]);

        Customer::query()->firstOrCreate(['user_id' => $user->id]);
    }
}
