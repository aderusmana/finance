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
        Schema::table('distributor_customers', function (Blueprint $table) {
            $table->string('status')->default('Approved')->after('logistic_fee');
            $table->integer('proposed_fee')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('distributor_customers', function (Blueprint $table) {
            $table->dropColumn(['status', 'proposed_fee']);
        });
    }
};
