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
        Schema::create('logistic_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('distributor_id');
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('customer_ship_to_id');
            $table->foreign('distributor_id')->references('id')->on('distributors');
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->foreign('customer_ship_to_id')->references('id')->on('customer_ship_toes');
            $table->integer('logistic_order_no');
            $table->date('delivery_date');
            $table->date('delivery_to');
            $table->string('period');
            $table->string('status');
            $table->string('route_to');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logistic_orders');
    }
};
