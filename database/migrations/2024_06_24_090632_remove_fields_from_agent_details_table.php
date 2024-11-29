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
            $table->dropColumn('authorization_letter');
            $table->dropColumn('authorization_file');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agent_details', function (Blueprint $table) {
            $table->string('authorization_letter')->nullable();
            $table->string('authorization_file')->nullable();
        });
    }
};
