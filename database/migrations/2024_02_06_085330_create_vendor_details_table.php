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
        Schema::create('vendor_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('fullname', 100)->nullable();
            $table->string('type_of_vendor', 100)->nullable();
            $table->string('address', 255)->nullable();
            $table->string('pancard', 255)->nullable();
            $table->string('pan_number', 255)->nullable();
            $table->tinyInteger('have_gst')->default(0); // 0=no, 1=yes
            $table->string('gst_number', 255)->nullable();
            $table->string('organization_name', 255)->nullable();
            $table->tinyInteger('organization_type')->default(0); // 1=proprietorship, 2=partnership, 3=private limited
            $table->string('proprietor_name', 255)->nullable();
            $table->unsignedBigInteger('proprietor_phone_number')->nullable();
            $table->string('proprietor_pan', 255)->nullable();
            $table->string('proprietor_address', 255)->nullable();
            $table->integer('no_of_partners')->nullable();
            $table->integer('no_of_directors')->nullable();
            $table->tinyInteger('status')->default(0); // default 0
            $table->timestamps();
            
            // Add foreign key constraint if needed
             $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_details');
    }
};
