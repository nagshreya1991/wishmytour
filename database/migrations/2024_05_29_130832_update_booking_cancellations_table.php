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
            $table->dropColumn('cancellation_date');
            $table->dropColumn('cancel_approval_date');
            $table->tinyInteger('status')->default(0)->after('cancellation_type'); // Replace 'some_existing_column' with the appropriate column name that comes before this new column
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booking_cancellations', function (Blueprint $table) {
            $table->dateTime('cancellation_date')->nullable();
            $table->dateTime('cancel_approval_date')->nullable();
            $table->dropColumn('status');
        });
    }
};
