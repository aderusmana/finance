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
        // A. Urutan untuk CBD
        $rolesCBD = [
            'atasan',
            'head-SNM',
            'head-finance',
            'manager-finance',
            'it'
        ];

        // B. Urutan untuk General / Non-Category
        $rolesGeneral = [
            'atasan',
            'head-finance',
            'manager-finance',
            'it'
        ];

        $allRoles = array_unique(array_merge($rolesCBD, $rolesGeneral));

        foreach ($allRoles as $roleName) {
            if (!Role::where('name', $roleName)->exists()) {
                Role::create(['name' => $roleName, 'guard_name' => 'web']);
            }
        }

        // A. Simpan Path CBD
        ApprovalPath::updateOrCreate(
            [
                'category'     => 'Customer',
                'sub_category' => 'CBD',
            ],
            [
                'sequence_approvers' => $rolesCBD,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // B. Simpan Path General / Non-Category
        ApprovalPath::updateOrCreate(
            [
                'category'     => 'Customer',
                'sub_category' => null,
            ],
            [
                'sequence_approvers' => $rolesGeneral,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
