<?php

namespace App\Models\Tenant;

use App\Models\Tenant\Facility;
use App\Models\Tenant\TenantModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Appointment extends TenantModel
{
    use SoftDeletes;

    protected $fillable = [
        'facility_id',
        'patient_name',
        'patient_phone',
        'patient_email',
        'scheduled_at',
        'reason',
        'status',
        'notes',
        'source',
        'nafasi_session_id',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }
}