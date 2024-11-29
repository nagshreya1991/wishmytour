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
        Schema::create('vendor_directors', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vendor_id');
            $table->string('director_name', 100);
            $table->string('phone_number', 100);
            $table->string('pan_number', 100);
            $table->string('address', 255);
            $table->timestamps();
            
            // Define foreign key constraint
            $table->foreign('vendor_id')->references('id')->on('vendor_details')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_directors');
    }
};
