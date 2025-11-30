<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBankGaransiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bank_garansi', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->string('bg_number', 191);
            $table->enum('bg_type', ['existing', 'extension', 'new'])->default('new');
            $table->unsignedBigInteger('base_bg_id')->nullable();
            $table->decimal('bg_nominal', 18, 2)->default(0);
            $table->date('issued_date')->nullable();
            $table->date('exp_date')->nullable();
            $table->enum('status', ['draft','sent_to_customer','submitted','reviewed','approved','expired'])->default('draft');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('base_bg_id')->references('id')->on('bank_garansi')->onDelete('set null');
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
        Schema::dropIfExists('bank_garansi');
    }
}
