<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InitializeUserTenant
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if ($user && $user->tenant_id) {
            $tenant = Tenant::query()->whereKey($user->tenant_id)->first();

            if ($tenant) {
                $database = config('tenancy.database.prefix') . $tenant->getTenantKey() . config('tenancy.database.suffix');
                config(['database.connections.tenant.database' => $database]);
                DB::purge('tenant');
                DB::reconnect('tenant');
                config(['database.default' => 'tenant']);
            } else {
                config(['database.default' => 'mysql']);
            }
        } else {
            config(['database.default' => 'mysql']);
        }

        return $next($request);
    }
}
