<?php
// database/migrations/tenant/xxxx_create_anonymous_reports_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anonymous_reports', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();                    // Report ID (only identifier)
            
            // Report content (situational only)
            $table->string('report_type');               // crime, corruption, gbv, child_abuse, trafficking, other
            $table->text('description');                 // What happened
            $table->text('location_description')->nullable(); // Where
            $table->string('time_description')->nullable();   // When
            $table->text('additional_details')->nullable();   // Any other info
            
            // Routing
            $table->foreignId('routed_to_facility_id')->nullable()->constrained('facilities')->nullOnDelete();
            $table->string('routed_to_type')->nullable(); // police_station, gbv_desk, child_protection, etc.
            
            // Status
            $table->string('status')->default('submitted'); // submitted, received, acknowledged, investigated, resolved
            $table->text('authority_notes')->nullable();    // Notes from receiving authority (optional)
            
            // NO personal data stored
            // NO IP address
            // NO device info
            // NO phone number
            // NO name
            // NO email
            
            // Auto-destroy
            $table->timestamp('auto_destroy_at')->nullable();
            
            $table->timestamps();
            
            $table->index('report_type');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anonymous_reports');
    }
};