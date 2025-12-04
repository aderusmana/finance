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
        Schema::create('approval_paths', function (Blueprint $table) {
            $table->id();
            $table->string('category', 50);
            $table->string('sub_category', 50)->nullable();
            $table->json('sequence_approvers');
            $table->timestamps();
            $table->unique(['category', 'sub_category'], 'unique_approval_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approval_paths');
    }
};
