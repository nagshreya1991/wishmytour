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
        Schema::create('package_flight', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('itinerary_id');
            $table->unsignedBigInteger('depart_destination');
            $table->unsignedBigInteger('arrive_destination');
            $table->timestamp('depart_datetime')->default(now());
            $table->timestamp('arrive_datetime')->default(now());
            $table->timestamps();

            // Foreign key relationship with itineraries table
            $table->foreign('itinerary_id')->references('id')->on('itineraries')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('package_flight');
    }
};
