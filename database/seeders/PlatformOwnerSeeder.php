<?php
// database/seeders/PlatformOwnerSeeder.php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PlatformOwnerSeeder extends Seeder
{
    public function run(): void
    {
        $owner = User::firstOrCreate(
            ['email' => 'damalide20@gmail.com'], // CHANGE THIS EMAIL
            [
                'name' => 'Platform Owner',
                'password' => Hash::make('change-this-immediately'), // CHANGE THIS PASSWORD
                'primary_role' => 'platform-owner',
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );

        $owner->assignRole('platform-owner');
    }
}