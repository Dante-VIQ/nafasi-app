<?php

namespace App\Models\Tenant;

use App\Models\Tenant\CrisisChatMessage;
use App\Models\Tenant\TenantModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CrisisChatSession extends TenantModel
{

    protected $fillable = [
        'uuid', 'session_token', 'crisis_type', 'language', 'status',
        'counselor_id', 'connected_at', 'ended_at', 'general_area',
        'communication_method', 'auto_destroy_at',
    ];

    protected $casts = [
        'connected_at' => 'datetime',
        'ended_at' => 'datetime',
        'auto_destroy_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function ($session) {
            $session->uuid = (string) Str::uuid();
            $session->session_token = Str::random(128);
            $session->auto_destroy_at = now()->addHours(2);
        });
    }

    public function messages()
    {
        return $this->hasMany(CrisisChatMessage::class, 'session_id');
    }

    public function counselor()
    {
        return $this->belongsTo(\App\Models\User::class, 'counselor_id');
    }

    public function isActive(): bool
    {
        return $this->status === 'connected' || $this->status === 'waiting';
    }
}