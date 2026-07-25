<?php

namespace App\Models\Tenant;

use App\Models\Tenant\Facility;
use App\Models\Tenant\ReferralFeedback;
use App\Models\Tenant\TenantModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Referral extends TenantModel
{
    use SoftDeletes;

    protected $fillable = [
        'uuid', 'referring_facility_id', 'referring_staff_name', 'referring_staff_role',
        'receiving_facility_id', 'patient_reference_id', 'patient_gender', 'patient_age_group',
        'patient_is_stable', 'requires_ambulance', 'referral_type', 'urgency',
        'reason_for_referral', 'clinical_summary', 'treatment_given', 'additional_notes',
        'status', 'rejection_reason', 'redirected_to_facility_id',
        'submitted_at', 'accepted_at', 'patient_departed_at', 'estimated_arrival_at',
        'arrived_at', 'completed_at', 'referral_reference_code', 'communication_log',
    ];

    protected $casts = [
        'patient_is_stable' => 'boolean',
        'requires_ambulance' => 'boolean',
        'communication_log' => 'array',
        'submitted_at' => 'datetime',
        'accepted_at' => 'datetime',
        'patient_departed_at' => 'datetime',
        'estimated_arrival_at' => 'datetime',
        'arrived_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function ($referral) {
            $referral->uuid = (string) Str::uuid();
            $referral->referral_reference_code = 'REF-' . now()->format('Ymd') . '-' . 
                str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
            $referral->submitted_at = now();
        });
    }

    public function referringFacility() { return $this->belongsTo(Facility::class, 'referring_facility_id'); }
    public function receivingFacility() { return $this->belongsTo(Facility::class, 'receiving_facility_id'); }
    public function redirectedToFacility() { return $this->belongsTo(Facility::class, 'redirected_to_facility_id'); }
    public function feedback() { return $this->hasMany(ReferralFeedback::class); }

    public static function referralTypes(): array
    {
        return [
            'surgery' => 'Surgery',
            'maternal_emergency' => 'Maternal Emergency',
            'neonatal' => 'Neonatal Care',
            'icu' => 'ICU Admission',
            'pediatric' => 'Pediatric Care',
            'mental_health' => 'Mental Health',
            'trauma' => 'Trauma',
            'oncology' => 'Oncology',
            'cardiac' => 'Cardiac Care',
            'stroke' => 'Stroke',
            'burns' => 'Burns',
            'dialysis' => 'Dialysis',
            'diagnostic' => 'Advanced Diagnostics',
            'specialist_consult' => 'Specialist Consultation',
        ];
    }
}