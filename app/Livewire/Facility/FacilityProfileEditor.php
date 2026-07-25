<?php
// app/Livewire/Facility/FacilityProfileEditor.php

namespace App\Livewire\Facility;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Tenant\Facility;
use Illuminate\Support\Facades\Auth;

class FacilityProfileEditor extends Component
{
    use WithFileUploads;

    public Facility $facility;
    public bool $saved = false;

    // Editable fields
    public string $name = '';
    public string $phone = '';
    public string $emergency_phone = '';
    public string $email = '';
    public string $address = '';
    public string $landmark = '';
    public string $city = '';
    public string $county = '';
    public string $public_description = '';
    public array $capabilities = [];
    public array $emergency_keywords = [];
    public string $emergencyKeywordsInput = '';
    public array $exclusion_keywords = [];
    public string $exclusionKeywordsInput = '';
    public string $emergency_definition = '';
    public bool $is_24_hours = false;
    public bool $accepts_referrals = false;
    public array $languages = [];
    public $license_document = null;

public function mount($facility = null): void
{
    if ($facility) {
        // Tenant admin editing a specific facility
        $this->facility = Facility::findOrFail($facility);
    } else {
        // Facility admin editing their own facility
        $facilityId = Auth::user()->facility_id;
        $this->facility = Facility::findOrFail($facilityId);
    }
    $this->loadFacilityData();
}

    protected function loadFacilityData(): void
    {
        $this->name = $this->facility->name;
        $this->phone = $this->facility->phone;
        $this->emergency_phone = $this->facility->emergency_phone ?? '';
        $this->email = $this->facility->email ?? '';
        $this->address = $this->facility->address;
        $this->landmark = $this->facility->landmark ?? '';
        $this->city = $this->facility->city ?? '';
        $this->county = $this->facility->county ?? '';
        $this->public_description = $this->facility->public_description ?? '';
        $this->capabilities = $this->facility->capabilities ?? [];
        $this->emergency_keywords = $this->facility->emergency_keywords ?? [];
        $this->emergencyKeywordsInput = implode(', ', $this->emergency_keywords);
        $this->exclusion_keywords = $this->facility->exclusion_keywords ?? [];
        $this->exclusionKeywordsInput = implode(', ', $this->exclusion_keywords);
        $this->emergency_definition = $this->facility->emergency_definition ?? '';
        $this->is_24_hours = $this->facility->is_24_hours;
        $this->accepts_referrals = $this->facility->accepts_referrals;
        $this->languages = $this->facility->languages ?? [];
    }

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'emergency_phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'required|string|max:500',
            'landmark' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'county' => 'nullable|string|max:100',
            'public_description' => 'nullable|string|max:500',
            'emergency_definition' => 'required|string|max:2000',
            'license_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ];
    }

    public function saveProfile(): void
    {
        $this->validate();

        // Process keywords
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

        // Upload license if provided
        $licensePath = $this->facility->license_document_path;
        if ($this->license_document) {
            $licensePath = $this->license_document->store('facility-licenses', 'public');
        }

        $this->facility->update([
            'name' => $this->name,
            'phone' => $this->phone,
            'emergency_phone' => $this->emergency_phone ?: null,
            'email' => $this->email ?: null,
            'address' => $this->address,
            'landmark' => $this->landmark ?: null,
            'city' => $this->city ?: null,
            'county' => $this->county ?: null,
            'public_description' => $this->public_description ?: null,
            'capabilities' => $this->capabilities,
            'emergency_keywords' => $this->emergency_keywords,
            'exclusion_keywords' => $this->exclusion_keywords,
            'emergency_definition' => $this->emergency_definition,
            'is_24_hours' => $this->is_24_hours,
            'accepts_referrals' => $this->accepts_referrals,
            'languages' => $this->languages,
            'license_document_path' => $licensePath,
        ]);

        $this->saved = true;
        session()->flash('message', 'Facility profile updated successfully.');
    }

    public function render()
    {
        return view('livewire.facility.facility-profile-editor', [
            'availableCapabilities' => Facility::availableCapabilities(),
        ])->layout('layouts.app');
    }
}