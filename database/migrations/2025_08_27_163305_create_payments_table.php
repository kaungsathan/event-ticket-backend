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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "Cash", "KBZ Pay", "MPU", "Credit Card"
            $table->string('code')->unique(); // e.g., "cash", "kbz", "mpu", "credit_card"
            $table->text('description')->nullable(); // Additional details about the payment method
            $table->string('type')->default('digital'); // 'cash', 'digital', 'bank_transfer', 'mobile_payment'
            $table->boolean('is_active')->default(true);
            $table->decimal('processing_fee_percentage', 5, 2)->default(0); // e.g., 2.50 for 2.5%
            $table->decimal('processing_fee_fixed', 10, 2)->default(0); // Fixed fee amount
            $table->integer('sort_order')->default(0); // For ordering in UI
            $table->json('settings')->nullable(); // Additional configuration like API keys, etc.
            $table->string('icon')->nullable(); // Icon or image for the payment method
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
