<?php
// database/migrations/tenant/xxxx_create_emergency_dispatches_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('emergency_dispatches', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();
            $table->string('emergency_type'); // snakebite, accident, maternal, cardiac, other
            $table->string('urgency')->default('immediate'); // immediate, urgent
            $table->text('patient_location_description')->nullable();
            $table->decimal('patient_latitude', 10, 7)->nullable();
            $table->decimal('patient_longitude', 10, 7)->nullable();
            
            // Assigned personnel
            $table->foreignId('responder_id')->nullable()->constrained('community_responders')->nullOnDelete();
            $table->foreignId('rider_id')->nullable()->constrained('motorbike_riders')->nullOnDelete();
            $table->foreignId('facility_id')->nullable()->constrained('facilities')->nullOnDelete();
            
            // Timing
            $table->timestamp('dispatched_at')->nullable();
            $table->timestamp('responder_reached_patient_at')->nullable();
            $table->timestamp('patient_reached_facility_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            
            // Status
            $table->string('status')->default('pending'); // pending, dispatched, responder_en_route, at_patient, en_route_facility, at_facility, resolved, cancelled
            $table->string('outcome')->nullable(); // treated_on_site, transported, deceased, referred
            
            // Payment
            $table->decimal('rider_payment', 10, 2)->nullable();
            $table->decimal('responder_payment', 10, 2)->nullable();
            $table->boolean('payment_verified')->default(false);
            $table->timestamp('payment_sent_at')->nullable();
            
            // Privacy
            $table->string('patient_session_id')->nullable(); // Anonymous
            $table->timestamp('auto_destroy_at')->nullable();
            
            $table->timestamps();
            
            $table->index('status');
            $table->index('emergency_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('emergency_dispatches');
    }
};