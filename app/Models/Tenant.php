<?php

namespace App\Models;

use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\DatabaseConfig;
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
    // The second parameter is the driver, e.g. 'mysql'
    $config = new DatabaseConfig($this, 'mysql');

    return $config
        ->database('nafasi_' . $this->id)
        ->username(config('database.connections.mysql.username'))
        ->password(config('database.connections.mysql.password'))
        ->host(config('database.connections.mysql.host'))
        ->port(config('database.connections.mysql.port'));
}
    
}
