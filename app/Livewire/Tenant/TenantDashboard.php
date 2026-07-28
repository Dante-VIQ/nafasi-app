<?php
// app/Livewire/Tenant/TenantDashboard.php

namespace App\Livewire\Tenant;

use App\Models\Tenant;
use App\Models\Tenant\Appointment;
use App\Models\Tenant\EmergencyDispatch;
use App\Models\Tenant\Facility;
use App\Models\Tenant\Referral;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class TenantDashboard extends Component
{
    public int $totalFacilities = 0;
    public int $activeFacilities = 0;
    public int $pendingVerifications = 0;
    public int $totalUsers = 0;
    public int $appointmentsToday = 0;
    public int $emergencyDispatchesToday = 0;
    public int $pendingReferrals = 0;
    public array $facilitiesByType = [];
    public array $congestionSummary = [];
    public array $recentActivity = [];

    public function mount()
    {
        $this->ensureTenant();
        $this->loadStats();
        $this->loadRecentActivity();
    }

    public function ensureTenant(): void
{
    if (tenancy()->initialized) {
        return;
    }

    $host = request()->getHost();

    // This component is tenant-only. Never attempt resolution on the
    // central domain — CentralCommunityAlertFeed is what renders there.
    if (in_array($host, config('tenancy.central_domains', []), true)) {
        return;
    }

    $tenant = Tenant::whereHas('domains', fn ($q) => $q->where('domain', $host))->first();

    if ($tenant) {
        tenancy()->initialize($tenant);
    }
}
    protected function loadStats(): void
    {
        // Facilities
        $this->totalFacilities = Facility::count();
        $this->activeFacilities = Facility::where('is_active', true)->count();
        $this->pendingVerifications = Facility::where('registration_status', 'submitted')->count();

        // Facilities by type
        $this->facilitiesByType = Facility::select('facility_type', DB::raw('count(*) as count'))
            ->groupBy('facility_type')
            ->pluck('count', 'facility_type')
            ->toArray();

        // Congestion summary
        $this->congestionSummary = Facility::select('congestion_status', DB::raw('count(*) as count'))
            ->whereNotNull('congestion_status')
            ->groupBy('congestion_status')
            ->pluck('count', 'congestion_status')
            ->toArray();

        // Users
        $this->totalUsers = User::count();

        // Today's appointments
        $this->appointmentsToday = Appointment::whereDate('created_at', today())->count();

        // Today's emergency dispatches
        $this->emergencyDispatchesToday = EmergencyDispatch::whereDate('created_at', today())->count();

        // Pending referrals
        $this->pendingReferrals = Referral::where('status', 'pending')->count();
    }

    protected function loadRecentActivity(): void
    {
        // In production: query actual activity tables
        $this->recentActivity = [];

        // Latest facilities registered
        $latestFacilities = Facility::latest()->take(3)->get();
        foreach ($latestFacilities as $facility) {
            $this->recentActivity[] = [
                'type' => 'facility',
                'message' => "{$facility->name} registered",
                'time' => $facility->created_at->diffForHumans(),
            ];
        }

        // Latest appointments
        $latestAppointments = Appointment::latest()->take(3)->get();
        foreach ($latestAppointments as $appointment) {
            $this->recentActivity[] = [
                'type' => 'booking',
                'message' => "{$appointment->patient_name} booked at {$appointment->facility->name}",
                'time' => $appointment->created_at->diffForHumans(),
            ];
        }
    }

    public function render()
    {
        return view('livewire.tenant.tenant-dashboard')->layout('layouts.app');
    }
}