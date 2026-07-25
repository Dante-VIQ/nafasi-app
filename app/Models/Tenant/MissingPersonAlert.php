<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;
use Illuminate\Support\Str;

class MissingPersonAlert extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'uuid', 'name', 'age_group', 'gender', 'description',
        'last_seen_location', 'suspect_description',
        'photo_path', 'suspect_photo_path', 'contact_phone',
        'status', 'reported_by', 'found_at', 'expires_at',
    ];

    protected $casts = [
        'found_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($alert) {
            $alert->uuid = (string) Str::uuid();
            $alert->expires_at = $alert->expires_at ?? now()->addHours(72);
        });
    }

    public function sightingReports()
    {
        return $this->hasMany(SightingReport::class);
    }

    public function reportedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'reported_by');
    }
}