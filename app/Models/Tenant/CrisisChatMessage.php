<?php
// app/Models/Tenant/CrisisChatMessage.php

namespace App\Models\Tenant;

use App\Models\Tenant\CrisisChatSession;
use App\Models\Tenant\TenantModel;
use Illuminate\Database\Eloquent\Model;

class CrisisChatMessage extends TenantModel
{

    protected $fillable = [
        'session_id', 'content_encrypted', 'sender_type', 'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function session()
    {
        return $this->belongsTo(CrisisChatSession::class, 'session_id');
    }
}