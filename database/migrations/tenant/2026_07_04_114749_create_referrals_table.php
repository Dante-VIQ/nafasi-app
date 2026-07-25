<?php
// database/migrations/tenant/xxxx_create_referrals_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('referrals', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();
            
            // Referring facility (sender)
            $table->foreignId('referring_facility_id')->constrained('facilities')->cascadeOnDelete();
            $table->string('referring_staff_name')->nullable();
            $table->string('referring_staff_role')->nullable();
            
            // Receiving facility
            $table->foreignId('receiving_facility_id')->constrained('facilities')->cascadeOnDelete();
            
            // Patient info (MINIMAL — only what's needed for transfer)
            $table->string('patient_reference_id')->nullable();
            $table->string('patient_gender')->nullable();
            $table->string('patient_age_group')->nullable(); // infant, child, adult, elderly
            $table->boolean('patient_is_stable')->default(true);
            $table->boolean('requires_ambulance')->default(false);
            
            // Referral details
            $table->string('referral_type'); // surgery, maternal_emergency, icu, pediatric, etc.
            $table->string('urgency')->default('routine'); // immediate, urgent, routine
            $table->text('reason_for_referral');
            $table->text('clinical_summary')->nullable();
            $table->text('treatment_given')->nullable();
            $table->text('additional_notes')->nullable();
            
            // Status
            $table->string('status')->default('pending');
            // pending → accepted → patient_en_route → arrived → admitted → completed
            $table->text('rejection_reason')->nullable();
            $table->foreignId('redirected_to_facility_id')->nullable()->constrained('facilities')->nullOnDelete();
            
            // Timing
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('patient_departed_at')->nullable();
            $table->timestamp('estimated_arrival_at')->nullable();
            $table->timestamp('arrived_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            
            // Communication
            $table->string('referral_reference_code')->unique();
            $table->json('communication_log')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('status');
            $table->index('referral_type');
            $table->index('urgency');
        });
        
        Schema::create('referral_feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referral_id')->constrained()->cascadeOnDelete();
            $table->foreignId('facility_id')->constrained('facilities')->cascadeOnDelete();
            $table->boolean('was_appropriate')->default(true);
            $table->boolean('patient_accepted')->default(true);
            $table->text('feedback_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referral_feedback');
        Schema::dropIfExists('referrals');
    }
};