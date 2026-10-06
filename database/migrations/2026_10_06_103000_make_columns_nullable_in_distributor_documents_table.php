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
        Schema::table('distributor_documents', function (Blueprint $table) {
            $table->enum('doc_type', ['bupot', 'transfer', 'top_insentif'])->nullable()->change();
            $table->string('file_name')->nullable()->change();
            $table->string('file_path')->nullable()->change();
            $table->string('file_ext', 20)->nullable()->change();
            $table->string('mime_type', 100)->nullable()->change();

            $table->index(['distributor_id', 'year'], 'dist_doc_distributor_year_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('distributor_documents', function (Blueprint $table) {
            $table->dropIndex('dist_doc_distributor_year_idx');

            $table->enum('doc_type', ['bupot', 'transfer', 'top_insentif'])->nullable(false)->change();
            $table->string('file_name')->nullable(false)->change();
            $table->string('file_path')->nullable(false)->change();
            $table->string('file_ext', 20)->nullable(false)->change();
        });
    }
};
