<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLampiranDTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lampiran_d', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bg_submission_id');
            $table->integer('version_latest')->default(0);
            $table->unsignedBigInteger('active_version_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('bg_submission_id')->references('id')->on('bg_submissions')->onDelete('cascade');
            // `active_version_id` will be linked to `lampiran_d_versions` after that table is created
            // to avoid circular foreign key creation issues during migrations.
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
        Schema::dropIfExists('lampiran_d');
        
    }
}
