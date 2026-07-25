<?php
// app/Services/Emergency/EmergencyDispatcher.php

namespace App\Services\Emergency;

use App\Jobs\SendSmsNotification;
use App\Models\Tenant\CommunityResponder;
use App\Models\Tenant\EmergencyDispatch;
use App\Models\Tenant\Facility;
use App\Models\Tenant\MotorbikeRider;
use Illuminate\Support\Facades\Cache;

class EmergencyDispatcher
{
    /**
     * Dispatch a responder and rider to a patient.
     * Returns the dispatch record with ETA.
     */
    public function dispatch(array $patientLocation, string $emergencyType, string $sessionId): array
    {
        // 1. Find nearest available responder with relevant capability
        $responder = $this->findNearestResponder($patientLocation, $emergencyType);
        
        if (!$responder) {
            return [
                'success' => false,
                'message' => 'No responder available nearby. Call 999 immediately.',
                'emergency_number' => '999',
            ];
        }

        // 2. Find nearest available rider (near the responder, not patient)
        $rider = $this->findNearestRider([
            'lat' => $responder->current_latitude ?? $responder->base_latitude,
            'lng' => $responder->current_longitude ?? $responder->base_longitude,
        ]);

        if (!$rider) {
            return [
                'success' => false,
                'message' => 'No transport available. Call 999 immediately.',
                'emergency_number' => '999',
            ];
        }

        // 3. Find nearest facility for this emergency type
        $facility = $this->findNearestFacility($patientLocation, $emergencyType);

        // 4. Calculate ETAs
        $riderToResponder = $this->calculateEta($rider, $responder);
        $responderToPatient = $this->calculateEta($responder, $patientLocation);
        $totalEtaMinutes = $riderToResponder + $responderToPatient + 2;

        // 5. Create dispatch record
        $dispatch = EmergencyDispatch::create([
            'emergency_type' => $emergencyType,
            'urgency' => 'immediate',
            'patient_location_description' => $patientLocation['description'] ?? null,
            'patient_latitude' => $patientLocation['lat'] ?? null,
            'patient_longitude' => $patientLocation['lng'] ?? null,
            'responder_id' => $responder->id,
            'rider_id' => $rider->id,
            'facility_id' => $facility?->id,
            'dispatched_at' => now(),
            'status' => 'dispatched',
            'patient_session_id' => $sessionId,
        ]);



// inside dispatch() method, replace simple update:
$lock = Cache::lock('responder_' . $responder->id, 10);

if ($lock->get()) {
    try {
        $freshResponder = CommunityResponder::find($responder->id);
        if ($freshResponder->status !== 'available') {
            return [
                'success' => false,
                'message' => 'Responder no longer available. Finding alternative…',
            ];
        }
        $freshResponder->update(['status' => 'responding']);
        $rider->update(['status' => 'on_dispatch']);
    } finally {
        $lock->release();
    }
} else {
    return [
        'success' => false,
        'message' => 'Could not secure responder. Please try again.',
    ];
}

    // Alert responder
    dispatch(new SendSmsNotification(
        $responder->phone,
        "NAFASI EMERGENCY: {$emergencyType}. Ref: {$dispatch->uuid}. Respond immediately."
    ));

    // Alert rider
    dispatch(new SendSmsNotification(
        $rider->phone,
        "NAFASI RIDE: Pickup responder, then to patient. Ref: {$dispatch->uuid}. Reply YES to accept."
    ));

    // Notify facility
    if ($facility && $facility->phone) {
        dispatch(new SendSmsNotification(
            $facility->phone,
            "NAFASI: Incoming {$emergencyType}. ETA: {$totalEtaMinutes}min. Ref: {$dispatch->uuid}"
        ));
    }

        return [
            'success' => true,
            'dispatch_id' => $dispatch->id,
            'dispatch_uuid' => $dispatch->uuid,
            'responder' => [
                'name' => $responder->name,
                'qualification' => $responder->qualification,
                'phone' => $responder->phone,
            ],
            'rider' => [
                'name' => $rider->name,
                'phone' => $rider->phone,
                'motorbike_reg' => $rider->motorbike_registration,
            ],
            'facility' => $facility ? [
                'name' => $facility->name,
                'phone' => $facility->phone,
                'distance_km' => $facility->distance ?? null,
            ] : null,
            'eta_to_patient_minutes' => $totalEtaMinutes,
            'instructions' => $this->getPatientInstructions($emergencyType),
        ];
    }

    protected function findNearestResponder(array $location, string $emergencyType): ?CommunityResponder
    {
        return CommunityResponder::available()
            ->whereJsonContains('capabilities', $emergencyType)
            ->where('is_verified', true)
            ->when(isset($location['lat']), fn($q) => 
                $q->nearby($location['lat'], $location['lng'], 10)
            )
            ->first();
    }

    protected function findNearestRider(array $location): ?MotorbikeRider
    {
        return MotorbikeRider::available()
            ->where('is_verified', true)
            ->when(isset($location['lat']), fn($q) =>
                $q->nearby($location['lat'], $location['lng'], 5)
            )
            ->first();
    }

    protected function findNearestFacility(array $location, string $emergencyType): ?Facility
    {
        $capabilityMap = [
            'snakebite' => 'antivenom',
            'accident' => 'emergency_department',
            'maternal' => 'c_section',
            'cardiac' => 'cardiac_cath_lab',
            'burn' => 'burn_unit',
        ];

        $capability = $capabilityMap[$emergencyType] ?? 'emergency_department';

        return Facility::query()
            ->where('is_active', true)
            ->where('registration_status', 'approved')
            ->whereJsonContains('capabilities', $capability)
            ->when(isset($location['lat']), fn($q) =>
                $q->selectRaw("*, (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance", 
                    [$location['lat'], $location['lng'], $location['lat']])
                ->orderBy('distance')
                ->having('distance', '<', 50)
            )
            ->first();
    }

    protected function calculateEta($from, $to): int
    {
        $fromLat = $from->current_latitude ?? $from->base_latitude ?? ($from['lat'] ?? 0);
        $fromLng = $from->current_longitude ?? $from->base_longitude ?? ($from['lng'] ?? 0);
        $toLat = $to->current_latitude ?? $to->base_latitude ?? ($to['lat'] ?? 0);
        $toLng = $to->current_longitude ?? $to->base_longitude ?? ($to['lng'] ?? 0);

        if (!$fromLat || !$toLat) return 15;

        $distance = $this->haversine($fromLat, $fromLng, $toLat, $toLng);
        $avgSpeedKph = 35; // Motorbike on rural roads
        return ceil(($distance / $avgSpeedKph) * 60) + 2;
    }

    protected function haversine(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371;
        $latDelta = deg2rad($lat2 - $lat1);
        $lngDelta = deg2rad($lng2 - $lng1);
        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($lngDelta / 2) * sin($lngDelta / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }

    protected function getPatientInstructions(string $type): array
    {
        return match($type) {
            'snakebite' => [
                'Stay still. Movement spreads venom.',
                'Lie down. Keep bite below heart.',
                'Remove tight clothing near bite.',
                'DO NOT tie tourniquet.',
                'DO NOT cut or suck wound.',
                'Help is coming. Stay calm.',
            ],
            'accident' => [
                'Do not move if neck/spine injury suspected.',
                'Apply pressure to bleeding wounds.',
                'Keep warm.',
                'Stay with the patient.',
            ],
            default => [
                'Stay calm.',
                'Keep patient comfortable.',
                'Help is on the way.',
            ],
        };
    }
}