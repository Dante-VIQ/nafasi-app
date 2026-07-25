<?php

namespace App\Models\Tenant;

use App\Models\Tenant\CommunityResponder;
use App\Models\Tenant\Facility;
use App\Models\Tenant\MotorbikeRider;
use App\Models\Tenant\TenantModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class EmergencyDispatch extends TenantModel
{

    protected $fillable = [
        'uuid', 'emergency_type', 'urgency',
        'patient_location_description', 'patient_latitude', 'patient_longitude',
        'responder_id', 'rider_id', 'facility_id',
        'dispatched_at', 'responder_reached_patient_at',
        'patient_reached_facility_at', 'resolved_at',
        'status', 'outcome', 'rider_payment', 'responder_payment',
        'payment_verified', 'payment_sent_at',
        'patient_session_id', 'auto_destroy_at',
    ];

    protected $casts = [
        'patient_latitude' => 'float',
        'patient_longitude' => 'float',
        'dispatched_at' => 'datetime',
        'responder_reached_patient_at' => 'datetime',
        'patient_reached_facility_at' => 'datetime',
        'resolved_at' => 'datetime',
        'payment_verified' => 'boolean',
        'payment_sent_at' => 'datetime',
        'auto_destroy_at' => 'datetime',
        'rider_payment' => 'float',
        'responder_payment' => 'float',
    ];

    protected static function booted(): void
    {
        static::creating(function ($dispatch) {
            $dispatch->uuid = (string) Str::uuid();
            $dispatch->auto_destroy_at = now()->addHours(48);
        });
    }

    public function responder()
    {
        return $this->belongsTo(CommunityResponder::class);
    }

    public function rider()
    {
        return $this->belongsTo(MotorbikeRider::class);
    }

    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }

    public static function emergencyTypes(): array
    {
        return [
            'snakebite' => 'Snake Bite',
            'accident' => 'Accident / Trauma',
            'maternal' => 'Maternal Emergency',
            'cardiac' => 'Cardiac Emergency',
            'respiratory' => 'Respiratory Distress',
            'burn' => 'Severe Burns',
            'other' => 'Other Emergency',
        ];
    }
}