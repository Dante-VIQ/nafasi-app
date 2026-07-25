<?php
// database/migrations/tenant/xxxx_create_community_responders_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('community_responders', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();
            $table->string('name');
            $table->string('phone')->unique();
            $table->string('qualification'); // CHW, Nurse, Clinical Officer, First Responder
            $table->json('capabilities'); // ["snakebite", "cpr", "first_aid", "maternal"]
            $table->json('languages'); // ["sw", "en", "kikuyu"]
            $table->decimal('base_latitude', 10, 7);
            $table->decimal('base_longitude', 10, 7);
            $table->decimal('current_latitude', 10, 7)->nullable();
            $table->decimal('current_longitude', 10, 7)->nullable();
            $table->string('village')->nullable();
            $table->string('ward')->nullable();
            $table->string('status')->default('offline'); // offline, available, responding
            $table->boolean('has_emergency_kit')->default(false);
            $table->boolean('is_verified')->default(false);
            $table->integer('total_emergencies_responded')->default(0);
            $table->integer('lives_saved')->default(0);
            $table->float('rating')->default(5.0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('community_responders');
    }
};