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
            ['region_name' => 'DKI Jakarta'],
            ['region_name' => 'Jawa Barat'],
            ['region_name' => 'Jawa Tengah'],
            ['region_name' => 'Jawa Timur'],
            ['region_name' => 'Sumatera'],
            ['region_name' => 'Kalimantan'],
            ['region_name' => 'Sulawesi'],
            ['region_name' => 'Bali & Nusa Tenggara'],
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
        ];

        foreach ($branches as $branch) {
            Branch::updateOrCreate(['branch_name' => $branch['branch_name']], $branch);
        }

        // ==========================================
        // 3. Seed Account Groups
        // ==========================================
        $accountGroups = [
            [
                'name_account_group' => 'Trading / Distributor',
                'bank_garansi' => true,
                'ccar' => 'smd_idr'
            ],
            [
                'name_account_group' => 'Retailer',
                'bank_garansi' => false,
                'ccar' => 'smd_idr'
            ],
            [
                'name_account_group' => 'Industry / End User',
                'bank_garansi' => true,
                'ccar' => 'smd_idr'
            ],
            [
                'name_account_group' => 'Government / BUMN',
                'bank_garansi' => true,
                'ccar' => 'smd_idr'
            ],
            [
                'name_account_group' => 'Export Market',
                'bank_garansi' => true,
                'ccar' => 'smd_usd'
            ],
        ];

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
            ['name_top' => 'CASH', 'desc_top' => 'Cash on Delivery / CBD'],
            ['name_top' => 'TOP 7', 'desc_top' => 'Net 7 Days'],
            ['name_top' => 'TOP 14', 'desc_top' => 'Net 14 Days'],
            ['name_top' => 'TOP 30', 'desc_top' => 'Net 30 Days'],
            ['name_top' => 'TOP 45', 'desc_top' => 'Net 45 Days'],
            ['name_top' => 'TOP 60', 'desc_top' => 'Net 60 Days'],
        ];

        foreach ($tops as $top) {
            TOP::updateOrCreate(['name_top' => $top['name_top']], $top);
        }

        // ==========================================
        // 5. Seed Customer Class
        // ==========================================
        $classes = [
            ['name_class' => 'Platinum'],
            ['name_class' => 'Gold'],
            ['name_class' => 'Silver'],
            ['name_class' => 'Bronze'],
            ['name_class' => 'General'],
        ];

        foreach ($classes as $cls) {
            CustomerClass::updateOrCreate(['name_class' => $cls['name_class']], $cls);
        }

        // ==========================================
        // 6. Seed Sales (Relasi ke User)
        // ==========================================

        // Mengambil User yang sudah dibuat di AllSeeder.php
        $salesUser = User::where('username', 'staff.sales1')->first();
        $headSalesUser = User::where('username', 'head.sales')->first();

        // Data pendukung untuk Foreign Keys
        $jktRegion = Regions::where('region_name', 'DKI Jakarta')->first();
        $hoBranch = Branch::where('branch_name', 'Head Office - Jakarta')->first();
        $distAccountGroup = AccountGroup::where('name_account_group', 'Trading / Distributor')->first();

        // Membuat data Sales untuk Staff Sales 1
        if ($salesUser && $jktRegion && $hoBranch && $distAccountGroup) {
            Sales::updateOrCreate(
                ['user_id' => $salesUser->id], // Key unik sekarang adalah user_id
                [
                    'region_id' => $jktRegion->id,
                    'branch_id' => $hoBranch->id,
                    'account_group_id' => $distAccountGroup->id,
                ]
            );
        }

        // Membuat data Sales untuk Head Sales
        if ($headSalesUser && $jktRegion && $hoBranch && $distAccountGroup) {
            Sales::updateOrCreate(
                ['user_id' => $headSalesUser->id], // Key unik sekarang adalah user_id
                [
                    'region_id' => $jktRegion->id,
                    'branch_id' => $hoBranch->id,
                    'account_group_id' => $distAccountGroup->id,
                ]
            );
        }
    }
}
