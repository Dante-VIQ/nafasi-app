<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Database Provisioning Mode
    |--------------------------------------------------------------------------
    |
    | 'manual'  — shared hosting (Hostinger demo/testing). The app's DB
    |             user cannot CREATE DATABASE/CREATE USER, so the admin
    |             pre-creates each tenant's database in hPanel and pastes
    |             the credentials into the tenant creation form. Backed by
    |             App\Tenancy\NoOpMySQLDatabaseManager.
    |
    | 'dynamic' — VPS / full-privilege MySQL (the eventual production
    |             environment). The app's DB user CAN create databases,
    |             so tenant creation is fully automatic — stancl/tenancy's
    |             real MySQLDatabaseManager provisions everything, no
    |             manual hPanel step, no credential fields in the form.
    |
    | Flip this one value when migrating to the VPS — nothing else in
    | the tenant creation flow needs to change.
    |
    */
    'db_provisioning' => env('TENANCY_DB_PROVISIONING', 'dynamic'),

    /*
    |--------------------------------------------------------------------------
    | Hostinger Account Prefix
    |--------------------------------------------------------------------------
    |
    | Only relevant in 'manual' mode — used to generate the suggested
    | database name shown to the admin, matching Hostinger's required
    | account-scoped naming (e.g. u355928035_nafasi_kiambu_county).
    | Irrelevant once on a VPS with dynamic provisioning.
    |
    */
    'hostinger_account_prefix' => env('HOSTINGER_ACCOUNT_PREFIX', 'u355928035_'),
];