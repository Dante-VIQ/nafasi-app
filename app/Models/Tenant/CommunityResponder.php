<?php

namespace App\Models\Tenant;

use App\Models\Tenant\TenantModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class CommunityResponder extends TenantModel
{
    use SoftDeletes;

    protected $fillable = [
        'uuid', 'name', 'phone', 'qualification', 'capabilities',
        'languages', 'base_latitude', 'base_longitude',
        'current_latitude', 'current_longitude', 'village', 'ward',
        'status', 'has_emergency_kit', 'is_verified',
        'total_emergencies_responded', 'lives_saved', 'rating', 'is_active',
    ];

    protected $casts = [
        'capabilities' => 'array',
        'languages' => 'array',
        'base_latitude' => 'float',
        'base_longitude' => 'float',
        'current_latitude' => 'float',
        'current_longitude' => 'float',
        'has_emergency_kit' => 'boolean',
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

    public function scopeNearby($query, float $lat, float $lng, int $radiusKm = 10)
    {
        return $query->selectRaw("
            *, 
            (6371 * acos(cos(radians(?)) * cos(radians(current_latitude)) * cos(radians(current_longitude) - radians(?)) + sin(radians(?)) * sin(radians(current_latitude)))) AS distance
        ", [$lat, $lng, $lat])
        ->having('distance', '<', $radiusKm)
        ->orderBy('distance');
    }
}