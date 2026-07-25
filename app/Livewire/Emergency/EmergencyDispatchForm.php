<?php
// app/Livewire/Emergency/EmergencyDispatchForm.php

namespace App\Livewire\Emergency;

use Livewire\Component;
use App\Services\Emergency\EmergencyDispatcher;
use App\Models\Tenant\EmergencyDispatch;
use App\Notifications\EmergencyDispatchNotification;

class EmergencyDispatchForm extends Component
{
    public string $emergency_type = '';
    public string $location_description = '';
    public ?float $userLat = null;
    public ?float $userLng = null;
    public bool $locationGranted = false;
    public ?array $dispatchResult = null;
    public bool $dispatched = false;

    protected $rules = [
        'emergency_type' => 'required|string',
        'location_description' => 'nullable|string|max:500',
    ];

    public function requestLocation()
    {
        $this->dispatch('request-geolocation');
    }

    #[On('set-location')]
    public function setLocation(float $lat, float $lng)
    {
        $this->userLat = $lat;
        $this->userLng = $lng;
        $this->locationGranted = true;
    }

public function dispatchHelp()
{
    $this->validate();

    $dispatcher = app(EmergencyDispatcher::class);
    $result = $dispatcher->dispatch(
        [
            'lat' => $this->userLat,
            'lng' => $this->userLng,
            'description' => $this->location_description,
        ],
        $this->emergency_type,
        session()->getId()
    );

    // Send SMS with dispatch details
    if ($result['success']) {
        $user = new \App\Models\User();
        $user->phone = auth()->user()->phone ?? null;
        
        if ($user->phone) {
            $user->notify(new EmergencyDispatchNotification($result));
        }
    }

    $this->dispatchResult = $result;
    $this->dispatched = true;
}

    public function resetForm()
    {
        $this->reset();
    }

    public function render()
    {
        return view('livewire.emergency.emergency-dispatch-form', [
            'emergencyTypes' => EmergencyDispatch::emergencyTypes(),
        ])->layout('layouts.guest');
    }
}