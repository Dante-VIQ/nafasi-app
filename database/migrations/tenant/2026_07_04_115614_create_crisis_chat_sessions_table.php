<?php
// database/migrations/tenant/xxxx_create_crisis_chat_sessions_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crisis_chat_sessions', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();
            
            // Anonymous session — no personal data
            $table->string('session_token', 128)->unique(); // Only identifier
            $table->string('crisis_type')->nullable();
            $table->string('language', 10)->default('sw');
            $table->string('status')->default('waiting'); // waiting, connected, ended
            
            // Counselor assignment
            $table->unsignedBigInteger('counselor_id')->nullable();
            $table->timestamp('connected_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            
            // Metadata (anonymized)
            $table->string('general_area')->nullable(); // "Nairobi" — never precise
            $table->string('communication_method')->default('chat'); // chat, voice
            
            // Auto-destroy
            $table->timestamp('auto_destroy_at')->nullable();
            
            $table->timestamps();
            
            $table->index('session_token');
            $table->index('status');
        });
        
        // Chat messages — stored encrypted, destroyed with session
        Schema::create('crisis_chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('crisis_chat_sessions')->cascadeOnDelete();
            $table->text('content_encrypted'); // Encrypted message
            $table->string('sender_type'); // user, counselor
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crisis_chat_messages');
        Schema::dropIfExists('crisis_chat_sessions');
    }
};