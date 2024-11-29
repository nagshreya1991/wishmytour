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
        Schema::table('packages', function (Blueprint $table) {
            $table->integer('is_transport')->default(0);
            $table->integer('is_flight')->default(0);
            $table->integer('is_train')->default(0);
            $table->integer('is_hotel')->default(0);
            $table->integer('is_meal')->default(0);
            $table->integer('is_sightseeing')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn('is_transport');
            $table->dropColumn('is_flight');
            $table->dropColumn('is_train');
            $table->dropColumn('is_hotel');
            $table->dropColumn('is_meal');
            $table->dropColumn('is_sightseeing');
        });
    }
};
