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
        Schema::table('logistic_orders', function (Blueprint $table) {
            $table->text('cancel_reason')->nullable()->after('delivery_date');
            $table->timestamp('canceled_at')->nullable()->after('cancel_reason');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('logistic_orders', function (Blueprint $table) {
            $table->dropColumn(['cancel_reason', 'canceled_at']);
        });
    }
};
