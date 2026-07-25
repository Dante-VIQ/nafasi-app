<?php

use App\Http\Middleware\CheckPermission;
use App\Http\Middleware\CheckRole;
use App\Http\Middleware\InitializePublicTenant;
use App\Http\Middleware\InitializeTenancyFromSession;
use App\Http\Middleware\InitializeUserTenant;
use App\Http\Middleware\RequireTwoFactorAuth;
use App\Http\Middleware\SanitizeInput;
use App\Http\Middleware\SecurityHeaders;
use App\Http\Middleware\SwitchTenantDatabase;
use App\Providers\SmsServiceProvider;
use App\Providers\TenancyServiceProvider;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;



return Application::configure(basePath: dirname(__DIR__))
->withRouting(
    web: __DIR__.'/../routes/web.php',
    api: __DIR__.'/../routes/api.php',
    commands: __DIR__.'/../routes/console.php',
    health: '/up',
)
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            '2fa' => RequireTwoFactorAuth::class,
            // 'user.tenant' => InitializeUserTenant::class,
            // 'switch.tenant' => SwitchTenantDatabase::class,
            'tenant.session' => InitializeTenancyFromSession::class,
            'role' => CheckRole::class,
            'permission' => CheckPermission::class,
            'public.tenant' => InitializePublicTenant::class,

        ]);

        $middleware->web(append: [
            // SecurityHeaders::class,     // Security headers on every response
            // SanitizeInput::class,       // Strip XSS/SQL injection from all input
            EncryptCookies::class,
            VerifyCsrfToken::class,
            // SwitchTenantDatabase::class,
        ]);
        $middleware->validateCsrfTokens(except: [
            'ussd',
            'ussd/*',
            'sms/webhook',
            'api/*',
        ]);

    })
    ->withProviders([
        TenancyServiceProvider::class,
        SmsServiceProvider::class,
    ])

    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
