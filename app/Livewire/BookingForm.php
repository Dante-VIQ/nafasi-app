<?php
// app/Livewire/BookingForm.php

namespace App\Livewire;

use App\Jobs\SendSmsNotification;
use App\Models\Tenant\Appointment;
use App\Models\Tenant\Facility;
use App\Notifications\BookingConfirmationNotification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Livewire\Component;

class BookingForm extends Component
{
    public Facility $facility;          // still type-hinted
    public string $patient_name = '';
    public string $patient_phone = '';
    public string $patient_email = '';
    public ?string $scheduled_at = null;
    public string $reason = '';
    public bool $showForm = false;
    public bool $booked = false;

    protected $rules = [
        'patient_name' => 'required|string|max:255',
        'patient_phone' => 'nullable|string|max:20',
        'patient_email' => 'nullable|email|max:255',
        'scheduled_at' => 'required|date|after:now',
        'reason' => 'nullable|string|max:500',
    ];

    public function mount($facilityId)
    {
        $this->facility = Facility::where('id', $facilityId)
            ->where('is_active', true)
            ->where('registration_status', 'approved')
            ->findOrFail($facilityId);
    }

    public function book()
    {
        $this->validate();
        
    // Ensure we are inside a tenant context
    $tenant = \App\Models\Tenant::where('status', 'active')->first();
    if ($tenant && !tenancy()->initialized) {
        tenancy()->initialize($tenant);
    }
        $appointment = Appointment::create([
            'facility_id' => $this->facility->id,
            'patient_name' => $this->patient_name,
            'patient_phone' => $this->patient_phone,
            'patient_email' => $this->patient_email,
            'scheduled_at' => $this->scheduled_at,
            'reason' => $this->reason,
            'status' => 'pending',
            'source' => 'nafasi',
          'nafasi_session_id' => Str::uuid()->toString(),
        ]);

        // Send SMS confirmation
        if ($this->patient_phone) {
            $user = new \App\Models\User();
            $user->phone = $this->patient_phone;

            $user->notify(new BookingConfirmationNotification([
                'facility_name' => $this->facility->name,
                'date' => \Carbon\Carbon::parse($this->scheduled_at)->format('d M Y'),
                'time' => \Carbon\Carbon::parse($this->scheduled_at)->format('H:i'),
                'reference' => substr($appointment->uuid, 0, 8),
            ]));
        }

        $this->booked = true;
    }

    public function render()
    {
        return view('livewire.booking-form');
    }
}