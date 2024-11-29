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
        Schema::table('booking_cancellations', function (Blueprint $table) {
            $table->integer('no_of_pax')->nullable()->after('booking_price');
            $table->decimal('pax_charge', 10, 2)->nullable()->after('no_of_pax');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booking_cancellations', function (Blueprint $table) {
            $table->dropColumn('no_of_pax');
            $table->dropColumn('pax_charge');
        });
    }
};
