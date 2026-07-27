<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class CreateTenant extends Command
{
    protected $signature = 'tenants:create {tenantId} {name} {domain}';
    protected $description = 'Create a tenant with hardcoded DB credentials';

    public function handle(): int
    {
        $tenantId = $this->argument('tenantId');

        $tenant = Tenant::create([
            'id' => $tenantId,
            'name' => $this->argument('name'),
            'domain' => $this->argument('domain'),

            // hardcoded values
            'database_name' => 'u355928035_nafasi_kiambu',
            'db_username'   => 'u355928035_kiambu_county',
            'db_password'   => env('KIAMBU_DB_PASSWORD'),
        ]);

        config([
            'database.connections.tenant.database' => 'u355928035_nafasi_kiambu',
            'database.connections.tenant.username'  => 'u355928035_kiambu_county',
            'database.connections.tenant.password'  => env('KIAMBU_DB_PASSWORD'),
        ]);

        DB::purge('tenant');
        DB::reconnect('tenant');

        Artisan::call('tenants:migrate', [
            '--tenants' => [$tenant->id],
        ]);

        $this->info('Tenant created and migrations started.');

        return self::SUCCESS;
    }
}