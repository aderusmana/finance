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
        Schema::create('logistic_fee_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('distributor_customer_id')->constrained('distributor_customers')->onDelete('cascade');
            $table->decimal('old_fee', 15, 2)->default(0);
            $table->decimal('new_fee', 15, 2)->default(0);
            $table->string('status', 50); // Approved, Rejected
            $table->string('action_by', 50)->nullable(); // NIK user yang melakukan action
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logistic_fee_logs');
    }
};
