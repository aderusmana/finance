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
        Schema::table('logistic_order_items', function (Blueprint $table) {
            $table->string('pack_size')->nullable()->after('order_item_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('logistic_order_items', function (Blueprint $table) {
            $table->dropColumn('pack_size');
        });
    }
};
