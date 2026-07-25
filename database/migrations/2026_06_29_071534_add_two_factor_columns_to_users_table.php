<?php
// database/migrations/xxxx_add_two_factor_columns_to_users_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('two_factor_enabled')->default(false);
            $table->string('two_factor_secret')->nullable();
            $table->string('two_factor_recovery_codes')->nullable();
            $table->timestamp('two_factor_confirmed_at')->nullable();
            $table->string('two_factor_method')->default('app');
            $table->string('phone_for_2fa')->nullable();
            $table->string('two_factor_code')->nullable();
            $table->timestamp('two_factor_code_expires_at')->nullable();
            $table->integer('two_factor_code_attempts')->default(0);
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('locked_until')->nullable();
            $table->string('primary_role')->default('public-user')->after('email');
            $table->string('phone')->nullable()->after('email');
            $table->string('language_preference')->default('en')->after('phone');
            $table->foreignId('facility_id')->nullable()->after('language_preference');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'two_factor_enabled', 'two_factor_secret',
                'two_factor_recovery_codes', 'two_factor_confirmed_at',
                'two_factor_method', 'phone_for_2fa',
                'two_factor_code', 'two_factor_code_expires_at',
                'two_factor_code_attempts', 'last_login_at',
                'last_login_ip', 'is_active', 'locked_until',
                'primary_role', 'phone', 'language_preference', 'facility_id',
            ]);
        });
    }
};