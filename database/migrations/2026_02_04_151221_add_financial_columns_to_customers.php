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
        Schema::table('customers', function (Blueprint $table) {
            $table->string('virtual_account', 100)->nullable()->after('bank_garansi');
            $table->json('payment_days')->nullable()->after('virtual_account');
            $table->json('payment_date')->nullable()->after('payment_days');
            $table->json('faktur_days')->nullable()->after('payment_date');
            $table->json('faktur_date')->nullable()->after('faktur_days');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['virtual_account', 'payment_days', 'payment_date', 'faktur_days', 'faktur_date']);
        });
    }
};
