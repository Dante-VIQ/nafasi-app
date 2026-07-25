<?php


namespace App\Models\Tenant;

use App\Models\Tenant\Facility;
use App\Models\Tenant\TenantModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AssistanceRequest extends TenantModel
{

    protected $fillable = [
        'uuid',
        'session_id',
        'phone_number',
        'preferred_language',
        'user_description',
        'urgency',
        'detected_tags',
        'latitude',
        'longitude',
        'location_description',
        'coordinator_id',
        'coordinator_notes',
        'status',
        'dispatched_facility_id',
        'dispatch_message',
        'dispatched_service_type',
        'dispatched_at',
        'estimated_arrival',
        'resolution',
        'resolved_at',
        'auto_destroy_at',
    ];

    protected $casts = [
        'detected_tags' => 'array',
        'latitude' => 'float',
        'longitude' => 'float',
        'dispatched_at' => 'datetime',
        'resolved_at' => 'datetime',
        'auto_destroy_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function ($request) {
            $request->uuid = (string) Str::uuid();
            $request->auto_destroy_at = now()->addHours(24);
        });
    }

    public function coordinator()
    {
        return $this->belongsTo(\App\Models\User::class, 'coordinator_id');
    }

    public function dispatchedFacility()
    {
        return $this->belongsTo(Facility::class, 'dispatched_facility_id');
    }

    public function isPending(): bool { return $this->status === 'pending'; }
    public function isResolved(): bool { return $this->status === 'resolved'; }
}