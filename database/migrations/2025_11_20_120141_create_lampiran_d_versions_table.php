<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLampiranDVersionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lampiran_d_versions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lampiran_d_id');
            $table->integer('version_no')->default(1);
            $table->json('data_snapshot')->nullable();
            $table->string('file_path', 500)->nullable();
            $table->unsignedBigInteger('generated_by')->nullable();
            $table->timestamp('generated_at')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->foreign('lampiran_d_id')->references('id')->on('lampiran_d')->onDelete('cascade');
            $table->foreign('generated_by')->references('id')->on('users')->onDelete('set null');
        });

        // Now that `lampiran_d_versions` exists, add the foreign key on `lampiran_d.active_version_id`
        if (Schema::hasTable('lampiran_d')) {
            Schema::table('lampiran_d', function (Blueprint $table) {
                $table->foreign('active_version_id')->references('id')->on('lampiran_d_versions')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Remove foreign key from lampiran_d if exists
        if (Schema::hasTable('lampiran_d')) {
            Schema::table('lampiran_d', function (Blueprint $table) {
                $sm = Schema::getConnection()->getDoctrineSchemaManager();
                // drop foreign if exists (Laravel uses column based drop)
                try {
                    $table->dropForeign(['active_version_id']);
                } catch (\Exception $e) {
                    // ignore if constraint doesn't exist
                }
            });
        }

        Schema::dropIfExists('lampiran_d_versions');
    }
}
