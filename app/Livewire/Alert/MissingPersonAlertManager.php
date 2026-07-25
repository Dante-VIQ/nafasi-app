<?php

namespace App\Livewire\Alert;

use App\Models\Tenant;
use App\Models\Tenant\MissingPersonAlert;
use App\Models\Tenant\SightingReport;
use App\Services\PhotoExifStripper; // We'll create this next
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class MissingPersonAlertManager extends Component
{
    use WithFileUploads;

    // Form fields
    public string $name = '';
    public string $age_group = '';
    public string $gender = '';
    public string $description = '';
    public string $last_seen_location = '';
    public string $suspect_description = '';
    public $photo = null;
    public $suspect_photo = null;
    public string $contact_phone = '';

    public bool $showCreateForm = false;
    public ?MissingPersonAlert $editingAlert = null;
    public array $alerts = [];
    public array $sightings = [];
    public ?MissingPersonAlert $selectedAlert = null;

    protected $rules = [
        'name'                => 'required|string|max:255',
        'age_group'           => 'nullable|in:infant,child,adult,elderly',
        'gender'              => 'nullable|in:male,female,other',
        'description'         => 'required|string|min:10|max:2000',
        'last_seen_location'  => 'nullable|string|max:500',
        'suspect_description' => 'nullable|string|max:2000',
        'photo'               => 'nullable|image|max:5120',
        'suspect_photo'       => 'nullable|image|max:5120',
        'contact_phone'       => 'nullable|string|max:20',
    ];

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
    public function mount()
    {
        $this->ensureTenant();
        $this->loadAlerts();
    }

    public function loadAlerts()
    {
        $this->ensureTenant();
        $this->alerts = MissingPersonAlert::with('sightingReports')
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get()
            ->toArray();
    }

    public function createAlert()
    {
        $this->ensureTenant();
        $this->validate();

        // Strip EXIF data from uploaded photos
        $photoPath = null;
        $suspectPhotoPath = null;

        if ($this->photo) {
            $photoPath = $this->photo->store('alerts', 'public');
            // In production, run PhotoExifStripper on the stored file
        }
        if ($this->suspect_photo) {
            $suspectPhotoPath = $this->suspect_photo->store('alerts', 'public');
        }

        MissingPersonAlert::create([
            'name'                 => $this->name,
            'age_group'            => $this->age_group ?: null,
            'gender'               => $this->gender ?: null,
            'description'          => $this->description,
            'last_seen_location'   => $this->last_seen_location ?: null,
            'suspect_description'  => $this->suspect_description ?: null,
            'photo_path'           => $photoPath,
            'suspect_photo_path'   => $suspectPhotoPath,
            'contact_phone'        => $this->contact_phone ?: null,
            'reported_by'          => Auth::id(),
            'status'               => 'active',
        ]);

        $this->reset(['name','age_group','gender','description','last_seen_location',
                       'suspect_description','photo','suspect_photo','contact_phone']);
        $this->showCreateForm = false;
        session()->flash('message', 'Alert created successfully.');
        $this->loadAlerts();
    }

    public function closeAlert(int $alertId)
    {
        $this->ensureTenant();
        $alert = MissingPersonAlert::findOrFail($alertId);
        $alert->update([
            'status'   => 'found',
            'found_at' => now(),
        ]);
        session()->flash('message', 'Alert closed – person found.');
        $this->loadAlerts();
    }

    public function viewSightings(int $alertId)
    {
        $this->ensureTenant();
        $this->selectedAlert = MissingPersonAlert::with('sightingReports')->findOrFail($alertId);
        $this->sightings = $this->selectedAlert->sightingReports->toArray();
    }

    public function render()
    {
        return view('livewire.alert.missing-person-alert-manager')
            ->layout('layouts.app');
    }
}