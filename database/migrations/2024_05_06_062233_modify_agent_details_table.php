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
        Schema::table('agent_details', function (Blueprint $table) {
            // Drop the 'fullname' column
            $table->dropColumn('fullname');
            // Add 'first_name' and 'last_name' columns
            $table->string('first_name')->nullable()->after('user_id');
            $table->string('last_name')->nullable()->after('first_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agent_details', function (Blueprint $table) {
            // Re-add the 'fullname' column
            $table->string('fullname')->nullable()->after('user_id');
            // Remove the 'first_name' and 'last_name' columns
            $table->dropColumn(['first_name', 'last_name']);
        });
    }
};
