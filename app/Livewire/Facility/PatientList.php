<?php
// app/Livewire/Facility/PatientList.php

namespace App\Livewire\Facility;

use App\Models\Tenant;
use App\Models\Tenant\Appointment;
use App\Models\Tenant\Facility;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class PatientList extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterStatus = '';
    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';
    public ?int $selectedPatientId = null;
    public array $selectedPatient = [];
    public array $patientAppointments = [];
    public bool $showPatientModal = false;

    protected $queryString = ['search', 'filterStatus'];

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

    public function viewPatient(int $patientId): void
    {
        $this->ensureTenant();
        $facilityId = Auth::user()->facility_id;
        
        $this->selectedPatient = Appointment::where('facility_id', $facilityId)
            ->where('id', $patientId)
            ->first()
            ->toArray();
            
        $this->patientAppointments = Appointment::where('facility_id', $facilityId)
            ->where('patient_phone', $this->selectedPatient['patient_phone'] ?? '')
            ->orderBy('scheduled_at', 'desc')
            ->take(10)
            ->get()
            ->toArray();
            
        $this->showPatientModal = true;
    }

    public function closeModal(): void
    {
        $this->ensureTenant();
        $this->showPatientModal = false;
        $this->selectedPatient = [];
        $this->patientAppointments = [];
    }

    public function render()
    {
        $facilityId = Auth::user()->facility_id;

        $patients = Appointment::where('facility_id', $facilityId)
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
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(20);

        // Get unique patients (by phone or name) for summary stats
        $totalUniquePatients = Appointment::where('facility_id', $facilityId)
            ->distinct('patient_phone')
            ->count('patient_phone');
            
        $todayPatients = Appointment::where('facility_id', $facilityId)
            ->whereDate('created_at', today())
            ->count();
            
        $pendingPatients = Appointment::where('facility_id', $facilityId)
            ->where('status', 'pending')
            ->count();

        return view('livewire.facility.patient-list', [
            'patients' => $patients,
            'totalUniquePatients' => $totalUniquePatients,
            'todayPatients' => $todayPatients,
            'pendingPatients' => $pendingPatients,
        ])->layout('layouts.app');
    }
}