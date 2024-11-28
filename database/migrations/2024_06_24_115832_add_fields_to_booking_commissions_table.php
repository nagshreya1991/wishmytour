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
        Schema::table('booking_commissions', function (Blueprint $table) {
            $table->integer('payment_status')->default(0)->comment('0->Pending, 1->Processing, 2->Paid');
            $table->integer('claim_status')->default(0)->comment('0->Claim Commission, 1->Off Claim Commission, 2->Claimed, 3->Withdrawn, 4->Cancelled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booking_commissions', function (Blueprint $table) {
            $table->dropColumn('payment_status');
            $table->dropColumn('claim_status');
        });
    }
};
