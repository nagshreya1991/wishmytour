<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePackageBulkDiscountsTable extends Migration
{
    public function up()
    {
        Schema::create('package_bulk_discounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('package_id');
            $table->integer('min_pax');
            $table->decimal('discount', 8, 2);
            $table->timestamps();

            $table->foreign('package_id')->references('id')->on('packages')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('package_bulk_discounts');
    }
}
