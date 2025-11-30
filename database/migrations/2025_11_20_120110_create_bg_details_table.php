<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBgDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bg_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bank_garansi_id');
            $table->string('bank_name', 191)->nullable();
            $table->string('branch_name', 191)->nullable();
            $table->text('bank_address')->nullable();
            $table->string('contact_person', 191)->nullable();
            $table->decimal('nominal', 18, 2)->default(0);
            $table->timestamps();

            $table->foreign('bank_garansi_id')->references('id')->on('bank_garansi')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bg_details');
    }
}
