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
        Schema::create('booking_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vendor_id');
            $table->unsignedBigInteger('package_id');
            $table->string('package_name')->nullable();
            $table->timestamp('run_date')->nullable();
            $table->text('message')->nullable(); 
            $table->timestamps();

            // Define foreign key constraints
           // $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('cascade');
          //  $table->foreign('package_id')->references('id')->on('packages')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_messages');
    }
};
