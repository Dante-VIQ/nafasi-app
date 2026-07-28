<?php

namespace App\Models;

use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains;

    protected $fillable = [
        'id',
        'name',
        'organization',
        'subscription_tier',
        'subscription_status',
        'trial_ends_at',
        'features',
        'region',
        'country',
        'status',
        'tenancy_db_name',
        'tenancy_db_username',
        'tenancy_db_password',
    ];

    protected $casts = [
        'features' => 'array',
        'trial_ends_at' => 'datetime',
    ];
    
    public static function getCustomColumns(): array
{
    return [
        'id',
        'name',
        'organization',
        'subscription_tier',
        'subscription_status',
        'trial_ends_at',
        'features',
        'region',
        'country',
        'status',
        'tenancy_db_name',
        'tenancy_db_username',
        'tenancy_db_password',
    ];
}
}
