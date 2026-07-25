<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\DictionarySeeder;
use Database\Seeders\PlatformOwnerSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Database\Seeders\TestFacilitySeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->call([
            RolesAndPermissionsSeeder::class,
            PlatformOwnerSeeder::class,
            TestFacilitySeeder::class,
            DictionarySeeder::class,
            
        ]);
    }
}
