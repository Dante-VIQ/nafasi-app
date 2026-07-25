<?php

namespace App\Livewire\Alert;

use Livewire\Component;
use App\Models\Tenant\MissingPersonAlert;
use App\Models\Tenant\SightingReport;
use App\Models\Tenant\Facility;
use App\Services\SmsService;
 use Livewire\Attributes\On;

class CommunityAlertFeed extends Component
{
    public array $alerts = [];

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

        $this->alerts = MissingPersonAlert::where('status', 'active')
            ->where('expires_at', '>', now())
            ->latest()
            ->take(5)
            ->get()
            ->toArray();
    }



#[On('setLocation')]
public function setLocation(float $lat, float $lng): void
{
    session(['user_latitude' => $lat, 'user_longitude' => $lng]);
}




public function reportSighting(int $alertId)
{
        $this->ensureTenant();
    $alert = MissingPersonAlert::findOrFail($alertId);

    // Capture location from browser (Alpine.js will set these)
    $lat = session('user_latitude');
    $lng = session('user_longitude');

        if (!session('user_latitude')) {
        $this->dispatch('request-location');
        // Wait a moment then try again, or ask user to share location manually
        session()->flash('sighting_message', 'Please enable location to report the sighting.');
        return;
    }
    // Create anonymous sighting report
    SightingReport::create([
        'missing_person_alert_id' => $alertId,
        'latitude'               => $lat,
        'longitude'              => $lng,
        'notes'                  => 'Sighting reported via public feed',
        'reporter_session_id'    => session()->getId(),
    ]);

    // Find nearest police station and notify them (simplified)
    $policeStation = Facility::where('facility_type', 'police_station')
        ->where('is_active', true)
        ->when($lat && $lng, function ($query) use ($lat, $lng) {
            $query->selectRaw("*, (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance", [$lat, $lng, $lat])
                  ->orderBy('distance');
        })
        ->first();

    // Send SMS notification to alert contact if available
    if ($alert->contact_phone) {
        app(SmsService::class)->send(
            $alert->contact_phone,
            "Nafasi Alert: A sighting of missing person '{$alert->name}' has been reported. Location: " . 
            ($lat && $lng ? "$lat, $lng" : 'Not provided') . 
            ". Please check your dashboard for details."
        );
    }

    // Also notify the nearest police station if phone available
    if ($policeStation && $policeStation->phone) {
        app(SmsService::class)->send(
            $policeStation->phone,
            "Nafasi: Sighting of missing person '{$alert->name}' reported near your station. Check admin panel."
        );
    }

    session()->flash('sighting_message', 'Thank you! Authorities have been notified. Your report is anonymous.');
    $this->loadAlerts();
}
    public function render()
    {
        return view('livewire.alert.community-alert-feed', [
            'alerts' => $this->alerts,
        ]);
    }
}