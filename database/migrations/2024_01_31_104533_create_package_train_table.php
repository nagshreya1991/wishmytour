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
        Schema::create('package_train', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('itinerary_id');
            $table->foreign('itinerary_id')->references('id')->on('itineraries')->onDelete('cascade');
            $table->string('train_name');
            $table->string('train_number');
            $table->string('class');
            $table->string('from_station');
            $table->string('to_station');
            $table->timestamp('depart_datetime')->nullable()->default(now());
            $table->timestamp('arrive_datetime')->nullable()->default(now());
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('package_train');
    }
};
