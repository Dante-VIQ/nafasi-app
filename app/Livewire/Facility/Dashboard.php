<?php

namespace App\Livewire\Facility;

use App\Models\Tenant\Appointment;
use App\Models\Tenant\Facility;
use App\Models\Tenant\FacilityCongestionLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class Dashboard extends Component
{
    public Facility $facility;

    public string $congestionStatus;

    public string $lastUpdated;

    public int $totalPatientsToday = 0;

    public int $pendingReferrals = 0;

    public int $activeBookings = 0;

    public array $congestionHistory = [];

    public bool $isOpen;

    public $upcomingBookings = [];

//     public function boot()
// {
//     $user = Auth::user();
//     if ($user && $user->tenant_id) {
//         $database = 'nafasi_tenant_' . $user->tenant_id;
//         config(["database.connections.tenant.database" => $database]);
//         DB::purge('tenant');
//         DB::reconnect('tenant');
//         config(['database.default' => 'tenant']);
//     }
// }

public function mount(): void
{
    $user = Auth::user();

    if (! $user) {
        abort(403);
    }

    $this->facility = Facility::query()
        ->whereKey($user->facility_id)
        ->firstOrFail();

    $this->congestionStatus = $this->facility->congestion_status ?? 'unknown';
    $this->lastUpdated = $this->facility->congestion_updated_at
        ? $this->facility->congestion_updated_at->diffForHumans()
        : 'Never';
    // $this->isOpen = $this->facility->isOpenNow();

    $this->loadStats();

    $this->upcomingBookings = Appointment::query()
        ->where('facility_id', '=', $this->facility->id)
        ->where('scheduled_at', '>=', now())
        ->orderBy('scheduled_at', 'asc')
        ->take(10)
        ->get()
        ->toArray();

    Log::info('Dashboard mount', [
    'default_connection' => DB::getDefaultConnection(),
    'tenant_db' => config('database.connections.tenant.database'),
]);
}

    
    protected function loadStats(): void
    {
        // Load congestion history (last 24 entries)
        $this->congestionHistory = FacilityCongestionLog::query()
            ->where('facility_id', $this->facility->id)
            ->latest()
            ->take(24)
            ->get()
            ->reverse()
            ->values()
            ->toArray();

        // Count today's patients (from facility's isolated database)
        // $this->totalPatientsToday = DB::connection("facility_{$this->facility->id}")
        //     ->table('visits')
        //     ->whereDate('created_at', today())
        //     ->count();
    }

    public function updateCongestion(string $status): void
    {
        $validStatuses = ['low', 'moderate', 'high', 'at_capacity'];

        if (! in_array($status, $validStatuses)) {
            return;
        }

        $this->facility->update([
            'congestion_status' => $status,
            'congestion_updated_at' => now(),
            'routing_priority' => $this->calculatePriority($status),
        ]);

        FacilityCongestionLog::create([
            'facility_id' => $this->facility->id,
            'status' => $status,
            'source' => 'manual',
            'reported_by' => Auth::id(),
        ]);

        $this->congestionStatus = $status;
        $this->lastUpdated = 'just now';

        $this->loadStats();

        session()->flash('message', 'Congestion updated to '.ucfirst($status).'.');
    }

    protected function calculatePriority(string $status): int
    {
        return match ($status) {
            'low' => 10,
            'moderate' => 5,
            'high' => 1,
            'at_capacity' => -10,
            default => 0,
        };
    }

    public function render()
    {
        return view('livewire.facility.dashboard');
    }
}
