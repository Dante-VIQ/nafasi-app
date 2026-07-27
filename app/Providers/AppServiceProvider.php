<?php

namespace App\Providers;

use App\Http\Responses\LoginViewResponse;
use App\Http\Responses\RegisterViewResponse;
use Illuminate\Auth\Events\Login;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Contracts\RegisterViewResponse as RegisterViewResponseContract;
use Laravel\Fortify\Contracts\LoginViewResponse as LoginViewResponseContract;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(RegisterViewResponseContract::class, RegisterViewResponse::class);
        $this->app->singleton(LoginViewResponseContract::class, LoginViewResponse::class);
    }

    public function boot(): void
    {
        // Rate limiters
        RateLimiter::for('login', fn ($request) => Limit::perMinute(5)->by($request->ip()));
        RateLimiter::for('api', fn ($request) => Limit::perMinute(60)->by($request->ip()));
        RateLimiter::for('emergency', fn ($request) => Limit::perMinute(30)->by($request->ip()));

        // Optional: store tenant_id in session on login – only if your User model has tenant_id
        // If not, remove this line entirely.
        // Event::listen(Login::class, StoreTenantInSession::class);
    }
}