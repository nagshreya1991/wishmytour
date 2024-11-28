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
        Schema::create('package_hotel', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('itinerary_id');
            $table->string('name');
            $table->tinyInteger('rating')->unsigned()->nullable();
            $table->timestamps();

            // Foreign key relationship with the 'itineraries' table
            $table->foreign('itinerary_id')->references('id')->on('itineraries')->onDelete('cascade');

            // Indexes
            $table->index('itinerary_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('package_hotel');
    }
};
