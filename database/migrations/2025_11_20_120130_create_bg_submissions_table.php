<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBgSubmissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bg_submissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bg_recommendation_id');
            $table->string('form_code', 100)->unique();
            $table->string('signed_document_path', 500)->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('upload_completed_at')->nullable();
            $table->enum('status', ['pending_print','awaiting_upload','uploaded','reviewed', 'waiting_approval', 'rejected_by_finance', 'completed'])->default('pending_print');
            $table->string('token', 100)->  nullable();
            $table->timestamps();

            $table->foreign('bg_recommendation_id')->references('id')->on('bg_recommendations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bg_submissions');
    }
}
