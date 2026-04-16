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
        Schema::dropIfExists('delivery_order_notes');

        Schema::create('delivery_order_notes', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('logistic_order_id');
            $table->foreign('logistic_order_id')
                  ->references('id')
                  ->on('logistic_orders')
                  ->onDelete('cascade');

            $table->string('delivery_order_no');
            $table->string('status')->default('Pending');
            $table->integer('download_count')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_order_notes');
    }
};
