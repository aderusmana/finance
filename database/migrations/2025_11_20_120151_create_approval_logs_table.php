<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApprovalLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('approval_logs', function (Blueprint $table) {
            $table->id();
            $table->string('category', 100);
            $table->unsignedBigInteger('related_id');
            $table->string('approver_nik');
            $table->integer('level')->nullable();
            $table->string('status');
            $table->text('notes')->nullable();
            $table->text('token')->nullable();
            $table->timestamps();

            $table->index(['category', 'related_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('approval_logs');
    }
}
