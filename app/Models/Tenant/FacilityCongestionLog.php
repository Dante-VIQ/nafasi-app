<?php
// app/Models/Tenant/FacilityCongestionLog.php

namespace App\Models\Tenant;

use App\Models\Tenant\Facility;
use App\Models\Tenant\TenantModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class FacilityCongestionLog extends TenantModel
{

    protected $fillable = [
        'facility_id',
        'status',
        'source',
        'reported_by',
        'notes',
    ];

    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }

    public function reportedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'reported_by');
    }
}