<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SwitchTenantDatabase
{

public function handle(Request $request, Closure $next)
{
    $user = $request->user();
    if ($user && $user->tenant_id) {
        $database = 'nafasi_tenant_' . $user->tenant_id;
        config(["database.connections.tenant.database" => $database]);
        DB::purge('tenant');
        DB::reconnect('tenant');
        config(['database.default' => 'tenant']);   // <-- make tenant the default
    }
    return $next($request);
}
}
