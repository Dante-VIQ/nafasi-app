<?php
// app/Models/User.php

namespace App\Models;

use App\Models\Tenant\Facility;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, HasRoles;

    protected $connection = 'mysql'; 
    
    protected $fillable = [
        'tenant_id',
        'name',
        'email',
        'password',
        'phone',
        'primary_role',
        'language_preference',
        'facility_id',
        // 2FA fields
        'two_factor_enabled',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_confirmed_at',
        'two_factor_method',
        'phone_for_2fa',
        'two_factor_code',
        'two_factor_code_expires_at',
        'two_factor_code_attempts',
        'last_login_at',
        'last_login_ip',
        'is_active',
        'locked_until',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_code',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_enabled' => 'boolean',
            'two_factor_confirmed_at' => 'datetime',
            'two_factor_code_expires_at' => 'datetime',
            'last_login_at' => 'datetime',
            'locked_until' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function isPlatformOwner(): bool
    {
        return $this->hasRole('platform-owner');
    }

    public function isPlatformAdmin(): bool
    {
        return $this->hasAnyRole(['platform-owner', 'super-admin']);
    }

    public function isVerificationPartner(): bool
    {
        return $this->hasRole('verification-partner');
    }

    public function isTenantAdmin(): bool
    {
        return $this->hasRole('tenant-admin');
    }

    public function isFacilityStaff(): bool
    {
        return $this->hasAnyRole(['facility-admin', 'facility-staff']);
    }

    public function isCoordinator(): bool
    {
        return $this->hasRole('coordinator');
    }

    public function hasTwoFactorEnabled(): bool
    {
        return $this->two_factor_enabled && $this->two_factor_confirmed_at !== null;
    }

    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }

    public function routeNotificationForSms(): ?string
{
    return $this->phone;
}
}