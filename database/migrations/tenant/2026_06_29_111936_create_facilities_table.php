<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facilities', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('facility_type');
            $table->text('description')->nullable();
            $table->text('public_description')->nullable();
            
            // Contact
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('emergency_phone')->nullable();
            $table->string('website')->nullable();
            
            // Location
            $table->text('address');
            $table->string('landmark')->nullable();
            $table->string('city')->nullable();
            $table->string('county')->nullable();
            $table->string('postal_code')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            
            // Operations
            $table->json('operating_hours')->nullable();
            $table->boolean('is_24_hours')->default(false);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_public')->default(true);
            
            // Self-definition (core)
            $table->json('capabilities')->nullable();
            $table->json('emergency_keywords')->nullable();
            $table->json('exclusion_keywords')->nullable();
            $table->text('emergency_definition')->nullable();
            $table->text('exclusion_definition')->nullable();
            $table->string('emergency_level')->default('standard');
            
            // Overflow
       $table->unsignedBigInteger('overflow_facility_id')->nullable();
            
            // Dispatch capability
            $table->boolean('can_dispatch_to_patient')->default(false);
            $table->string('dispatch_service_type')->nullable();
            $table->string('typical_response_time')->nullable();
            $table->integer('dispatch_radius_km')->nullable();
            
            // Health system level
            $table->integer('health_system_level')->nullable();
            
            // Referral
            $table->boolean('accepts_referrals')->default(false);
            $table->boolean('accepting_referrals_now')->default(true);
            $table->json('accepted_referral_types')->nullable();
            $table->string('referral_congestion_status')->nullable();
            $table->timestamp('referral_congestion_updated_at')->nullable();
            $table->boolean('requires_referral_letter')->default(false);
            $table->boolean('accepts_self_referral')->default(true);
            
            // Registration
            $table->string('registration_status')->default('draft');
           $table->unsignedBigInteger('verified_by')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->text('verification_notes')->nullable();
            $table->string('license_document_path')->nullable();
            $table->date('license_expiry')->nullable();
            
            // Subscription
            $table->string('subscription_tier')->default('free');
            $table->string('subscription_status')->default('inactive');
            $table->timestamp('trial_ends_at')->nullable();
            
            // Congestion
            $table->string('congestion_status')->nullable();
            $table->timestamp('congestion_updated_at')->nullable();
            $table->integer('routing_priority')->default(0);
            
            // Meta
            $table->json('languages')->nullable();
            $table->json('accepted_payment')->nullable();
$table->unsignedBigInteger('created_by')->nullable();
$table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['latitude', 'longitude']);
            $table->index('facility_type');
            $table->index('congestion_status');
            $table->index('registration_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facilities');
    }
};