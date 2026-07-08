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
        Schema::table('logistic_orders', function (Blueprint $table) {
            $table->dropColumn(['period', 'delivery_to', 'status', 'route_to']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('logistic_orders', function (Blueprint $table) {
            $table->date('delivery_to')->nullable();
            $table->string('period')->nullable();
            $table->string('status')->nullable();
            $table->string('route_to')->nullable();
        });
    }
};
