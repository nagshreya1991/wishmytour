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
        Schema::create('booking_customer_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booking_id');
            $table->unsignedBigInteger('package_id');
            $table->string('name', 100);
            $table->string('address', 255);
            $table->string('email', 100);
            $table->string('phone_number', 100);
            $table->unsignedBigInteger('state_id');
            $table->string('pan_number', 100);
            $table->tinyInteger('booking_for')->default(0)->comment('0->myself or 1->others');
            $table->timestamps();

            // Foreign keys
            $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('cascade');
           
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_customer_details');
    }
};
