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
            $table->string('purchasing_manager_telepon')->nullable()->after('purchasing_manager_email');
            $table->string('finance_manager_telepon')->nullable()->after('finance_manager_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('purchasing_manager_telepon');
            $table->dropColumn('finance_manager_telepon');
        });
    }
};
