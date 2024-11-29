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
            $table->integer('child_discount')->nullable();
            $table->decimal('single_occupancy_cost', 10, 2)->nullable();
            $table->dateTime('offseason_from_date')->nullable();
            $table->dateTime('offseason_to_date')->nullable();
            $table->decimal('offseason_price', 10, 2)->nullable();
            $table->dateTime('onseason_from_date')->nullable();
            $table->dateTime('onseason_to_date')->nullable();
            $table->decimal('onseason_price', 10, 2)->nullable();
            $table->integer('total_seat')->nullable();
            $table->integer('bulk_no_of_pax')->nullable();
            $table->integer('pax_discount_percent')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn([
                'child_discount',
                'single_occupancy_cost',
                'offseason_from_date',
                'offseason_to_date',
                'offseason_price',
                'onseason_from_date',
                'onseason_to_date',
                'onseason_price',
                'total_seat',
                'bulk_no_of_pax',
                'pax_discount_percent',
            ]);
        });
    }
};
