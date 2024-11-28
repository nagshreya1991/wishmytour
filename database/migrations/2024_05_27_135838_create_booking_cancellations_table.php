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
        Schema::create('booking_cancellations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booking_id');
            $table->unsignedBigInteger('customer_id');
            $table->dateTime('cancellation_date');
            $table->text('cancellation_reason')->nullable();
            $table->enum('cancellation_type', ['partial', 'full']);
            $table->decimal('booking_price', 10, 2)->nullable();
            $table->decimal('cancellation_percent', 10, 2)->nullable();
            $table->decimal('cancellation_charge', 10, 2)->nullable();
            $table->decimal('website_percent', 10, 2)->nullable();
            $table->decimal('website_charge', 10, 2)->nullable();
            $table->decimal('gst_percent', 10, 2)->nullable();
            $table->decimal('gst_charge', 10, 2)->nullable();
            $table->decimal('refund_amount', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_cancellations');
    }
};
