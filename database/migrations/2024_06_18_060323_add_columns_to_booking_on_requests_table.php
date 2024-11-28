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
        Schema::table('booking_on_requests', function (Blueprint $table) {
            $table->decimal('base_amount', 10, 2)->nullable();
            $table->decimal('addon_total_price', 10, 2)->nullable();	
            $table->decimal('gst_percent', 10, 2)->nullable();
            $table->decimal('gst_price', 10, 2)->nullable();
            $table->decimal('tcs', 10, 2)->nullable();
            $table->decimal('final_price', 10, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booking_on_requests', function (Blueprint $table) {
            $table->dropColumn('addon_total_price');
            $table->dropColumn('gst_percent');
            $table->dropColumn('gst_price');
            $table->dropColumn('tcs');
            $table->dropColumn('final_price');
        });
    }
};
