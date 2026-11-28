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
        $director = User::create([
            'name' => 'Director Jada',
            'email' => 'director@jastock.id',
            'username' => 'director',
            'password' => Hash::make('password'),
        ]);
        $director->role = 'director';
        $director->save();

        $foreman = User::create([
            'name' => 'Foreman Jada',
            'email' => 'foreman@jastock.id',
            'username' => 'foreman',
            'password' => Hash::make('password'),
        ]);
        $foreman->role = 'foreman';
        $foreman->save();
    }
}
