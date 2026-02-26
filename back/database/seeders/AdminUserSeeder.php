<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'password' => Hash::make('Xxxzz1@1Pasword'),
                'role' => 'admin',
                'avatar' => 'https://ui-avatars.com/api/?name=Admin',
                'social' => 'site',
            ]
        );
    }
}
