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
        Schema::rename('type_of_vendor', 'vendor_type');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('vendor_type', 'type_of_vendor');
    }
};
