<?php
// app/Livewire/Facility/BookingManager.php

namespace App\Livewire\Facility;

use App\Models\Tenant;
use App\Models\Tenant\Appointment;
use App\Models\Tenant\Facility;
use App\Services\SmsService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class BookingManager extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterStatus = '';
    public string $filterDate = '';
    public string $sortField = 'scheduled_at';
    public string $sortDirection = 'asc';

    protected $queryString = ['search', 'filterStatus', 'filterDate'];

    public function ensureTenant(): void
{
    if (!tenancy()->initialized) {
        $host = request()->getHost();
        $parts = explode('.', $host);
        if (count($parts) >= 3) {
            $subdomain = $parts[0]; // e.g., 'kiambu'
            $domain   = $parts[1] . '.' . $parts[2]; // 'nafasi.test'
            $tenant   = Tenant::whereHas('domains', function ($q) use ($subdomain, $domain) {
                $q->where('domain', $subdomain . '.' . $domain);
            })->first();
            if ($tenant) {
                tenancy()->initialize($tenant);
            }
        }
    }
}
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        $this->ensureTenant();
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }


    public function confirmBooking(int $appointmentId): void
    {
         $this->ensureTenant();
        $appointment = Appointment::findOrFail($appointmentId);
        $appointment->update(['status' => 'confirmed']);

        // Send SMS confirmation
        if ($appointment->patient_phone) {
            app(SmsService::class)->send(
                $appointment->patient_phone,
                "✅ Nafasi: Your appointment at {$appointment->facility->name} is confirmed.\n" .
                "Date: {$appointment->scheduled_at->format('d M Y, H:i')}\n" .
                "Reference: " . substr($appointment->uuid, 0, 8)
            );
        }

        session()->flash('message', "Booking confirmed for {$appointment->patient_name}.");
    }

    public function markArrived(int $appointmentId): void
    {
        $this->ensureTenant();
        $appointment = Appointment::findOrFail($appointmentId);
        $appointment->update(['status' => 'arrived']);
        session()->flash('message', "{$appointment->patient_name} marked as arrived.");
    }

    public function markCompleted(int $appointmentId): void
    {
        $this->ensureTenant();
        $appointment = Appointment::findOrFail($appointmentId);
        $appointment->update(['status' => 'completed']);
        session()->flash('message', "Appointment completed for {$appointment->patient_name}.");
    }

    public function cancelBooking(int $appointmentId): void
    {
        $this->ensureTenant();
        $appointment = Appointment::findOrFail($appointmentId);
        $appointment->update(['status' => 'cancelled']);

        if ($appointment->patient_phone) {
            app(SmsService::class)->send(
                $appointment->patient_phone,
                "Your appointment at {$appointment->facility->name} on {$appointment->scheduled_at->format('d M Y, H:i')} has been cancelled.\n" .
                "Book again at nafasi.health"
            );
        }

        session()->flash('message', "Booking cancelled for {$appointment->patient_name}.");
    }

    public function render()
    {
        $facilityId = Auth::user()->facility_id;
        $facility = Facility::find($facilityId);

        $appointments = Appointment::where('facility_id', $facilityId)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('patient_name', 'like', "%{$this->search}%")
                      ->orWhere('patient_phone', 'like', "%{$this->search}%")
                      ->orWhere('reason', 'like', "%{$this->search}%");
                });
            })
            ->when($this->filterStatus, function ($query) {
                $query->where('status', $this->filterStatus);
            })
            ->when($this->filterDate, function ($query) {
                $query->whereDate('scheduled_at', $this->filterDate);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(20);

        // Stats
        $todayCount = Appointment::where('facility_id', $facilityId)
            ->whereDate('scheduled_at', today())->count();
        $pendingCount = Appointment::where('facility_id', $facilityId)
            ->where('status', 'pending')->count();
        $confirmedCount = Appointment::where('facility_id', $facilityId)
            ->where('status', 'confirmed')->count();
        $completedCount = Appointment::where('facility_id', $facilityId)
            ->whereDate('scheduled_at', today())
            ->where('status', 'completed')->count();

        return view('livewire.facility.booking-manager', [
            'appointments' => $appointments,
            'facility' => $facility,
            'todayCount' => $todayCount,
            'pendingCount' => $pendingCount,
            'confirmedCount' => $confirmedCount,
            'completedCount' => $completedCount,
        ])->layout('layouts.app');
    }
}