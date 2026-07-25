<?php
// database/seeders/RolesAndPermissionsSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // ============================================
        // PERMISSIONS
        // ============================================
        $permissions = [
            // Platform Management
            'platform.manage',
            'platform.analytics',
            'platform.settings',

            // Tenant Management
            'tenant.create',
            'tenant.update',
            'tenant.delete',
            'tenant.suspend',
            'tenant.analytics',
            'tenant.users.manage',
            'tenant.settings.manage',

            // Facility Management
            'facility.create',
            'facility.update',
            'facility.delete',
            'facility.verify',
            'facility.suspend',
            'facility.profile.update',
            'facility.congestion.update',
            'facility.analytics',

            // Referral Management
            'referral.create',
            'referral.view',
            'referral.accept',
            'referral.reject',
            'referral.redirect',

            // Patient Data (Facility DB)
            'patients.view',
            'patients.create',
            'patients.update',
            'patients.delete',
            'patients.export',

            // User Management
            'users.create',
            'users.update',
            'users.delete',
            'users.roles.assign',
            'users.suspend',

            // Coordinator
            'coordinator.access',
            'coordinator.requests.handle',
            'coordinator.services.dispatch',

            // Bookings
            'bookings.view',
            'bookings.create',
            'bookings.update',
            'bookings.cancel',

            // Reports
            'reports.view',
            'reports.export',

            // API
            'api.access',
            'api.keys.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // ============================================
        // ROLES
        // ============================================

        // 1. Platform Owner (God Mode)
        $platformOwner = Role::create(['name' => 'platform-owner']);
        $platformOwner->givePermissionTo(Permission::all());

        // 2. Super Admin
        $superAdmin = Role::create(['name' => 'super-admin']);
        $superAdmin->givePermissionTo([
            'platform.manage',
            'platform.analytics',
            'platform.settings',
            'tenant.create',
            'tenant.update',
            'tenant.suspend',
            'tenant.analytics',
            'tenant.users.manage',
            'tenant.settings.manage',
            'facility.create',
            'facility.update',
            'facility.delete',
            'facility.verify',
            'facility.suspend',
            'facility.analytics',
            'users.create',
            'users.update',
            'users.delete',
            'users.roles.assign',
            'users.suspend',
            'reports.view',
            'reports.export',
            'api.access',
            'api.keys.manage',
        ]);

        // 3. Verification Partner
        $verificationPartner = Role::create(['name' => 'verification-partner']);
        $verificationPartner->givePermissionTo([
            'facility.verify',
            'facility.suspend',
            'facility.analytics',
            'reports.view',
        ]);

        // 4. Tenant Admin (County/Organization)
        $tenantAdmin = Role::create(['name' => 'tenant-admin']);
        $tenantAdmin->givePermissionTo([
            'tenant.analytics',
            'tenant.users.manage',
            'tenant.settings.manage',
            'facility.create',
            'facility.update',
            'facility.suspend',
            'facility.analytics',
            'users.create',
            'users.update',
            'users.suspend',
            'reports.view',
            'reports.export',
            'api.access',
        ]);

        // 5. Facility Admin
        $facilityAdmin = Role::create(['name' => 'facility-admin']);
        $facilityAdmin->givePermissionTo([
            'facility.profile.update',
            'facility.congestion.update',
            'facility.analytics',
            'referral.create',
            'referral.view',
            'referral.accept',
            'referral.reject',
            'referral.redirect',
            'patients.view',
            'patients.create',
            'patients.update',
            'patients.export',
            'bookings.view',
            'bookings.create',
            'bookings.update',
            'bookings.cancel',
            'reports.view',
        ]);

        // 6. Facility Staff
        $facilityStaff = Role::create(['name' => 'facility-staff']);
        $facilityStaff->givePermissionTo([
            'facility.congestion.update',
            'referral.view',
            'patients.view',
            'bookings.view',
            'bookings.create',
        ]);

        // 7. Coordinator
        $coordinator = Role::create(['name' => 'coordinator']);
        $coordinator->givePermissionTo([
            'coordinator.access',
            'coordinator.requests.handle',
            'coordinator.services.dispatch',
            'referral.create',
            'referral.view',
        ]);

        // 8. Public User
        $publicUser = Role::create(['name' => 'public-user']);
        $publicUser->givePermissionTo([
            'bookings.create',
            'bookings.view',
            'bookings.cancel',
        ]);
    }
}