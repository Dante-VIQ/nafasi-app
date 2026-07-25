<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('missing_person_alerts', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();
            $table->string('name');               // Name or nickname of missing person
            $table->string('age_group')->nullable(); // infant, child, adult, elderly
            $table->string('gender')->nullable();
            $table->text('description');          // Physical description, clothing, etc.
            $table->text('last_seen_location')->nullable();
            $table->text('suspect_description')->nullable(); // Description of suspect if known
            $table->string('photo_path')->nullable();        // Missing person photo (EXIF stripped)
            $table->string('suspect_photo_path')->nullable(); // Suspect photo (EXIF stripped)
            $table->string('contact_phone')->nullable();     // Authority contact, not public
            $table->string('status')->default('active');     // active, found, cancelled
            $table->unsignedBigInteger('reported_by')->nullable();
            $table->timestamp('found_at')->nullable();
            $table->timestamp('expires_at')->nullable();     // Auto-expire after 72h
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('missing_person_alerts');
    }
};
