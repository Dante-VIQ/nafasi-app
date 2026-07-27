<?php

namespace App\Providers;

use App\Http\Responses\LoginViewResponse;
use App\Http\Responses\RegisterViewResponse;
use App\Listeners\StoreTenantInSession;
use Illuminate\Auth\Events\Login;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Contracts\RegisterViewResponse as RegisterViewResponseContract;
use Livewire\Livewire;
use Stancl\Tenancy\Events\TenancyInitialized;
    
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(RegisterViewResponseContract::class, RegisterViewResponse::class);
        $this->app->singleton(\Laravel\Fortify\Contracts\LoginViewResponse::class, LoginViewResponse::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

    //     if (tenancy()->initialized) {
    //     config(['database.default' => 'tenant']);
    // }



    Event::listen(TenancyInitialized::class, function () {
        $tenant = tenant();
        if (!$tenant) return;

        // Derive the database name from the tenant id (prefix + id)
        $database = 'nafasi_' . $tenant->getTenantKey();

        config(['database.connections.tenant.database' => $database]);
        DB::purge('tenant');
        DB::reconnect('tenant');
    });
        // Livewire::setUpdateRoute(function ($handle) {
        //     return Route::middleware(['web', 'auth', '2fa', 'tenant.session'])
        //         ->post('/livewire/update', $handle);
        // });

        Event::listen(Login::class, StoreTenantInSession::class);
        // ============================================
        // RATE LIMITERS (registered in AppServiceProvider or here via boot)
        // ============================================
        RateLimiter::for('login', function ($request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        RateLimiter::for('api', function ($request) {
            return Limit::perMinute(60)->by($request->ip());
        });

        RateLimiter::for('emergency', function ($request) {
            return Limit::perMinute(30)->by($request->ip());
        });
    }
}
