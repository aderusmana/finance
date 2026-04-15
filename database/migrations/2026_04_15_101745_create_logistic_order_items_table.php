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
        Schema::create('logistic_order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('logistic_order_id');
            $table->string('order_item_code');
            $table->string('order_item_name');
            $table->integer('order_quantity');
            $table->string('order_amount', 20)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logistic_order_items');
    }
};
