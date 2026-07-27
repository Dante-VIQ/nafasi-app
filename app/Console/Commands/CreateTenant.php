<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class CreateTenant extends Command
{
    protected $signature = 'tenants:create
                            {tenantId : Unique tenant ID (e.g. kiambu)}
                            {name : Display name (e.g. "Kiambu County")}
                            {domain : Full domain (e.g. kiambu.vumbidna.com)}';

    protected $description = 'Create a new tenant (database must already exist in hPanel)';

    public function handle(): int
    {
        $tenantId = $this->argument('tenantId');
        $name     = $this->argument('name');
        $domain   = $this->argument('domain');

        // 1. Create the tenant record
        $tenant = Tenant::create([
            'id'                  => $tenantId,
            'name'                => $name,
            'organization'        => $name,
            'subscription_tier'   => 'government',
            'subscription_status' => 'active',
            'status'              => 'active',
        ]);

        // 2. Attach the domain
        $tenant->domains()->create(['domain' => $domain]);

        $this->info("Tenant '{$tenantId}' created.");

        // 3. Run migrations (database must already exist + user must have privileges)
        $this->info('Running tenant migrations...');
        $this->call('tenants:migrate', ['--tenant' => $tenantId]);

        $this->info("✅ Tenant '{$tenantId}' is ready.");

        return self::SUCCESS;
    }
}