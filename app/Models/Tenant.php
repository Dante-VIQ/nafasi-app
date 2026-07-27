<?php

namespace App\Models;

use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\DatabaseConfig;

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
    ];

    protected $casts = [
        'features'        => 'array',
        'trial_ends_at'   => 'datetime',
    ];

    // These columns are custom, must be listed so the package knows about them
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
        ];
    }

 

public function database(): DatabaseConfig
    {
        return $this->databaseConfig()
            ->setName('nafasi_' . $this->id)
            ->setUsername(config('database.connections.mysql.username'))
            ->setPassword(config('database.connections.mysql.password'))
            ->setHost(config('database.connections.mysql.host'))
            ->setPort(config('database.connections.mysql.port'));
    }
    
}
