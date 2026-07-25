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
Schema::table('facilities', function (Blueprint $table) {
    $table->string('mpesa_business_shortcode')->nullable(); // Paybill or Till
    $table->string('payment_phone')->nullable();            // For STK Push
    $table->string('last_payment_reference')->nullable();
    $table->timestamp('last_payment_at')->nullable();
    $table->timestamp('subscription_expires_at')->nullable();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('facilities', function (Blueprint $table) {
            //
        });
    }
};
