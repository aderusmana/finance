<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Master\ApprovalPath;
use Spatie\Permission\Models\Role;

class CustomerApprovalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rolesToCheck = [
            'atasan',      // 1. Atasan
            'manager-finance', // 2. Manager Finance
            'head-snm',        // 3. Head SNM
            'head-finance',    // 4. Head Finance
            'it'               // 5. IT
        ];

        foreach ($rolesToCheck as $roleName) {
            if (!Role::where('name', $roleName)->exists()) {
                Role::create(['name' => $roleName, 'guard_name' => 'web']);
            }
        }

        ApprovalPath::updateOrCreate(
            [
                'category'     => 'Customer',
                'sub_category' => 'CBD',
            ],
            [
                'sequence_approvers' => $rolesToCheck,

                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
