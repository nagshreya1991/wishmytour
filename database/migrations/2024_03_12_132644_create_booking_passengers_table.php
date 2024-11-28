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
        Schema::create('booking_passengers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booking_id');
            $table->unsignedBigInteger('package_id');
            $table->unsignedBigInteger('booking_room_id');
            $table->string('title', 100);
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->date('dob');
            $table->string('gender', 100);
            $table->timestamps();

            // Foreign key
            $table->foreign('booking_room_id')->references('id')->on('booking_room')->onDelete('cascade');
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_passengers');
    }
};
