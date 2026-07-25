<?php
// database/migrations/xxxx_add_custom_columns_to_tenants_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('name')->nullable()->after('id');
            $table->string('organization')->nullable()->after('name');
            $table->string('subscription_tier')->default('free')->after('organization');
            $table->string('subscription_status')->default('inactive')->after('subscription_tier');
            $table->timestamp('trial_ends_at')->nullable()->after('subscription_status');
            $table->json('features')->nullable()->after('trial_ends_at');
            $table->string('region')->nullable()->after('features');
            $table->string('country')->nullable()->after('region');
            $table->string('status')->default('active')->after('country');
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn([
                'name', 'organization', 'subscription_tier', 'subscription_status',
                'trial_ends_at', 'features', 'region', 'country', 'status',
            ]);
        });
    }
};