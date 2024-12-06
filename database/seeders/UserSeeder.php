<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'username' => 'admin',
            'email'=> 'admin@gmail.com',
            'password' => bcrypt('12345678'),
            'phone_number' => '1234567890',
            'role' => 'admin'
        ]);
    }
}
