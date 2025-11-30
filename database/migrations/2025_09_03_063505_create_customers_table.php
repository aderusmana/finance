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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique()->nullable();
            $table->string('name', 255);
            $table->string('customer_class', 100)->nullable();
            $table->string('account_group', 100)->nullable();
            $table->string('address1', 255)->nullable();
            $table->string('address2', 255)->nullable();
            $table->string('address3', 255)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->string('country', 100)->nullable();
            $table->string('shipping_to_name', 255)->nullable();
            $table->string('shipping_to_address', 255)->nullable();
            $table->string('purchasing_manager_name', 255)->nullable();
            $table->string('purchasing_manager_email', 255)->nullable();
            $table->string('finance_manager_name', 255)->nullable();
            $table->string('finance_manager_email', 255)->nullable();
            $table->string('penagihan_nama_kontak', 255)->nullable();
            $table->string('penagihan_telepon', 50)->nullable();
            $table->string('penagihan_address', 255)->nullable();
            $table->string('npwp', 50)->nullable();
            $table->date('tanggal_npwp')->nullable();
            $table->string('nppkp', 100)->nullable();
            $table->date('tanggal_nppkp')->nullable();
            $table->enum('output_tax', ['PPN', 'NON-PPN', 'Terhutang PPN'])->nullable();
            $table->string('term_of_payment', 100)->nullable();
            $table->string('lead_time', 100)->nullable();
            $table->decimal('credit_limit', 18, 2)->nullable();
            $table->string('ccar', 100)->nullable();
            $table->enum('bank_garansi', ['YA', 'TIDAK'])->nullable();
            $table->string('area', 100)->nullable();
            $table->date('join_date')->nullable();
            $table->enum('status', ['active', 'inactive'])->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
