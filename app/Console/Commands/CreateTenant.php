<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreateTenant extends Command
{
    protected $signature = 'tenants:create
                            {tenantId : Unique tenant ID (e.g. kiambu)}
                            {name : Display name (e.g. "Kiambu County")}
                            {domain : Full domain (e.g. kiambu.vumbidna.com)}
                            {--db-name= : Pre-provisioned database name (defaults to tenantId if omitted)}
                            {--db-username= : MySQL username for that database}
                            {--db-password= : MySQL password for that database}';

    protected $description = 'Create a new tenant against a database that already exists in hPanel';

    public function handle(): int
    {
        $tenantId   = $this->argument('tenantId');
        $name       = $this->argument('name');
        $domain     = $this->argument('domain');

        $dbName     = $this->option('db-name') ?? $tenantId;
        $dbUsername = $this->option('db-username') ?? $this->ask('MySQL username for this tenant\'s database');
        $dbPassword = $this->option('db-password') ?? $this->secret('MySQL password for this tenant\'s database');

        if (! $dbUsername || ! $dbPassword) {
            $this->error('A database username and password are required — create these in hPanel first.');
            return self::FAILURE;
        }

        // 1. Verify the database is actually reachable with these credentials
        //    BEFORE creating any records. This is the check that would have
        //    caught a missing/mistyped database or wrong credentials up front,
        //    instead of failing halfway through migration.
        if (! $this->databaseIsReachable($dbName, $dbUsername, $dbPassword)) {
            $this->error("Could not connect to database [{$dbName}] with the given credentials.");
            $this->line('Confirm in hPanel that the database and user exist and are linked, then try again.');
            return self::FAILURE;
        }

        $this->info("Verified connection to database [{$dbName}].");

        $tenant = null;

        try {
            // 2. Create the tenant record, storing the pre-provisioned
            //    credentials so NoOpMySQLDatabaseManager can use them.
            $tenant = Tenant::create([
                'id'                  => $tenantId,
                'name'                => $name,
                'organization'        => $name,
                'subscription_tier'   => 'government',
                'subscription_status' => 'active',
                'status'              => 'active',
                'tenancy_db_name'     => $dbName,
                'tenancy_db_username' => $dbUsername,
                'tenancy_db_password' => $dbPassword,
            ]);

            // 3. Attach the domain
            $tenant->domains()->create(['domain' => $domain]);

            $this->info("Tenant '{$tenantId}' record created.");

            // 4. Run migrations for THIS tenant only.
            //    Note: --tenants (plural), stancl/tenancy's actual option name.
            $this->info('Running tenant migrations...');
            $exitCode = $this->call('tenants:migrate', ['--tenants' => [$tenantId]]);

            if ($exitCode !== self::SUCCESS) {
                throw new \RuntimeException('tenants:migrate returned a non-zero exit code.');
            }

            $this->info("✅ Tenant '{$tenantId}' is ready.");
            return self::SUCCESS;

        } catch (\Throwable $e) {
            // 5. Clean up the half-created tenant so re-running the command
            //    doesn't collide on a duplicate ID next time.
            Log::error("tenants:create failed for [{$tenantId}]: {$e->getMessage()}");
            $this->error("Failed to fully provision tenant '{$tenantId}': {$e->getMessage()}");

            if ($tenant) {
                $this->warn('Rolling back — deleting the partially-created tenant record.');
                $tenant->domains()->delete();
                $tenant->delete();
            }

            return self::FAILURE;
        }
    }

    /**
     * Test the given credentials against the given database directly,
     * independent of the app's central connection, using a throwaway
     * connection config. This mirrors exactly what the tenant's real
     * runtime connection will look like.
     */
    protected function databaseIsReachable(string $dbName, string $username, string $password): bool
    {
        $connectionName = 'tenant_check_'.$dbName;

        config([
            "database.connections.{$connectionName}" => array_merge(
                config('database.connections.mysql'),
                [
                    'database' => $dbName,
                    'username' => $username,
                    'password' => $password,
                ]
            ),
        ]);

        try {
            DB::connection($connectionName)->select('SELECT 1');
            return true;
        } catch (\Throwable $e) {
            return false;
        } finally {
            DB::purge($connectionName);
        }
    }
}