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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('description');
            $table->enum('discount_type', ['percentage', 'amount'])
                ->default('percentage')
                ->comment('percentage or amount');
            $table->decimal('discount_amount', 10, 2)->nullable();
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->tinyInteger('status')->default(0);
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
