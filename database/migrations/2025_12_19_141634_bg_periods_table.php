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
        Schema::create('bg_periods', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bg_recommendation_id');
            $table->date('period_date');
            $table->decimal('amount', 18, 2)->default(0);
            $table->timestamps();

            $table->foreign('bg_recommendation_id')
                  ->references('id')->on('bg_recommendations')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bg_periods');
    }
};