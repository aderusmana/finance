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
        Schema::create('distributor_document_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('distributor_document_id')->constrained('distributor_documents')->cascadeOnDelete();
            $table->foreignId('distributor_id')->constrained('distributors')->cascadeOnDelete();

            // Document Classification
            $table->enum('doc_type', ['bupot', 'transfer', 'top_insentif'])->index();

            // Document Period:
            // - Required for 'bupot' & 'top_insentif' (annual calendar cycle Jan-Dec: 1-12)
            // - Nullable for 'transfer' (running continuously per distributor)
            $table->unsignedTinyInteger('month')->nullable()->index();

            // Physical File Details
            $table->string('title')->nullable();
            $table->string('file_name');
            $table->string('file_path');
            $table->string('file_ext', 20);
            $table->unsignedBigInteger('file_size')->default(0);
            $table->string('mime_type', 100)->nullable();

            // Specific columns for running Transfer Explanation & Notes
            $table->date('transaction_date')->nullable();
            $table->text('notes')->nullable();

            // Uploader Audit Log
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            // Indexes for fast querying
            $table->index(['distributor_document_id', 'doc_type', 'month'], 'dist_doc_att_period_idx');
            $table->index(['distributor_id', 'doc_type'], 'dist_doc_att_type_idx');
            $table->index(['distributor_id', 'doc_type', 'transaction_date'], 'dist_doc_att_trf_timeline_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('distributor_document_attachments');
    }
};
