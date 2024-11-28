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
        Schema::table('bookings', function (Blueprint $table) {
            $table->decimal('base_price', 10, 2)->nullable();
            $table->decimal('addon_total_price', 10, 2)->nullable();
            $table->decimal('website_price', 10, 2)->nullable();
            $table->decimal('website_percent', 10, 2)->nullable();
            $table->decimal('gst_price', 10, 2)->nullable();
            $table->decimal('gst_percent', 10, 2)->nullable();
            $table->decimal('tcs', 10, 2)->nullable();
            $table->decimal('coupon_price', 10, 2)->nullable();
            $table->decimal('final_price', 10, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'base_price',
                'addon_total_price',
                'gst',
                'tcs',
                'coupon_price',
                'final_price',
            ]);
        });
    }
};
