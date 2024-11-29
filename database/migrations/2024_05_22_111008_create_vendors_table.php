<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->tinyInteger('type')->default(1);
            $table->boolean('have_gst')->default(false);
            $table->string('gst_number')->nullable();
            $table->string('pan_number')->nullable();
            $table->string('gst_certificate_file')->nullable();
            $table->string('pan_card_file')->nullable();
            $table->string('photo')->nullable();
            $table->decimal('gst_rate', 8, 2)->nullable();
            $table->decimal('tcs_rate', 8, 2)->nullable();
            $table->string('organization_name')->nullable();
            $table->tinyInteger('organization_type')->default(1)->comment('1: Proprietorship, 2: Partnership, 3: Private Limited');
            $table->text('address')->nullable();
            $table->string('zip_code')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->string('status')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('ifsc_code')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('branch_name')->nullable();
            $table->string('contact_person_name')->nullable();
            $table->string('cancelled_cheque')->nullable();
            $table->string('authorization_letter')->nullable();
            $table->boolean('bank_verified')->default(false);
            $table->boolean('is_verified')->default(false);
            $table->boolean('gst_verified')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vendors');
    }
}

