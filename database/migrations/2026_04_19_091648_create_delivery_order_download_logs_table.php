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
        Schema::create('delivery_order_download_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_order_note_id')->constrained('delivery_order_notes')->onDelete('cascade');
            $table->string('downloaded_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_order_download_logs');
    }
};
