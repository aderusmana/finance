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
            $table->boolean('is_urgent')->default(false)->after('canceled_at');
            $table->timestamp('last_reminder_at')->nullable()->after('is_urgent');
            $table->unsignedInteger('reminder_count')->default(0)->after('last_reminder_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('logistic_orders', function (Blueprint $table) {
            $table->dropColumn(['is_urgent', 'last_reminder_at', 'reminder_count']);
        });
    }
};
