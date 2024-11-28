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
            $table->text('address')->nullable();
            $table->string('pan_number', 255)->nullable();
            $table->string('pan_card_file', 255)->nullable();
            $table->string('profile_img', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agent_details', function (Blueprint $table) {
            $table->dropColumn('address');
            $table->dropColumn('pan_number');
            $table->dropColumn('pan_card_file');
            $table->dropColumn('profile_img');
        });
    }
};
