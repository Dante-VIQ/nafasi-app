<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;

/**
 * Validates that the given database name/username/password combination
 * is actually reachable, BEFORE a tenant record gets saved.
 *
 * Used from both the `tenants:create` CLI command and the admin
 * dashboard tenant form, so both paths fail loudly and immediately
 * if the database was never pre-provisioned in hPanel, or the
 * credentials were mistyped — instead of failing later, mid-migration,
 * with a much more confusing error.
 *
 * Usage in a FormRequest or Livewire component:
 *
 *   'tenancy_db_name' => ['required', new ValidTenantDatabaseCredentials(
 *       $this->tenancy_db_username, $this->tenancy_db_password
 *   )],
 */
class ValidTenantDatabaseCredentials implements ValidationRule
{
    public function __construct(
        protected ?string $username,
        protected ?string $password,
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $dbName = $value;

        if (! $this->username || ! $this->password) {
            $fail('A database username and password are required to verify this database.');
            return;
        }

        $connectionName = 'tenant_credential_check_'.$dbName;

        config([
            "database.connections.{$connectionName}" => array_merge(
                config('database.connections.mysql'),
                [
                    'database' => $dbName,
                    'username' => $this->username,
                    'password' => $this->password,
                ]
            ),
        ]);

        try {
            DB::connection($connectionName)->select('SELECT 1');
        } catch (\Throwable $e) {
            $fail("Could not connect to database [{$dbName}] with the given credentials. ".
                  "Confirm it was created in hPanel and the username/password are correct.");
        } finally {
            DB::purge($connectionName);
        }
    }
}