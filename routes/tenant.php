<?php

declare(strict_types=1);

use App\Livewire\Facility\BookingManager;
use App\Livewire\Facility\CongestionButton;
use App\Livewire\Facility\Dashboard;
use App\Livewire\Facility\FacilityList;
use App\Livewire\Facility\FacilityProfileEditor;
use App\Livewire\Facility\FacilityStaffManager;
use App\Livewire\Facility\PatientList;
use App\Livewire\Facility\PaymentPage;
use App\Livewire\Facility\RegistrationWizard;
use App\Livewire\Referral\ReferralManager;
use App\Livewire\Tenant\TenantDashboard;
use App\Livewire\Tenant\TenantUserManager;
use App\Livewire\Verification\FacilityReviewDetail;
use App\Livewire\Verification\FacilityReviewQueue;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {

    Route::middleware(['auth', '2fa'])->group(function () {
        Route::get('/dashboard', function () {
            $user = auth()->user();

            // Platform & super admin → central routes (work anywhere)
            if ($user->isPlatformOwner() || $user->hasRole('super-admin')) {
                return redirect('/platform/dashboard');
            }
            if ($user->isVerificationPartner()) {
                return redirect('/verification/queue');
            }

            // Tenant‑specific routes → use relative paths (keep current domain)
            if ($user->isTenantAdmin()) {
                return redirect('/tenant/dashboard');
            }
            if ($user->isFacilityStaff()) {
                return redirect('/facility/dashboard');
            }
            if ($user->isCoordinator()) {
                return redirect('/coordinator/dashboard');
            }

            return redirect('/');
        })->name('dashboard');

        // Profile and other central routes…
    });

    Route::domain('{subdomain}.nafasi.test')->middleware(['auth', '2fa', 'tenant.session'])->group(function () {
        // dd(\App\Models\User::all());

        // Tenant Admin routes
        Route::middleware([
            'auth',
            '2fa',
            'role:tenant-admin,platform-owner,super-admin',
            InitializeTenancyByDomain::class,
        ])->prefix('tenant')
            ->name('tenant.')
            ->group(function () {
                Route::get('/dashboard', TenantDashboard::class)->name('dashboard');
                Route::get('/facilities', FacilityList::class)->name('facilities');
                Route::get('/users', TenantUserManager::class)->name('users');
                Route::get('/facilities/register', RegistrationWizard::class)
                    ->name('facilities.register');
                    
            });

        // Facility Admin & Staff routes
        Route::middleware(['role:facility-admin,facility-staff,tenant-admin,platform-owner,super-admin',
            InitializeTenancyByDomain::class,
        ])
            ->prefix('facility')
            ->name('facility.')
            ->group(function () {
                Route::get('/dashboard', Dashboard::class)->name('dashboard');

                Route::middleware(['role:facility-admin,tenant-admin,platform-owner,super-admin',
                ])->group(function () {
                    Route::get('/profile/edit/{facility}', FacilityProfileEditor::class)
                        ->name('profile.edit');
                    Route::get('/staff', FacilityStaffManager::class)->name('staff');
                    
                    Route::get('/alerts/manage', \App\Livewire\Alert\MissingPersonAlertManager::class)
    ->name('alerts.manage');

                });

                Route::get('/congestion', CongestionButton::class)->name('congestion');
                Route::get('/patients', PatientList::class)->name('patients');
                Route::get('/bookings', BookingManager::class)->name('bookings');
                Route::get('/referrals', ReferralManager::class)->name('referrals');
                Route::get('/payment', PaymentPage::class)->name('payment');
            });

        Route::middleware(['role:verification-partner,platform-owner,super-admin',
            InitializeTenancyByDomain::class,
        ])
            ->prefix('verification')
            ->name('verification.')
            ->group(function () {
                Route::get('/queue', FacilityReviewQueue::class)->name('queue');
                Route::get('/review/{facilityId}', FacilityReviewDetail::class)->name('review');
            });

        // Coordinator routes
        Route::middleware(['auth', '2fa', 'role:coordinator,tenant-admin,platform-owner,super-admin',
            InitializeTenancyByDomain::class,
        ])->group(function () {
    Route::get('/alerts/manage', \App\Livewire\Alert\MissingPersonAlertManager::class)->name('alerts.manage');
});
    });

});
