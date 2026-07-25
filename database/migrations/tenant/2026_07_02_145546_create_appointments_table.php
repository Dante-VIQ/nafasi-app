<?php
// database/migrations/tenant/xxxx_create_appointments_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facility_id')->constrained()->cascadeOnDelete();
            
            // Minimal patient info – no medical history
            $table->string('patient_name');
            $table->string('patient_phone')->nullable();
            $table->string('patient_email')->nullable();
            
            // Appointment details
            $table->dateTime('scheduled_at');
            $table->string('reason')->nullable(); // e.g. "general checkup", "pharmacy refill"
            $table->string('status')->default('pending'); // pending, confirmed, arrived, completed, cancelled
            
            // Metadata
            $table->text('notes')->nullable();
            $table->string('source')->default('nafasi'); // nafasi, phone, walk_in
            $table->uuid('nafasi_session_id')->nullable(); // temporary, not personal
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('facility_id');
            $table->index('scheduled_at');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};