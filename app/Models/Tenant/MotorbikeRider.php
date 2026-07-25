<?php

namespace App\Models\Tenant;

use App\Models\Tenant\TenantModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class MotorbikeRider extends TenantModel
{
    use SoftDeletes;

    protected $fillable = [
        'uuid', 'name', 'phone', 'motorbike_registration',
        'has_helmet_for_passenger', 'base_latitude', 'base_longitude',
        'current_latitude', 'current_longitude', 'base_stage_name',
        'status', 'is_verified', 'total_emergencies_responded',
        'rating', 'mpesa_number', 'is_active',
    ];

    protected $casts = [
        'has_helmet_for_passenger' => 'boolean',
        'base_latitude' => 'float',
        'base_longitude' => 'float',
        'current_latitude' => 'float',
        'current_longitude' => 'float',
        'is_verified' => 'boolean',
        'is_active' => 'boolean',
        'rating' => 'float',
    ];

    protected static function booted(): void
    {
        static::creating(fn($r) => $r->uuid = (string) Str::uuid());
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'available')->where('is_active', true);
    }

    public function scopeNearby($query, float $lat, float $lng, int $radiusKm = 5)
    {
        return $query->selectRaw("
            *, 
            (6371 * acos(cos(radians(?)) * cos(radians(current_latitude)) * cos(radians(current_longitude) - radians(?)) + sin(radians(?)) * sin(radians(current_latitude)))) AS distance
        ", [$lat, $lng, $lat])
        ->having('distance', '<', $radiusKm)
        ->orderBy('distance');
    }
}