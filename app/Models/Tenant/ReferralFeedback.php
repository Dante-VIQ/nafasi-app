<?php

namespace App\Models\Tenant;

use App\Models\Tenant\Facility;
use App\Models\Tenant\Referral;
use App\Models\Tenant\TenantModel;
use Illuminate\Database\Eloquent\Model;

class ReferralFeedback extends TenantModel
{

    protected $fillable = [
        'referral_id', 'facility_id', 'was_appropriate',
        'patient_accepted', 'feedback_notes',
    ];

    protected $casts = [
        'was_appropriate' => 'boolean',
        'patient_accepted' => 'boolean',
    ];

    public function referral() { return $this->belongsTo(Referral::class); }
    public function facility() { return $this->belongsTo(Facility::class); }
}