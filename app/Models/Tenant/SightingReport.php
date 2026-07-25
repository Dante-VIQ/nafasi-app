<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;
use Illuminate\Support\Str;

class SightingReport extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'uuid', 'missing_person_alert_id', 'latitude', 'longitude',
        'notes', 'reporter_session_id', 'reported_at',
    ];

    protected $casts = [
        'reported_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($report) {
            $report->uuid = (string) Str::uuid();
            $report->reported_at = now();
        });
    }

    public function alert()
    {
        return $this->belongsTo(MissingPersonAlert::class, 'missing_person_alert_id');
    }
}