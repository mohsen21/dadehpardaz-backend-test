<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => 'علی احمدی',
            'email' => 'ali.ahmadi@example.com',
            'national_code' => '1234567890',
            'is_approver' => false,
        ]);

 
        User::factory()->count(10)->create();
        User::factory()->count(3)->approver()->create();
    }
}
