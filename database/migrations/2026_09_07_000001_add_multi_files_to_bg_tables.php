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
        Schema::table('bank_garansi', function (Blueprint $table) {
            if (!Schema::hasColumn('bank_garansi', 'lampiran_d_file_path')) {
                $table->string('lampiran_d_file_path', 500)->nullable()->after('warkat_file_path');
            }
            if (!Schema::hasColumn('bank_garansi', 'warkat_files')) {
                $table->json('warkat_files')->nullable()->after('lampiran_d_file_path');
            }
            if (!Schema::hasColumn('bank_garansi', 'lampiran_d_files')) {
                $table->json('lampiran_d_files')->nullable()->after('warkat_files');
            }
        });

        Schema::table('bg_submissions', function (Blueprint $table) {
            if (!Schema::hasColumn('bg_submissions', 'lampiran_d_file_path')) {
                $table->string('lampiran_d_file_path', 500)->nullable()->after('warkat_file_path');
            }
            if (!Schema::hasColumn('bg_submissions', 'warkat_files')) {
                $table->json('warkat_files')->nullable()->after('lampiran_d_file_path');
            }
            if (!Schema::hasColumn('bg_submissions', 'lampiran_d_files')) {
                $table->json('lampiran_d_files')->nullable()->after('warkat_files');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bank_garansi', function (Blueprint $table) {
            if (Schema::hasColumn('bank_garansi', 'lampiran_d_files')) {
                $table->dropColumn('lampiran_d_files');
            }
            if (Schema::hasColumn('bank_garansi', 'warkat_files')) {
                $table->dropColumn('warkat_files');
            }
            if (Schema::hasColumn('bank_garansi', 'lampiran_d_file_path')) {
                $table->dropColumn('lampiran_d_file_path');
            }
        });

        Schema::table('bg_submissions', function (Blueprint $table) {
            if (Schema::hasColumn('bg_submissions', 'lampiran_d_files')) {
                $table->dropColumn('lampiran_d_files');
            }
            if (Schema::hasColumn('bg_submissions', 'warkat_files')) {
                $table->dropColumn('warkat_files');
            }
            if (Schema::hasColumn('bg_submissions', 'lampiran_d_file_path')) {
                $table->dropColumn('lampiran_d_file_path');
            }
        });
    }
};
