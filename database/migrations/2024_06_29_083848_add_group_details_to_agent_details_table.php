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
            $table->decimal('group_percentage', 10, 2)->nullable()->after('agent_code'); 
            $table->string('group_name', 255)->nullable()->after('group_percentage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agent_details', function (Blueprint $table) {
            $table->dropColumn('group_percentage');
            $table->dropColumn('group_name');
        });
    }
};
