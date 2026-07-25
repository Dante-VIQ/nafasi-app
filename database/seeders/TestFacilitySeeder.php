<?php
// database/seeders/TestFacilitySeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tenant;
use App\Models\Tenant\Facility;

class TestFacilitySeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::first();                     // grab the test tenant
        if (!$tenant) {
            $this->command->warn('No tenant found. Skipping facility seeding.');
            return;
        }

        tenancy()->initialize($tenant);               // switch context

        $facilities = [
            [
                'uuid' => 'test-facility-1',
                'name' => 'Thika Level 5 Hospital',
                'slug' => 'thika-level-5-hospital',
                'facility_type' => 'hospital',
                'phone' => '+254700111222',
                'email' => 'thika5@health.go.ke',
                'address' => 'Thika Town, Kiambu County',
                'latitude' => -1.0396,
                'longitude' => 37.0697,
                'is_24_hours' => true,
                'is_active' => true,
                'is_verified' => true,
                'registration_status' => 'approved',
                'capabilities' => ['emergency_department','icu','surgery','laboratory','pharmacy','antenatal'],
                'emergency_keywords' => ['accident','heart attack','stroke'],
                'congestion_status' => 'moderate',
                'subscription_tier' => 'free',
                'subscription_status' => 'active',
            ],
            [
                'uuid' => 'test-facility-2',
                'name' => 'Good Health Pharmacy',
                'slug' => 'good-health-pharmacy',
                'facility_type' => 'pharmacy',
                'phone' => '+254722333444',
                'address' => 'Kenyatta Avenue, Thika',
                'latitude' => -1.0385,
                'longitude' => 37.0710,
                'is_24_hours' => false,
                'is_active' => true,
                'is_verified' => true,
                'registration_status' => 'approved',
                'capabilities' => ['pharmacy','hiv_testing'],
                'emergency_keywords' => [],
                'congestion_status' => 'low',
                'subscription_tier' => 'free',
                'subscription_status' => 'active',
            ],
            [
                'uuid' => 'test-facility-3',
                'name' => 'Ruiru Health Centre',
                'slug' => 'ruiru-health-centre',
                'facility_type' => 'health_centre',
                'phone' => '+254711555666',
                'address' => 'Ruiru Town, Kiambu',
                'latitude' => -1.1492,
                'longitude' => 36.9606,
                'is_24_hours' => true,
                'is_active' => true,
                'is_verified' => true,
                'registration_status' => 'approved',
                'capabilities' => ['laboratory','antenatal','immunization','xray'],
                'emergency_keywords' => [],
                'congestion_status' => 'low',
                'subscription_tier' => 'free',
                'subscription_status' => 'active',
            ],
        ];

        foreach ($facilities as $facility) {
            Facility::create($facility);
        }

        tenancy()->end();                             // cleanup
    }
}