<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Update bg_recommendations table
        Schema::table('bg_recommendations', function (Blueprint $table) {
            // Change enum status to string to flexibly accommodate new workflow statuses
            // ('draft', 'pending', 'waiting_approval_sales', 'rejected_by_sales', 'process', 'waiting_upload', 'approved', 'rejected')
            $table->string('status', 50)->default('draft')->change();
            
            if (!Schema::hasColumn('bg_recommendations', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('notes');
            }
            if (!Schema::hasColumn('bg_recommendations', 'parent_recommendation_id')) {
                $table->unsignedBigInteger('parent_recommendation_id')->nullable()->after('id');
                $table->foreign('parent_recommendation_id')->references('id')->on('bg_recommendations')->onDelete('set null');
            }
            if (!Schema::hasColumn('bg_recommendations', 'sales_approved_by')) {
                $table->unsignedBigInteger('sales_approved_by')->nullable()->after('rejection_reason');
                $table->timestamp('sales_approved_at')->nullable()->after('sales_approved_by');
                $table->foreign('sales_approved_by')->references('id')->on('users')->onDelete('set null');
            }
        });

        // 2. Update bg_submissions table
        Schema::table('bg_submissions', function (Blueprint $table) {
            $table->string('status', 50)->default('pending_print')->change();

            if (!Schema::hasColumn('bg_submissions', 'custom_address')) {
                $table->text('custom_address')->nullable()->after('signed_document_path');
            }
            if (!Schema::hasColumn('bg_submissions', 'bg_number')) {
                $table->string('bg_number', 100)->nullable()->after('custom_address');
            }
            if (!Schema::hasColumn('bg_submissions', 'bg_nominal')) {
                $table->decimal('bg_nominal', 18, 2)->nullable()->after('bg_number');
            }
            if (!Schema::hasColumn('bg_submissions', 'exp_date')) {
                $table->date('exp_date')->nullable()->after('bg_nominal');
            }
            if (!Schema::hasColumn('bg_submissions', 'warkat_file_path')) {
                $table->string('warkat_file_path', 500)->nullable()->after('exp_date');
            }
            if (!Schema::hasColumn('bg_submissions', 'submission_type')) {
                $table->string('submission_type', 50)->default('portal')->after('warkat_file_path');
            }
            if (!Schema::hasColumn('bg_submissions', 'validated_by')) {
                $table->unsignedBigInteger('validated_by')->nullable()->after('submission_type');
                $table->timestamp('validated_at')->nullable()->after('validated_by');
                $table->foreign('validated_by')->references('id')->on('users')->onDelete('set null');
            }
        });

        // 3. Update bank_garansi table
        Schema::table('bank_garansi', function (Blueprint $table) {
            $table->string('status', 50)->default('draft')->change();

            if (!Schema::hasColumn('bank_garansi', 'warkat_file_path')) {
                $table->string('warkat_file_path', 500)->nullable()->after('bg_nominal');
            }
            if (!Schema::hasColumn('bank_garansi', 'is_adendum')) {
                $table->boolean('is_adendum')->default(false)->after('warkat_file_path');
            }
        });

        // 4. Ensure dep-SNM role exists and is assigned to Pak Ronal Katili
        try {
            $depSnmRole = Role::firstOrCreate(['name' => 'dep-SNM', 'guard_name' => 'web']);
            
            $ronal = User::where('name', 'like', '%ronal%')->orWhere('email', 'like', '%ronal%')->first();
            if ($ronal && !$ronal->hasRole('dep-SNM')) {
                $ronal->assignRole($depSnmRole);
            }
        } catch (\Exception $e) {
            // Ignore if role already handled
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bg_recommendations', function (Blueprint $table) {
            if (Schema::hasColumn('bg_recommendations', 'parent_recommendation_id')) {
                $table->dropForeign(['parent_recommendation_id']);
                $table->dropColumn('parent_recommendation_id');
            }
            if (Schema::hasColumn('bg_recommendations', 'sales_approved_by')) {
                $table->dropForeign(['sales_approved_by']);
                $table->dropColumn(['sales_approved_by', 'sales_approved_at']);
            }
            if (Schema::hasColumn('bg_recommendations', 'rejection_reason')) {
                $table->dropColumn('rejection_reason');
            }
        });

        Schema::table('bg_submissions', function (Blueprint $table) {
            if (Schema::hasColumn('bg_submissions', 'validated_by')) {
                $table->dropForeign(['validated_by']);
                $table->dropColumn(['validated_by', 'validated_at']);
            }
            $columns = ['custom_address', 'bg_number', 'bg_nominal', 'exp_date', 'warkat_file_path', 'submission_type'];
            foreach ($columns as $col) {
                if (Schema::hasColumn('bg_submissions', $col)) {
                    $table->dropColumn($col);
                }
            }
        });

        Schema::table('bank_garansi', function (Blueprint $table) {
            if (Schema::hasColumn('bank_garansi', 'warkat_file_path')) {
                $table->dropColumn('warkat_file_path');
            }
            if (Schema::hasColumn('bank_garansi', 'is_adendum')) {
                $table->dropColumn('is_adendum');
            }
        });
    }
};
