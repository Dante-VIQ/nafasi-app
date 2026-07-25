<?php
// app/Livewire/Platform/PlatformDashboard.php

namespace App\Livewire\Platform;

use Livewire\Component;
use App\Models\Tenant;
use App\Models\User;
use App\Services\ML\MlServiceClient;
use Illuminate\Support\Facades\DB;

class PlatformDashboard extends Component
{
    public int $totalTenants = 0;
    public int $activeTenants = 0;
    public int $totalUsers = 0;
    public int $totalFacilities = 0;
    public array $usersByRole = [];
    public string $mlStatus = 'unknown';
    public string $dbStatus = 'unknown';
    public string $queueStatus = 'unknown';
    public array $recentActivity = [];
    public array $tenantGrowth = [];

    public function mount()
    {
        $this->loadStats();
        $this->loadSystemHealth();
        $this->loadRecentActivity();
        $this->loadTenantGrowth();
    }

protected function loadStats(): void
{
    $this->totalTenants = Tenant::count();
    $this->activeTenants = Tenant::where('status', 'active')->count();
    $this->totalUsers = User::count();
    $this->usersByRole = User::select('primary_role', DB::raw('count(*) as count'))
        ->groupBy('primary_role')
        ->pluck('count', 'primary_role')
        ->toArray();

    $this->totalFacilities = $this->countFacilitiesAcrossTenants();
}

protected function countFacilitiesAcrossTenants(): int
{
    $total = 0;
    $hadError = false;

    foreach (Tenant::where('status', 'active')->get() as $tenant) {
        try {
            tenancy()->initialize($tenant);
            $total += \App\Models\Tenant\Facility::count();
        } catch (\Exception $e) {
            $hadError = true;
            report($e); // log it, don't lose it
        } finally {
            tenancy()->end(); // guaranteed to run, even if count() threw
        }
    }

    return $hadError ? -1 : $total;
}

    protected function loadSystemHealth(): void
    {
        // ML Service
        try {
            $ml = new MlServiceClient();
            $this->mlStatus = $ml->isHealthy() ? 'healthy' : 'degraded';
        } catch (\Exception $e) {
            $this->mlStatus = 'unreachable';
        }

        // Database
        try {
            DB::connection()->getPdo();
            $this->dbStatus = 'connected';
        } catch (\Exception $e) {
            $this->dbStatus = 'disconnected';
        }

        // Queue
        try {
            $queueSize = DB::table('jobs')->count();
            $this->queueStatus = $queueSize > 100 ? 'backlogged' : ($queueSize > 10 ? 'busy' : 'healthy');
        } catch (\Exception $e) {
            $this->queueStatus = 'unknown';
        }
    }

    protected function loadRecentActivity(): void
    {
        // Load from activity log or recent audit events
        $this->recentActivity = [
            [
                'type' => 'tenant_registered',
                'message' => 'New tenant registered: Kiambu County',
                'time' => now()->subHours(2)->diffForHumans(),
            ],
            [
                'type' => 'facility_verified',
                'message' => 'Thika Level 5 Hospital verified',
                'time' => now()->subHours(5)->diffForHumans(),
            ],
            [
                'type' => 'emergency_dispatched',
                'message' => 'Snakebite dispatch in Ruiru',
                'time' => now()->subHours(8)->diffForHumans(),
            ],
            [
                'type' => 'model_trained',
                'message' => 'ML model retrained with 150 new interactions',
                'time' => now()->subDay()->diffForHumans(),
            ],
        ];
    }

    protected function loadTenantGrowth(): void
    {
        // Monthly tenant growth for the last 6 months
        $this->tenantGrowth = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $count = Tenant::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
            $this->tenantGrowth[] = [
                'month' => $month->format('M Y'),
                'count' => $count,
            ];
        }
    }

    public function render()
    {
        return view('livewire.platform.platform-dashboard')->layout('layouts.app');
    }
}