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
        if (!Schema::hasTable('customers')) {
            return;
        }

        Schema::table('customers', function (Blueprint $table) {
            // Add email after address3 if it doesn't exist
            if (!Schema::hasColumn('customers', 'email')) {
                $table->string('email', 255)->nullable()->after('address3');
            }

            // Tax contact fields after penagihan_address
            if (!Schema::hasColumn('customers', 'tax_contact_name')) {
                $table->string('tax_contact_name', 255)->nullable()->after('penagihan_address');
            }
            if (!Schema::hasColumn('customers', 'tax_contact_email')) {
                $table->string('tax_contact_email', 255)->nullable()->after('tax_contact_name');
            }
            if (!Schema::hasColumn('customers', 'tax_contact_phone')) {
                $table->string('tax_contact_phone', 50)->nullable()->after('tax_contact_email');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('customers')) {
            return;
        }

        Schema::table('customers', function (Blueprint $table) {
            foreach (['tax_contact_phone', 'tax_contact_email', 'tax_contact_name', 'email'] as $col) {
                if (Schema::hasColumn('customers', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
