<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'علی احمدی',
                'email' => 'ali.ahmadi@example.com',
                'password' => Hash::make('password'),
                'national_code' => '1234567890',
                'is_approver' => false,
            ],
            [
                'name' => 'مریم رضایی',
                'email' => 'maryam.rezaei@example.com',
                'password' => Hash::make('password'),
                'national_code' => '2345678901',
                'is_approver' => false,
            ],
            [
                'name' => 'محمد کریمی',
                'email' => 'mohammad.karimi@example.com',
                'password' => Hash::make('password'),
                'national_code' => '3456789012',
                'is_approver' => true,
            ],
            [
                'name' => 'فاطمه محمدی',
                'email' => 'fateme.mohammadi@example.com',
                'password' => Hash::make('password'),
                'national_code' => '4567890123',
                'is_approver' => false,
            ],
            [
                'name' => 'حسن نوری',
                'email' => 'hasan.nouri@example.com',
                'password' => Hash::make('password'),
                'national_code' => '5678901234',
                'is_approver' => true,
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
