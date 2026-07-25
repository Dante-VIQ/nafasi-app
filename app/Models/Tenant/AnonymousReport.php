<?php

namespace App\Models\Tenant;

use App\Models\Tenant\Facility;
use App\Models\Tenant\TenantModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AnonymousReport extends TenantModel
{

    protected $fillable = [
        'uuid',
        'report_type',
        'description',
        'location_description',
        'time_description',
        'additional_details',
        'routed_to_facility_id',
        'routed_to_type',
        'status',
        'authority_notes',
        'auto_destroy_at',
    ];

    protected $casts = [
        'auto_destroy_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function ($report) {
            $report->uuid = (string) Str::uuid();
            $report->auto_destroy_at = now()->addDays(30); // Keep for 30 days max
        });
    }

    public function routedToFacility()
    {
        return $this->belongsTo(Facility::class, 'routed_to_facility_id');
    }

    public static function reportTypes(): array
    {
        return [
            'crime' => 'General Crime',
            'gbv' => 'Gender-Based Violence',
            'child_abuse' => 'Child Abuse',
            'corruption' => 'Corruption / Bribery',
            'trafficking' => 'Human Trafficking',
            'domestic_violence' => 'Domestic Violence',
            'drugs' => 'Drug-Related Crime',
            'theft' => 'Theft / Robbery',
            'assault' => 'Assault / Violence',
            'suspicious' => 'Suspicious Activity',
            'other' => 'Other',
        ];
    }

    public static function routedToTypes(): array
    {
        return [
            'police_station' => 'Police Station',
            'gbv_desk' => 'GBV Desk',
            'child_protection' => 'Child Protection Unit',
            'anti_corruption' => 'Anti-Corruption Commission',
            'human_trafficking' => 'Human Trafficking Hotline',
            'community_policing' => 'Community Policing',
        ];
    }
}