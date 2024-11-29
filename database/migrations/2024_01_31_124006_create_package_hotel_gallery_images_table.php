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
        Schema::create('package_hotel_gallery_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hotel_id');
            $table->string('path');
            $table->timestamps();

            // Foreign key relationship with the 'package_hotel' table
            $table->foreign('hotel_id')->references('id')->on('package_hotel')->onDelete('cascade');

            // Indexes
            $table->index('hotel_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('package_hotel_gallery_images');
    }
};
