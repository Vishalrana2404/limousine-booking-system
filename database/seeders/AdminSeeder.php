<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@yopmail.com'],
            [
                'first_name' => 'Limousine',
                'last_name' => 'Admin',
                'password' => Hash::make('Ztech@44'), // Hash the password
                'user_type_id' => null,
                'status' =>'Active'
            ]
        );
    }
}
