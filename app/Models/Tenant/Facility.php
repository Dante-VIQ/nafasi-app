<?php

namespace App\Models\Tenant;

use App\Models\Tenant\TenantModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Facility extends TenantModel
{
    use SoftDeletes;

    protected $connection = 'tenant';

    protected $fillable = [
        'uuid',
        'name',
        'slug',
        'facility_type',
        'description',
        'public_description',
        'phone',
        'email',
        'emergency_phone',
        'website',
        'address',
        'landmark',
        'city',
        'county',
        'postal_code',
        'latitude',
        'longitude',
        'operating_hours',
        'is_24_hours',
        'is_active',
        'is_verified',
        'is_public',
        'capabilities',
        'emergency_keywords',
        'exclusion_keywords',
        'emergency_definition',
        'exclusion_definition',
        'emergency_level',
        'overflow_facility_id',
        'can_dispatch_to_patient',
        'dispatch_service_type',
        'typical_response_time',
        'dispatch_radius_km',
        'health_system_level',
        'accepts_referrals',
        'accepting_referrals_now',
        'accepted_referral_types',
        'referral_congestion_status',
        'referral_congestion_updated_at',
        'requires_referral_letter',
        'accepts_self_referral',
        'registration_status',
        'verified_by',
        'verified_at',
        'verification_notes',
        'license_document_path',
        'license_expiry',
        'subscription_tier',
        'subscription_status',
        'trial_ends_at',
        'congestion_status',
        'congestion_updated_at',
        'routing_priority',
        'languages',
        'accepted_payment',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'operating_hours' => 'array',
        'capabilities' => 'array',
        'emergency_keywords' => 'array',
        'exclusion_keywords' => 'array',
        'accepted_referral_types' => 'array',
        'languages' => 'array',
        'accepted_payment' => 'array',
        'is_24_hours' => 'boolean',
        'is_active' => 'boolean',
        'is_verified' => 'boolean',
        'is_public' => 'boolean',
        'can_dispatch_to_patient' => 'boolean',
        'accepts_referrals' => 'boolean',
        'accepting_referrals_now' => 'boolean',
        'requires_referral_letter' => 'boolean',
        'accepts_self_referral' => 'boolean',
        'latitude' => 'float',
        'longitude' => 'float',
        'dispatch_radius_km' => 'integer',
        'health_system_level' => 'integer',
        'routing_priority' => 'integer',
        'congestion_updated_at' => 'datetime',
        'referral_congestion_updated_at' => 'datetime',
        'verified_at' => 'datetime',
        'trial_ends_at' => 'datetime',
        'license_expiry' => 'date',
    ];

    // No hidden fields needed at the model level yet
    protected $hidden = [];

    // Auto-generate UUID and slug
    protected static function booted(): void
    {
        static::creating(function ($facility) {
            $facility->uuid = $facility->uuid ?? (string) Str::uuid();
        });

        static::saving(function ($facility) {
            if ($facility->isDirty('name')) {
                $facility->slug = Str::slug($facility->name);
            }
        });
    }

    public static function facilityTypes(): array
    {
        return [
            'hospital' => 'General Hospital',
            'hospital_emergency' => 'Hospital with Emergency Department',
            'hospital_psychiatric' => 'Psychiatric Hospital',
            'health_centre' => 'Health Centre',
            'dispensary' => 'Dispensary',
            'pharmacy' => 'Pharmacy',
            'urgent_care' => 'Urgent Care Centre',
            'maternity_home' => 'Maternity / Birthing Centre',
            'dialysis_centre' => 'Dialysis Centre',
            'dental_clinic' => 'Dental Clinic',
            'eye_clinic' => 'Eye Clinic',
            'rehab_centre' => 'Rehabilitation Centre',
            'hospice' => 'Hospice / Palliative Care',
            'laboratory' => 'Diagnostic Laboratory',
            'imaging_centre' => 'Imaging Centre',
            'ambulance_service' => 'Ambulance Service',
            'fire_station' => 'Fire Station',
            'police_station' => 'Police Station',
            'mental_health_crisis' => 'Mental Health Crisis Centre',
            'gbv_safe_house' => 'GBV Safe House',
            'community_health' => 'Community Health Worker Network',
            'mobile_clinic' => 'Mobile Clinic',
        ];
    }

    public static function availableCapabilities(): array
    {
        return [
            'emergency_department' => 'Emergency Department',
            'icu' => 'Intensive Care Unit',
            'nicu' => 'Neonatal ICU',
            'surgery' => 'Surgical Theatre',
            'c_section' => 'C-Section Capable',
            'blood_bank' => 'Blood Bank',
            'antivenom' => 'Antivenom Stock',
            'cardiac_cath_lab' => 'Cardiac Catheterization Lab',
            'stroke_thrombolysis' => 'Stroke Thrombolysis',
            'burn_unit' => 'Burn Unit',
            'dialysis' => 'Dialysis',
            'chemotherapy' => 'Chemotherapy',
            'xray' => 'X-Ray',
            'ultrasound' => 'Ultrasound',
            'ct_scan' => 'CT Scan',
            'mri' => 'MRI',
            'laboratory' => 'Laboratory',
            'pharmacy' => 'Pharmacy (On-site)',
            'antenatal' => 'Antenatal Care',
            'immunization' => 'Immunization',
            'family_planning' => 'Family Planning',
            'hiv_testing' => 'HIV Testing & Counseling',
            'tb_treatment' => 'TB Treatment',
            'mental_health' => 'Mental Health Services',
            'crisis_hotline_24_7' => '24/7 Crisis Hotline',
            'suicide_prevention' => 'Suicide Prevention',
            'safe_shelter' => 'Safe Shelter',
            'trauma_counselling' => 'Trauma Counseling',
            'physiotherapy' => 'Physiotherapy',
            'ambulance_bay' => 'Ambulance Bay',
            'wheelchair_accessible' => 'Wheelchair Accessible',
            'sign_language' => 'Sign Language Available',
            'structural_fire' => 'Structural Firefighting',
            'bush_fire' => 'Bush/Wildfire Response',
            'auto_extrication' => 'Auto Extrication',
            'hazmat' => 'Hazardous Materials Response',
            'water_rescue' => 'Water Rescue',
            'gbv_desk' => 'Gender-Based Violence Desk',
            'child_protection' => 'Child Protection Unit',
        ];
    }

    public static function emergencyLevels(): array
    {
        return [
            'immediate' => 'Immediate — Life-threatening, respond within minutes',
            'urgent' => 'Urgent — Serious, respond within 1-2 hours',
            'standard' => 'Standard — Needs attention but not critical',
            'non_emergency' => 'Non-Emergency — Routine or scheduled care only',
        ];
    }

    public static function dispatchServiceTypes(): array
    {
        return [
            'ambulance' => 'Ambulance Service',
            'mobile_clinic' => 'Mobile Clinic',
            'community_health_worker' => 'Community Health Worker',
            'home_based_care' => 'Home-Based Care',
            'mental_health_crisis_team' => 'Mental Health Crisis Team',
            'police_gbv_unit' => 'Police GBV Unit',
            'fire_rescue' => 'Fire & Rescue',
            'pharmacy_delivery' => 'Pharmacy Delivery',
        ];
    }
}
