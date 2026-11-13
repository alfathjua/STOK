<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Seed the director and foreman accounts.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Director Jada',
            'email' => 'director@jadstock.id',
            'username' => 'director',
            'role' => 'director',
            'password' => Hash::make('password'),
        ]);

        User::create([
            'name' => 'Foreman Jada',
            'email' => 'foreman@jadstock.id',
            'username' => 'foreman',
            'role' => 'foreman',
            'password' => Hash::make('password'),
        ]);
    }
}
