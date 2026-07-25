<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;

class InitializeTenancyFromSession
{
    public function handle(Request $request, Closure $next)
    {
        $tenant = null;

        if ($tenantId = $request->session()->get('tenant_id')) {
            $tenant = Tenant::query()->whereKey($tenantId)->first();
        }

        if (! $tenant && $request->user()?->tenant_id) {
            $tenant = Tenant::query()->whereKey($request->user()->tenant_id)->first();
        }

        if ($tenant) {
            tenancy()->initialize($tenant);
        }

        return $next($request);
    }
}