<?php
// database/migrations/tenant/xxxx_create_motorbike_riders_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('motorbike_riders', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();
            $table->string('name');
            $table->string('phone')->unique();
            $table->string('motorbike_registration')->unique();
            $table->boolean('has_helmet_for_passenger')->default(false);
            $table->decimal('base_latitude', 10, 7);
            $table->decimal('base_longitude', 10, 7);
            $table->decimal('current_latitude', 10, 7)->nullable();
            $table->decimal('current_longitude', 10, 7)->nullable();
            $table->string('base_stage_name')->nullable(); // "Kenyatta Market Stage"
            $table->string('status')->default('offline'); // offline, available, on_dispatch
            $table->boolean('is_verified')->default(false);
            $table->integer('total_emergencies_responded')->default(0);
            $table->float('rating')->default(5.0);
            $table->string('mpesa_number')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('motorbike_riders');
    }
};