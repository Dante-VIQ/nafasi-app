<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant;

class CreateTenant extends Command
{
    protected $signature = 'tenants:create
                            {id : Unique tenant ID, e.g. kiambu-county}
                            {name : Display name, e.g. "Kiambu County"}
                            {domain : Full domain, e.g. kiambu.nafasi.health}
                            {--org= : Organization name}
                            {--tier=government : Subscription tier}
                            {--region= : Region, e.g. Central}
                            {--country=KE : Country code}';

    protected $description = 'Create a new tenant with domain, database, and migrations';

    public function handle()
    {
        $id     = $this->argument('id');
        $name   = $this->argument('name');
        $domain = $this->argument('domain');

        // Check for duplicate
        if (Tenant::find($id)) {
            $this->error("Tenant '{$id}' already exists.");
            return 1;
        }

        // Create tenant
        $tenant = Tenant::create([
            'id'                  => $id,
            'name'                => $name,
            'organization'        => $this->option('org') ?? $name,
            'subscription_tier'   => $this->option('tier'),
            'subscription_status' => 'active',
            'region'              => $this->option('region'),
            'country'             => $this->option('country'),
            'status'              => 'active',
        ]);

        $tenant->domains()->create(['domain' => $domain]);

        $this->info("Tenant '{$id}' created.");

        // Create database
        $this->info('Creating tenant database...');
        $tenant->createDatabase();
        $this->info('Database created.');

        // Run tenant migrations
        $this->info('Running tenant migrations...');
        $this->call('tenants:migrate', ['--tenants' => $id]);
        $this->info('Migrations complete.');

        $this->info("✅ Tenant '{$id}' is ready at https://{$domain}");
    }
}