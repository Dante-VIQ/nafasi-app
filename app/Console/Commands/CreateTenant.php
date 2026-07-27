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
        $tenant = $this->argument('tenantId');

        $tenant = Tenant::create([
            'id' => 'kiambu',
            'name' => 'Kiambu County',
            'domain' => 'kiambu.vumbidna.com',
            'database_name' => 'nafasi_kiambu',
            'db_username' => 'u355928035_kiambu_county',
            'db_password' => env('KIAMBU_DB_PASSWORD'),
        ]);

        config()->set('database.connections.tenant.database', 'nafasi_kiambu');
        config()->set('database.connections.tenant.username', 'u355928035_kiambu_county');
        config()->set('database.connections.tenant.password', env('KIAMBU_DB_PASSWORD'));

        DB::purge('tenant');
        DB::reconnect('tenant');

        Artisan::call('tenants:migrate', [
            '--tenants' => ['kiambu'],
        ]);

        $this->info('Tenant created and migrations started.');

        return self::SUCCESS;
    }
}
