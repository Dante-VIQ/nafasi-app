<?php

namespace App\Tenancy;

use Stancl\Tenancy\DatabaseConfig;

/**
 * stancl/tenancy's DatabaseConfig::tenantConfig() picks up any tenant
 * attribute matching `{prefix}db_*` (e.g. tenancy_db_username,
 * tenancy_db_password) by KEY EXISTENCE, not by whether it has a value.
 *
 * In manual-provisioning mode (Hostinger) those columns hold real
 * credentials, and SHOULD override the base 'mysql' template — that's
 * exactly the package's intended per-tenant credential mechanism.
 *
 * In dynamic-provisioning mode (local/VPS) we deliberately leave those
 * columns null, since the tenant should just use the same central DB
 * user (only the database name differs). But array_merge() in the
 * parent's connection() method treats a null value as "present" and
 * lets it clobber the base template's real username/password with
 * null — which is what caused "Access denied for user ''@'localhost'".
 *
 * This subclass strips null-valued entries before they reach the
 * merge, so:
 *   - manual mode:  real strings present -> still override (unchanged)
 *   - dynamic mode: nulls present -> filtered out -> base template's
 *                   real credentials (e.g. root) pass through untouched
 */
class NafasiDatabaseConfig extends DatabaseConfig
{
    public function tenantConfig(): array
    {
        return array_filter(
            parent::tenantConfig(),
            fn ($value) => $value !== null
        );
    }
}