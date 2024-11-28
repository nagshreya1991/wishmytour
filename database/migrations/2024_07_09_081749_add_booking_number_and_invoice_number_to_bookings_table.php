<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBookingNumberAndInvoiceNumberToBookingsTable extends Migration
{
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('booking_number')->unique()->after('customer_id');
            $table->string('invoice_number')->unique()->nullable()->after('booking_number');
        });
    }

    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn('booking_number');
            $table->dropColumn('invoice_number');
        });
    }
}

