<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('interaction_outcomes', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();
            $table->string('tenant_id')->nullable()->index();    // which tenant served this request
            $table->string('session_id');                        // temporary, not linked to user
            $table->text('user_text');                           // original text (will be anonymized)
            $table->string('language', 10)->nullable();
            $table->json('intent')->nullable();                  // classified intent (array)
            $table->float('confidence')->default(0);
            $table->json('facility_hints')->nullable();          // suggested facility types
            $table->unsignedBigInteger('recommended_facility_id')->nullable(); // first facility shown
            $table->string('outcome_type')->nullable();          // booked, called, directions, dispatched, none
            $table->unsignedBigInteger('outcome_facility_id')->nullable(); // which facility user actually chose
            $table->boolean('was_correct')->nullable();          // verified by coordinator
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->text('verification_notes')->nullable();
            $table->text('anonymized_text')->nullable();         // text with names/phones replaced
            $table->timestamps();

            $table->index('session_id');
            $table->index('outcome_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('interaction_outcomes');
    }
};