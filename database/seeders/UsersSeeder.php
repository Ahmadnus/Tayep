<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    public function run()
    {
        for ($i = 1; $i <= 10; $i++) {
            User::create([
                'name' => 'User'.$i,
                'email' => "user$i@example.com",
                'password' => Hash::make('password'),
            ]);
        }
    }
}
