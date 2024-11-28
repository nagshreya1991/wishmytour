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
        Schema::table('package_message', function (Blueprint $table) {
            $table->enum('is_read', ['0', '1'])->default('0')->after('message');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('package_message', function (Blueprint $table) {
            $table->dropColumn('is_read');
        });
    }
};
