<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dictionary_entries', function (Blueprint $table) {
            $table->id();
            $table->string('word');
            $table->string('word_normalized');
            $table->string('language', 10);              // sw, en, sheng
            $table->string('part_of_speech')->nullable();
            $table->text('definition')->nullable();
            $table->json('meanings')->nullable();         // all meanings
            $table->string('source')->default('kamusi');
            $table->json('tags')->nullable();             // ['emergency','fire','pharmacy']
            $table->float('emergency_weight')->default(0);
            $table->string('facility_type_hint')->nullable();
            $table->boolean('is_crisis_signal')->default(false);
            $table->boolean('is_help_come_to_me_signal')->default(false);
            $table->boolean('is_emergency_signal')->default(false);
            $table->json('synonyms')->nullable();
            $table->integer('usage_count')->default(0);
            $table->float('confidence_score')->default(1.0);
            $table->timestamps();

            $table->index('word_normalized');
            $table->index('language');
            $table->fullText(['word', 'definition']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dictionary_entries');
    }
};