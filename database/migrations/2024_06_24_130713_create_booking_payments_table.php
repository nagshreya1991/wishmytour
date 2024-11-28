<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingPaymentsTable extends Migration
{
    public function up()
    {
        Schema::create('booking_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->decimal('total_amount', 10, 2); // Example decimal format, adjust as needed
            $table->decimal('paid_amount', 10, 2); // Example decimal format, adjust as needed
            $table->string('payment_type')->nullable();
            $table->timestamp('payment_date')->nullable();
            // Add other columns as needed
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('booking_payments');
    }
}
