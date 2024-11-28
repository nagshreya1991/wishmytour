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
        Schema::table('package_flight', function (Blueprint $table) {
           // Make depart_destination and arrive_destination columns nullable
           $table->unsignedBigInteger('depart_destination')->nullable()->change();
           $table->unsignedBigInteger('arrive_destination')->nullable()->change();

           // Make depart_datetime and arrive_datetime columns nullable
           $table->timestamp('depart_datetime')->nullable()->change();
           $table->timestamp('arrive_datetime')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('package_flight', function (Blueprint $table) {
            //
        });
    }
};
