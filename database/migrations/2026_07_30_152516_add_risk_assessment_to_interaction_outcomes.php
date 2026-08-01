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
        Schema::table('interaction_outcomes', function (Blueprint $table) {
            $table->json('risk_assessment')->nullable()->after('facility_hints');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('interaction_outcomes', function (Blueprint $table) {
            //
        });
    }
};
