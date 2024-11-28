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
        Schema::create('package_sightseeing', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('itinerary_id');
            $table->string('morning')->nullable();
            $table->string('afternoon')->nullable();
            $table->string('evening')->nullable();
            $table->string('night')->nullable();
            $table->timestamps();

            // Add foreign key constraint
            $table->foreign('itinerary_id')->references('id')->on('itineraries')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('package_sightseeing');
    }
};
