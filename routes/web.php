<?php

use App\Http\Controllers\SmsWebhookController;
use App\Http\Controllers\UssdController;
use App\Http\Middleware\InitializePublicTenant;
use App\Livewire\AboutPage;
use App\Livewire\Admin\AiDashboard;
use App\Livewire\Admin\PartnerManager;
use App\Livewire\Coordinator\RequestQueue;
use App\Livewire\Crisis\CrisisChat;
use App\Livewire\Emergency\EmergencyDispatchForm;
use App\Livewire\LandingPage;
use App\Livewire\Platform\PlatformDashboard;
use App\Livewire\Platform\TenantManager;
use App\Livewire\PrivacyPolicy;
use App\Livewire\Profile\ProfilePage;
use App\Livewire\Reporting\AnonymousReportForm;
use App\Livewire\Tenant\TenantUserManager;
use App\Livewire\TermsOfService;
use Illuminate\Support\Facades\Route;


// ============================================
// PUBLIC — No Authentication
// ============================================
Route::post('/ussd', [UssdController::class, 'handle'])->name('ussd.handle');
Route::post('/sms/webhook', [SmsWebhookController::class, 'handle'])->name('sms.webhook');

Route::get('/', LandingPage::class)
    ->middleware([InitializePublicTenant::class])
    ->name('home');

Route::get('/about', AboutPage::class)->name('about');
Route::get('/privacy', PrivacyPolicy::class)->name('privacy');
Route::get('/terms', TermsOfService::class)->name('terms');
Route::get('/find-help', LandingPage::class)->name('find-help');
Route::get('/offline', fn () => view('offline'))->name('offline');

// Crisis & Reporting (public, anonymous)
Route::get('/crisis-chat', CrisisChat::class)->name('crisis.chat');
Route::get('/report/anonymous', AnonymousReportForm::class)->name('report.anonymous');
Route::get('/emergency', EmergencyDispatchForm::class)->name('emergency.dispatch');

// ============================================
// CENTRAL ROUTES (Platform Owner, Super Admin, Verification Partner)
// These routes do NOT use tenant switching.
// ============================================
// foreach (config('tenancy.central_domains') as $domain) {
//     Route::domain($domain)->middleware(['auth'])->group(function () {
Route::middleware(['auth'])->group(function () {

    // Profile (available to all authenticated users)
    Route::get('/profile', ProfilePage::class)->name('profile');
    Route::get('/profile/security', ProfilePage::class)->name('profile.security');

// Platform Owner & Super Admin
Route::middleware(['auth', '2fa', 'role:platform-owner,super-admin'])
    ->prefix('platform')
    ->name('platform.')
    ->group(function () {
        // Dashboard
        Route::get('/dashboard', \App\Livewire\Platform\PlatformDashboard::class)->name('dashboard');

        // Tenant Management (create, list, manage tenants)
        Route::get('/tenants', TenantUserManager::class)->name('tenants');
        Route::get('/tenants/create', TenantManager::class)->name('tenants.create');
    });

    // Admin
    Route::middleware(['role:platform-owner,super-admin'])
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {
            Route::get('/ai-dashboard', AiDashboard::class)->name('ai.dashboard');
            Route::get('/partners', PartnerManager::class)->name('partners');
        });

    // Verification Partner
    Route::middleware(['role:coordinator,platform-owner,super-admin,tenant-admin'])
        ->prefix('coordinator')
        ->name('coordinator.')
        ->group(function () {
            Route::get('/dashboard', RequestQueue::class)->name('dashboard');
        });
});

//     });
// }
require __DIR__.'/console.php';
// require __DIR__.'/tenant.php';
require __DIR__.'/auth.php';
