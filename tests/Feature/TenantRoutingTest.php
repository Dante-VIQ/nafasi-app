<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class TenantRoutingTest extends TestCase
{
    public function test_tenant_dashboard_route_uses_session_based_tenancy_initialization(): void
    {
        $route = app('router')->getRoutes()->getByName('tenant.dashboard');

        $this->assertNotNull($route);
        $this->assertContains('tenant.session', $route->gatherMiddleware());
        $this->assertContains('auth', $route->gatherMiddleware());
        $this->assertContains('2fa', $route->gatherMiddleware());
    }
}
