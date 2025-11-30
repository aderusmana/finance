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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('account_group_id')->nullable()->constrained('account_groups');
            $table->foreignId('branch_id')->nullable()->constrained('branches');
            $table->foreignId('region_id')->nullable()->constrained('regions');
            $table->string('sales_code')->unique();
            $table->string('sales_name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('sales')) {
            Schema::table('sales', function (Blueprint $table) {
                $table->dropForeign(['account_group_id']);
                $table->dropForeign(['branch_id']);
                $table->dropForeign(['region_id']);
            });
        }

        Schema::dropIfExists('sales');

    }
};
