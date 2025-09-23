<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::create([
            'active'            => 1,
            'name'              => 'Rifqi Aria',
            'email'             => 'rifqiaria95@gmail.com',
            'email_verified_at' => now(),
            'password'          => bcrypt('password123'), // Bisa diganti dengan password yang lebih aman
            'remember_token'    => Str::random(10)
        ]);

        // Assign role superadmin ke user
        $user->assignRole('superadmin');
    }
}
