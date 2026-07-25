<?php
// database/migrations/tenant/xxxx_create_facility_congestion_logs_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facility_congestion_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facility_id')->constrained()->cascadeOnDelete();
            $table->string('status');
            $table->string('source')->default('manual');
            $table->unsignedBigInteger('reported_by')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['facility_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facility_congestion_logs');
    }
};