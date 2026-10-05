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
        Schema::create('distributor_document_downloads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('distributor_document_id')->constrained('distributor_documents')->cascadeOnDelete();
            $table->foreignId('distributor_id')->constrained('distributors')->cascadeOnDelete();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->enum('downloaded_via', ['guest_portal', 'internal'])->default('guest_portal');
            $table->timestamp('downloaded_at')->useCurrent();

            $table->index(['distributor_id', 'downloaded_at'], 'dist_doc_downloaded_at_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('distributor_document_downloads');
    }
};
