<?php
// database/migrations/tenant/xxxx_create_assistance_requests_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assistance_requests', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();
            
            // User info (MINIMAL)
            $table->string('session_id'); // Temporary, not personal
            $table->string('phone_number')->nullable(); // Optional, for callback
            $table->string('preferred_language')->default('sw');
            
            // Situation
            $table->text('user_description')->nullable();
            $table->string('urgency')->default('unknown'); // unknown, routine, urgent, emergency
            $table->json('detected_tags')->nullable();
            
            // Location
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->text('location_description')->nullable();
            
            // Coordination
            $table->unsignedBigInteger('coordinator_id')->nullable();
            $table->text('coordinator_notes')->nullable();
            $table->string('status')->default('pending');
            // pending → accepted → dispatching → in_progress → resolved → cancelled
            
            // Dispatch
            $table->unsignedBigInteger('dispatched_facility_id')->nullable();
            $table->text('dispatch_message')->nullable();
            $table->string('dispatched_service_type')->nullable();
            $table->timestamp('dispatched_at')->nullable();
            $table->string('estimated_arrival')->nullable();
            
            // Resolution
            $table->string('resolution')->nullable();
            $table->timestamp('resolved_at')->nullable();
            
            // Auto-destroy
            $table->timestamp('auto_destroy_at')->nullable();
            
            $table->timestamps();
            
            $table->index('status');
            $table->index('coordinator_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assistance_requests');
    }
};