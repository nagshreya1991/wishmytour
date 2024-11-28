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
    { Schema::create('booking_commissions', function (Blueprint $table) {
        $table->id();
        $table->unsignedInteger('user_id');
        $table->unsignedInteger('booking_id');
        $table->decimal('commission', 10, 2)->nullable();
        $table->decimal('group_percentage', 10, 2)->nullable();
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_commissions');
    }
};
