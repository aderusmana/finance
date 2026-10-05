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
        Schema::create('distributor_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('distributor_id')->constrained('distributors')->cascadeOnDelete();

            // Klasifikasi Dokumen
            $table->enum('doc_type', ['bupot', 'transfer', 'top_insentif'])->index();

            // Periode Dokumen:
            // - Wajib untuk 'bupot' & 'top_insentif' (siklus kalender tahunan Jan-Des)
            // - Nullable untuk 'transfer' (running terus per distributor, bisa dari arsip 2020 s/d sekarang)
            $table->unsignedSmallInteger('year')->nullable()->index(); // Misal: 2026, atau 2020
            $table->unsignedTinyInteger('month')->nullable()->index(); // 1 = Januari, 12 = Desember (NULL untuk transfer)

            // Detail Berkas
            $table->string('title')->nullable(); // Misal: "Transfer Pelunasan Inv-01", "BuPot PPh 23 Jan"
            $table->string('file_name');         // Nama file asli
            $table->string('file_path');         // Path relatif di storage
            $table->string('file_ext', 20);      // pdf, xlsx, png, dll.
            $table->unsignedBigInteger('file_size')->default(0); // Dalam byte
            $table->string('mime_type', 100)->nullable();

            // Kolom Khusus Penjelasan Transfer Running & Catatan
            $table->date('transaction_date')->nullable(); // Tanggal riil transaksi transfer
            $table->text('notes')->nullable();            // Catatan/keterangan transfer berjalan

            // Audit Log Internal
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            // Compound Index untuk Performa Query Cepat:
            // 1. Query Dokumen Bulanan (BuPot & TOP Insentif)
            $table->index(['distributor_id', 'year', 'month'], 'dist_doc_period_idx');
            // 2. Query Penjelasan Transfer Running (Mengambil seluruh file transfer distributor tanpa batas tahun)
            $table->index(['distributor_id', 'doc_type'], 'dist_doc_type_idx');
            // 3. Query Timeline Transfer Urut Tanggal
            $table->index(['distributor_id', 'doc_type', 'transaction_date'], 'dist_doc_trf_timeline_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('distributor_documents');
    }
};
