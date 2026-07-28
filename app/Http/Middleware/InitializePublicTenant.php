<?php
// app/Http/Middleware/InitializeTenantForPublic.php

namespace App\Http\Middleware;

use Closure;
use App\Models\Tenant;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;

class InitializePublicTenant
{
    public function handle($request, Closure $next)
    {
        // Try to initialise tenant via domain
        $domainMiddleware = app(InitializeTenancyByDomain::class);

        try {
            return $domainMiddleware->handle($request, $next);
        } catch (\Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedOnDomainException $e) {
            // Domain not recognised → fallback to default tenant
            $tenant = Tenant::where('status', 'active')->first();
            if ($tenant) {
                tenancy()->initialize($tenant);
            }
            return $next($request);
        }
    }
}