<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::insert([
            [
                'name' => 'John Doe',
                'email' => 'bhoxzpaul65@gmail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('asdqweee'),
                'role' => 'admin',
                'remember_token' => 'randomtoken123',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'janesmith@example.com',
                'email_verified_at' => now(),
                'password' => Hash::make('asdqweee'),
                'role' => 'user',
                'remember_token' => 'randomtoken456',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Michael Johnson',
                'email' => 'michaelj@example.com',
                'email_verified_at' => now(),
                'password' => Hash::make('asdqweee'),
                'role' => 'vendor',
                'remember_token' => 'randomtoken789',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
