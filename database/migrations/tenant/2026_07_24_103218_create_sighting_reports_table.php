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
Schema::create('sighting_reports', function (Blueprint $table) {
    $table->id();
    $table->uuid()->unique();
    $table->foreignId('missing_person_alert_id')->constrained()->cascadeOnDelete();
    $table->decimal('latitude', 10, 7)->nullable();
    $table->decimal('longitude', 10, 7)->nullable();
    $table->text('notes')->nullable();
    $table->string('reporter_session_id'); // anonymous, not linked to user
    $table->timestamp('reported_at')->nullable();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sighting_reports');
    }
};
