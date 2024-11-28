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
        Schema::table('customer_details', function (Blueprint $table) {
            $table->string('id_type')->nullable()->after('zipcode');
            $table->string('id_number')->nullable()->after('id_type');
            $table->tinyInteger('id_verified')->default(0)->after('id_number'); // 0=not verified, 1=verified
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_details', function (Blueprint $table) {
            $table->dropColumn(['id_type', 'id_number', 'id_verified']);
        });
    }
};
