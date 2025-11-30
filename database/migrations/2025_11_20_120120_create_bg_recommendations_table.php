<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBgRecommendationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bg_recommendations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->decimal('average', 18, 2)->nullable();
            $table->decimal('average_increase_percent', 5, 2)->nullable();
            $table->integer('top')->nullable();
            $table->integer('lead_time')->nullable();
            $table->decimal('inflation', 5, 2)->nullable();
            $table->decimal('increase_percent', 5, 2)->nullable();
            $table->decimal('recommended_credit_limit', 18, 2)->nullable();
            $table->decimal('rounded_credit_limit', 18, 2)->nullable();
            $table->decimal('fk_with_limit', 18, 2)->nullable();
            $table->decimal('current_bg', 18, 2)->nullable();
            $table->decimal('set_bg', 18, 2)->nullable();
            $table->decimal('credit_limit_updated', 18, 2)->nullable();
            $table->enum('status', ['draft','pending','approved','rejected'])->default('draft');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bg_recommendations');
    }
}
