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
            $table->date('end_date')->nullable();
            $table->decimal('price', 10, 2)->nullable()->after('add_on_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booking_on_requests', function (Blueprint $table) {
            $table->dropColumn('end_date');
            $table->dropColumn('price');
        });
    }
};
