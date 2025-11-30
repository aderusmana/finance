<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCreditLimitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('credit_limits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('bank_garansi_id')->nullable();
            $table->unsignedBigInteger('recommendation_id')->nullable();
            $table->decimal('requested_limit', 18, 2)->nullable();
            $table->decimal('approved_limit', 18, 2)->nullable();
            $table->unsignedBigInteger('lampiran_d_version_id')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('bank_garansi_id')->references('id')->on('bank_garansi')->onDelete('set null');
            $table->foreign('recommendation_id')->references('id')->on('bg_recommendations')->onDelete('set null');
            $table->foreign('lampiran_d_version_id')->references('id')->on('lampiran_d_versions')->onDelete('set null');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('credit_limits');
    }
}
