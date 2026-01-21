<?php

namespace Database\Seeders;

use App\Models\Customer\AccountGroup;
use App\Models\Customer\Branch;
use App\Models\Customer\CustomerClass;
use App\Models\Customer\Regions;
use App\Models\Customer\Sales;
use App\Models\Customer\TOP;
use App\Models\User;
use Illuminate\Database\Seeder;

class MasterManagementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ==========================================
        // 1. Seed Regions (Wilayah)
        // ==========================================
        $regions = [
            ['region_name' => 'COMMERCIAL'],
            ['region_name' => 'KEY ACCOUNT'],
            ['region_name' => 'REGION 1A'],
            ['region_name' => 'REGION 1B'],
            ['region_name' => 'REGION 1C'],
            ['region_name' => 'REGION 1D'],
            ['region_name' => 'REGION 2A'],
            ['region_name' => 'REGION 2B'],
            ['region_name' => 'REGION 2C'],
            ['region_name' => 'REGION 2D'],
            ['region_name' => 'REGION 3A'],
            ['region_name' => 'REGION 3B'],
            ['region_name' => 'REGION 3C'],
            ['region_name' => 'REGION 4A'],
            ['region_name' => 'REGION 4B'],
        ];

        foreach ($regions as $reg) {
            Regions::updateOrCreate(['region_name' => $reg['region_name']], $reg);
        }

        // ==========================================
        // 2. Seed Branches (Cabang)
        // ==========================================
        $branches = [
            ['branch_name' => 'Head Office - Jakarta'],
            ['branch_name' => 'Branch Bandung'],
            ['branch_name' => 'Branch Surabaya'],
            ['branch_name' => 'Branch Medan'],
            ['branch_name' => 'Branch Makassar'],
            ['branch_name' => 'Pusat'],

        ];

        foreach ($branches as $branch) {
            Branch::updateOrCreate(['branch_name' => $branch['branch_name']], $branch);
        }

        // ==========================================
        // 3. Seed Account Groups
        //    - create groups from region names (name_account_group == region_name)
        //    - add some extra random groups (including Trading / Distributor)
        // ==========================================
        $accountGroups = [];

        // Create account group per region (name matches region)
        foreach ($regions as $reg) {
            $accountGroups[] = [
            'name_account_group' => $reg['region_name'],
            'bank_garansi' => (bool) rand(0, 1),
            'ccar' => rand(0, 1) ? 'smd_idr' : 'smd_usd',
            ];
        }

        // No additional random account groups; use region names only

        foreach ($accountGroups as $group) {
            AccountGroup::updateOrCreate(
            ['name_account_group' => $group['name_account_group']],
            [
                'bank_garansi' => $group['bank_garansi'],
                'ccar' => $group['ccar']
            ]
            );
        }


        // ==========================================
        // 4. Seed TOP (Terms of Payment)
        // ==========================================
        $tops = [
            ['name_top' => 'CBD', 'desc_top' => 'Cash on Delivery / CBD'],
            ['name_top' => '7', 'desc_top' => 'Net 7 Days'],
            ['name_top' => '14', 'desc_top' => 'Net 14 Days'],
            ['name_top' => '30', 'desc_top' => 'Net 30 Days'],
            ['name_top' => '45', 'desc_top' => 'Net 45 Days'],
        ];

        foreach ($tops as $top) {
            TOP::updateOrCreate(['name_top' => $top['name_top']], $top);
        }

        // ==========================================
        // 5. Seed Customer Class
        // ==========================================
        $classes = [
            ['name_class' => 'Bakery'],
            ['name_class' => 'Key Account'],
            ['name_class' => 'Distributor'],
            ['name_class' => 'Food Service'],
            ['name_class' => 'HORECA'],
            ['name_class' => 'Bulk Olein/Oil'],
            ['name_class' => 'Export'],

        ];

        foreach ($classes as $cls) {
            CustomerClass::updateOrCreate(['name_class' => $cls['name_class']], $cls);
        }

        // ==========================================
        // 6. Seed Sales (Relasi ke User)
        // ==========================================
        $anyRegion = Regions::inRandomOrder()->first();
        $anyBranch = Branch::inRandomOrder()->first();
        $anyAccountGroup = AccountGroup::inRandomOrder()->first();

        $targetUsers = User::whereIn('username', ['staff.sales1', 'head.sales'])->get();

        if ($targetUsers->isEmpty()) {
            $targetUsers = User::limit(2)->get();
        }

        if ($anyRegion && $anyBranch && $anyAccountGroup) {
            foreach ($targetUsers as $user) {
                Sales::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'region_id' => $anyRegion->id,
                        'branch_id' => $anyBranch->id,
                        'account_group_id' => $anyAccountGroup->id,
                    ]
                );
            }
        }
    }
}
