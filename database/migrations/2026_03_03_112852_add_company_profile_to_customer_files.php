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
        Schema::table('customer_files', function (Blueprint $table) {
            $table->string('company_profile_file')->nullable()->after('akte_file');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_files', function (Blueprint $table) {
            $table->dropColumn('company_profile_file');
        });
    }
};
