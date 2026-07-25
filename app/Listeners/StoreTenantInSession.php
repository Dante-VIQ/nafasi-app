<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;

class StoreTenantInSession
{
    public function handle(Login $event): void
    {
        $user = $event->user;
        if (! $user) {
            return;
        }

        // Safely retrieve tenant_id without directly accessing undefined properties
        $tenantId = data_get($user, 'tenant_id');

        if ($tenantId) {
            session(['tenant_id' => $tenantId]);
        }
    }
}