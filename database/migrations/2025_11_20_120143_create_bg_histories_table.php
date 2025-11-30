<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBgHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bg_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bank_garansi_id');
            $table->decimal('previous_nominal', 18, 2)->nullable();
            $table->decimal('new_nominal', 18, 2)->nullable();
            $table->date('previous_exp_date')->nullable();
            $table->date('new_exp_date')->nullable();
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('bank_garansi_id')->references('id')->on('bank_garansi')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bg_histories');
    }
}
