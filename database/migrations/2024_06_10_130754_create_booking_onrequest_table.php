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
        Schema::create('booking_on_requests', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('package_id')->unsigned();
            $table->bigInteger('customer_id')->unsigned();
            $table->string('token')->nullable();
            $table->string('room_details')->nullable();
            $table->integer('status')->default(0);
            $table->string('add_on_id')->nullable();
            $table->date('start_date')->nullable(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_onrequest');
    }
};
