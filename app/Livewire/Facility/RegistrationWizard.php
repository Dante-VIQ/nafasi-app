<?php
// app/Livewire/Facility/RegistrationWizard.php

namespace App\Livewire\Facility;

use App\Models\Tenant\Facility;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class RegistrationWizard extends Component
{
    use WithFileUploads;

    public int $step = 1;
    public const TOTAL_STEPS = 7;

    // Step 1: Basic Info
    public string $name = '';
    public string $facility_type = '';
    public string $phone = '';
    public string $email = '';
    public string $emergency_phone = '';
    public string $website = '';

    // Step 2: Location
    public string $address = '';
    public string $landmark = '';
    public string $city = '';
    public string $county = '';
    public string $postal_code = '';
    public ?float $latitude = null;
    public ?float $longitude = null;

    // Step 3: Capabilities
    public array $capabilities = [];
    public string $description = '';
    public string $public_description = '';

    // Step 4: Emergency Definition
    public string $emergencyKeywordsInput = '';
    public array $emergency_keywords = [];
    public string $exclusionKeywordsInput = '';
    public array $exclusion_keywords = [];
    public string $emergency_level = 'standard';
    public string $emergency_definition = '';
    public string $exclusion_definition = '';
    public ?int $overflow_facility_id = null;

    // Step 5: Dispatch
    public bool $can_dispatch_to_patient = false;
    public string $dispatch_service_type = '';
    public string $typical_response_time = '';
    public ?int $dispatch_radius_km = null;

    // Step 6: Operations
    public array $operating_hours = [];
    public bool $is_24_hours = false;
    public array $languages = ['sw', 'en'];
    public array $accepted_payment = [];

    // Step 7: Verification
    public $license_document = null;
    public ?string $license_expiry = null;
    public bool $registrationComplete = false;
    public ?Facility $registeredFacility = null;

    // Health system level
    public ?int $health_system_level = null;
    public bool $accepts_referrals = false;

protected function rules(): array
{
    return match($this->step) {
        1 => [
            'name' => 'required|string|max:255',
            'facility_type' => 'required|string',
            'phone' => 'required|string|max:20',
        ],
        2 => [
            'address' => 'required|string|max:500',
        ],
        3 => [
            'capabilities' => 'nullable|array',
        ],
        4 => [
            'emergency_level' => 'required|string',
            'emergency_definition' => 'required|string|max:2000',
        ],
        7 => [
            'license_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ],
        default => [],
    };
}

    protected $messages = [
        'name.required' => 'Facility name is required.',
        'facility_type.required' => 'Please select your facility type.',
        'phone.required' => 'A contact phone number is required.',
        'address.required' => 'Facility address is required.',
        'emergency_definition.required' => 'Please describe what situations should be treated as emergencies.',
    ];

public function nextStep(): void
{
    if (!empty($this->rules())) {
        $this->validate();
    }
    
    if ($this->step === 4) {
        $this->processKeywords();
    }
    
    if ($this->step < self::TOTAL_STEPS) {
        $this->step++;
    }
}

    public function previousStep(): void
    {
        if ($this->step > 1) {
            $this->step--;
        }
    }

    protected function processKeywords(): void
    {
        $this->emergency_keywords = collect(explode(',', $this->emergencyKeywordsInput))
            ->map(fn($kw) => strtolower(trim($kw)))
            ->filter(fn($kw) => !empty($kw))
            ->unique()
            ->values()
            ->toArray();

        $this->exclusion_keywords = collect(explode(',', $this->exclusionKeywordsInput))
            ->map(fn($kw) => strtolower(trim($kw)))
            ->filter(fn($kw) => !empty($kw))
            ->unique()
            ->values()
            ->toArray();
    }

    public function requestLocation(): void
    {
        $this->dispatch('request-geolocation');
    }

    public function setLocation(float $lat, float $lng): void
    {
        $this->latitude = $lat;
        $this->longitude = $lng;
    }

    public function submit(): void
    {
          $this->validate();

    // Ensure we are connected to the tenant database
    $user = auth()->user();
    if ($user && $user->tenant_id) {
        $database = 'nafasi_tenant_' . $user->tenant_id;
        config(["database.connections.tenant.database" => $database]);
        DB::purge('tenant');
        DB::reconnect('tenant');
    }


        $licensePath = null;
        if ($this->license_document) {
            $licensePath = $this->license_document->store('facility-licenses', 'public');
        }

        $this->registeredFacility = Facility::create([
            'name' => $this->name,
            'facility_type' => $this->facility_type,
            'phone' => $this->phone,
            'email' => $this->email ?: null,
            'emergency_phone' => $this->emergency_phone ?: null,
            'website' => $this->website ?: null,
            'address' => $this->address,
            'landmark' => $this->landmark ?: null,
            'city' => $this->city ?: null,
            'county' => $this->county ?: null,
            'postal_code' => $this->postal_code ?: null,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'capabilities' => $this->capabilities ?: [],
            'description' => $this->description ?: null,
            'public_description' => $this->public_description ?: null,
            'emergency_keywords' => $this->emergency_keywords,
            'exclusion_keywords' => $this->exclusion_keywords,
            'emergency_level' => $this->emergency_level,
            'emergency_definition' => $this->emergency_definition,
            'exclusion_definition' => $this->exclusion_definition ?: null,
            'overflow_facility_id' => $this->overflow_facility_id,
            'can_dispatch_to_patient' => $this->can_dispatch_to_patient,
            'dispatch_service_type' => $this->dispatch_service_type ?: null,
            'typical_response_time' => $this->typical_response_time ?: null,
            'dispatch_radius_km' => $this->dispatch_radius_km,
            'operating_hours' => $this->operating_hours ?: null,
            'is_24_hours' => $this->is_24_hours,
            'languages' => $this->languages,
            'accepted_payment' => $this->accepted_payment ?: [],
            'health_system_level' => $this->health_system_level,
            'accepts_referrals' => $this->accepts_referrals,
            'license_document_path' => $licensePath,
            'license_expiry' => $this->license_expiry ?: null,
            'registration_status' => 'submitted',
            'is_verified' => false,
            'is_active' => true,
            'is_public' => true,
            'subscription_tier' => 'free',
            'subscription_status' => 'trialing',
            'trial_ends_at' => now()->addDays(30),
            'created_by' => auth()->id(),
        ]);

        $this->registrationComplete = true;
    }

    public function getStepLabel(): string
    {
        return match($this->step) {
            1 => 'Basic Information',
            2 => 'Location',
            3 => 'Services & Capabilities',
            4 => 'Emergency Definition',
            5 => 'Dispatch Capability',
            6 => 'Operations',
            7 => 'Verification',
            default => '',
        };
    }

    public function render()
    {
        return view('livewire.facility.registration-wizard', [
            'facilityTypes' => Facility::facilityTypes(),
            'availableCapabilities' => Facility::availableCapabilities(),
            'emergencyLevels' => Facility::emergencyLevels(),
            'dispatchServiceTypes' => Facility::dispatchServiceTypes(),
        ])->layout('layouts.app');
    }
}