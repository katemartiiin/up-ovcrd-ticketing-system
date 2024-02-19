<?php

namespace Database\Seeders;

use App\Models\Users\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Client One',
            'first_name' => 'Client',
            'last_name' => 'One',
            'email' => 'client_one@upd.edu.ph',
            'email_verified_at' => now(),
            'password' => Hash::make('client123'),
            'image_path' => 'https://placehold.co/500x500'
        ]);

        User::create([
            'name' => 'Client Two',
            'first_name' => 'Client',
            'last_name' => 'Two',
            'email' => 'client_two@upd.edu.ph',
            'email_verified_at' => now(),
            'password' => Hash::make('client123'),
            'image_path' => 'https://placehold.co/500x500'
        ]);
    }
}
