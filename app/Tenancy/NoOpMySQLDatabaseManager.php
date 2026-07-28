<?php

namespace App\Tenancy;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Contracts\TenantDatabaseManager;

/**
 * Drop-in replacement for stancl/tenancy's MySQLDatabaseManager.
 *
 * On shared hosting (Hostinger etc.) the app's DB user does NOT have
 * CREATE DATABASE / CREATE USER privileges — databases are provisioned
 * through hPanel (or the Hostinger API) ahead of time, each with its
 * own dedicated username/password.
 *
 * This manager does NOT attempt to create or drop databases. It only:
 *  - checks whether the pre-provisioned database is reachable
 *  - builds the connection config Laravel should use for that tenant
 *
 * Tenant provisioning workflow becomes:
 *   1. Create the database + user manually in hPanel (or via API script)
 *   2. Store those credentials on the Tenant record
 *   3. tenancy()->initialize($tenant) just switches the connection —
 *      no CREATE DATABASE ever runs.
 */
class NoOpMySQLDatabaseManager implements TenantDatabaseManager
{
    /**
     * The base connection (usually 'mysql', your central connection)
     * that stancl/tenancy tells us to use for admin-ish operations
     * like checking whether a database exists. Set via setConnection().
     */
    protected string $connection = 'mysql';

    /**
     * Required by the TenantDatabaseManager contract. Called by
     * stancl/tenancy before createDatabase()/databaseExists() so the
     * manager knows which base connection to run checks against.
     */
    public function setConnection(string $connection): void
    {
        $this->connection = $connection;
    }

    /**
     * Instead of running CREATE DATABASE, just verify the
     * pre-provisioned database already exists and is reachable.
     * Throws a clear exception if someone forgot to provision it.
     */
    public function createDatabase(TenantWithDatabase $tenant): bool
    {
        $name = $tenant->database()->getName();

        if (! $this->databaseExists($name)) {
            Log::error("Tenancy: database [{$name}] was not found. ".
                "On shared hosting it must be pre-created via hPanel/API before assigning it to a tenant.");

            throw new \RuntimeException(
                "Database [{$name}] does not exist. Pre-provision it in hPanel before creating this tenant."
            );
        }

        return true;
    }

    /**
     * Never actually drop the database — on shared hosting you almost
     * always want to keep tenant data around (or archive it manually)
     * rather than let an app-level DELETE cascade into DROP DATABASE.
     * If you genuinely want this to be destructive, drop the database
     * manually via hPanel/phpMyAdmin instead.
     */
    public function deleteDatabase(TenantWithDatabase $tenant): bool
    {
        Log::warning("Tenancy: deleteDatabase() called for [{$tenant->database()->getName()}] ".
            "but auto-deletion is disabled on shared hosting. Remove it manually via hPanel if needed.");

        return true;
    }

    /**
     * Check whether the database exists, using the central connection
     * (e.g. 'mysql') and querying information_schema — this only needs
     * SELECT, which Hostinger's default user is always granted, unlike
     * CREATE DATABASE/CREATE USER which shared hosting never grants.
     */
    public function databaseExists(string $name): bool
    {
        try {
            $result = DB::connection($this->connection)
                ->select('SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?', [$name]);

            return count($result) > 0;
        } catch (\Throwable $e) {
            Log::error("Tenancy: could not verify database [{$name}]: {$e->getMessage()}");
            return false;
        }
    }

    /**
     * Build the connection config for this tenant's database.
     *
     * $baseConfig comes from config('tenancy.database.managers.mysql')
     * or your central 'mysql' connection as a template (host, port, charset...).
     * We override database/username/password with the tenant's own
     * pre-provisioned credentials, since Hostinger gives each database
     * its own user rather than one shared super-user.
     */
public function makeConnectionConfig(array $baseConfig, string $databaseName): array
{
    $tenant = tenancy()->tenant;

    $username = $tenant->tenancy_db_username;
    $password = $tenant->tenancy_db_password;

    return array_merge($baseConfig, [
        'database' => $databaseName,
        'username' => filled($username) ? $username : $baseConfig['username'],
        'password' => filled($password) ? $password : $baseConfig['password'],
    ]);
}
}