<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('interaction_outcomes', function (Blueprint $table) {
            $table->boolean('escalated')->default(false)->after('decision');
            $table->string('escalation_level')->nullable()->after('escalated');
            $table->json('escalation_reasons')->nullable()->after('escalation_level');
            $table->unsignedBigInteger('escalation_handler_id')->nullable()->after('escalation_reasons');
            $table->timestamp('escalation_resolved_at')->nullable()->after('escalation_handler_id');
        });
    }

    public function down(): void
    {
        Schema::table('interaction_outcomes', function (Blueprint $table) {
            $table->dropColumn([
                'escalated', 'escalation_level', 'escalation_reasons',
                'escalation_handler_id', 'escalation_resolved_at',
            ]);
        });
    }
};