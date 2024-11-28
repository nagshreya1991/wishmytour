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
            $table->string('depart_destination', 255)->nullable()->change();
            $table->string('arrive_destination', 255)->nullable()->change();
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
