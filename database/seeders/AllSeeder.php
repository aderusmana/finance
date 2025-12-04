<?php

namespace Database\Seeders;

use App\Models\Master\Department;
use App\Models\Master\Position;
use App\Models\Master\Revision;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AllSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable FK checks untuk menghindari error saat insert atasan_nik
        Schema::disableForeignKeyConstraints();

        // ==========================================
        // 1. Create Permissions
        // ==========================================
        $permissions = [
            'view role', 'create role', 'update role', 'delete role',
            'view permission', 'create permission', 'update permission', 'delete permission',
            'view user', 'create user', 'update user', 'delete user',
            'view department', 'create department', 'update department', 'delete department',
            'view requisition', 'create requisition', 'update requisition', 'delete requisition',
            'view approval',
            'view item', 'create item', 'update item', 'delete item',
            'view customer', 'create customer', 'update customer', 'delete customer',
            'view log', 'view requisition-form', 'view report', 'view requisition-approval',
            'view approval-path', 'view revision',
            'approve requisition', 'reject requisition',
            'view dashboard'
        ];

        foreach ($permissions as $perm) {
            Permission::updateOrCreate(['name' => $perm]);
        }

        $now = Carbon::now();

        // ==========================================
        // 2. Create Departments
        // ==========================================
        $departments = [
            ['id' => 1, 'name' => 'Engineering & Maintenance', 'code' => '-', 'slug' => 'engineering-maintainance'],
            ['id' => 2, 'name' => 'Finance Admin', 'code' => '-', 'slug' => 'finance-admin'],
            ['id' => 3, 'name' => 'HCD', 'code' => '-', 'slug' => 'hcd'],
            ['id' => 4, 'name' => 'Manufacturing', 'code' => '-', 'slug' => 'manufacturing'],
            ['id' => 5, 'name' => 'QM & HSE', 'code' => '5302', 'slug' => 'qm-hse'],
            ['id' => 6, 'name' => 'R&D', 'code' => '5302', 'slug' => 'rd'],
            ['id' => 7, 'name' => 'Sales & Marketing', 'code' => '5300', 'slug' => 'sales-marketing'],
            ['id' => 8, 'name' => 'Supply Chain', 'code' => '-', 'slug' => 'supply-chain'],
            ['id' => 9, 'name' => 'Supply & Maintenance', 'code' => '-', 'slug' => 'supply-and-maintenance'],
        ];

        foreach ($departments as $dept) {
            Department::updateOrCreate(
                ['id' => $dept['id']],
                [
                    'name' => $dept['name'],
                    'code' => $dept['code'],
                    'slug' => $dept['slug'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }

        // ==========================================
        // 3. Create Positions
        // ==========================================
        $posDirector = Position::updateOrCreate(['position_name' => 'Director']);
        $posGM = Position::updateOrCreate(['position_name' => 'General Manager']);
        $posManager = Position::updateOrCreate(['position_name' => 'Manager']);
        $posSupervisor = Position::updateOrCreate(['position_name' => 'Supervisor']);
        $posStaff = Position::updateOrCreate(['position_name' => 'Staff']);

        // ==========================================
        // 4. Create Revision Data
        // ==========================================
        Revision::updateOrCreate(
            ['id' => 1],
            [
                'revision_number' => 'REV-001',
                'revision_count' => 1,
                'revision_date' => $now->format('Y-m-d'),
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        // ==========================================
        // 5. Create Roles
        // ==========================================
        $superAdminRole = Role::updateOrCreate(['name' => 'super-admin']);
        $userRequisitionRole = Role::updateOrCreate(['name' => 'user-requisition']);
        $approvalRole = Role::updateOrCreate(['name' => 'user-approval']);

        // --- NEW: Role Sales ---
        $salesRole = Role::updateOrCreate(['name' => 'sales']);
        $salesRole->givePermissionTo([
            'view dashboard',
            'view customer',
            'create customer',
            'update customer',
            'delete customer',
            'view report' // Opsional: biasanya sales butuh lihat report
        ]);

        // Setup Specific Roles (Existing)
        $headWhRole = Role::updateOrCreate(['name' => 'wh-supervisor']);
        $headWhRole->givePermissionTo(['view log', 'view requisition-form', 'view report', 'view requisition-approval', 'approve requisition', 'reject requisition']);

        $staffWhRole = Role::updateOrCreate(['name' => 'wh-staff']);
        $staffWhRole->givePermissionTo(['view log', 'view requisition-form', 'view report', 'view requisition', 'create requisition', 'update requisition', 'delete requisition']);

        $headMaterialRole = Role::updateOrCreate(['name' => 'material-supervisor']);
        $headMaterialRole->givePermissionTo(['view log', 'view requisition-form', 'view report', 'view requisition-approval', 'approve requisition', 'reject requisition']);

        $staffMaterialRole = Role::updateOrCreate(['name' => 'material-staff']);
        $staffMaterialRole->givePermissionTo(['view log', 'view requisition-form', 'view report', 'view requisition', 'create requisition', 'update requisition', 'delete requisition']);

        $headSalesMarketingRole = Role::updateOrCreate(['name' => 'head-SNM']);
        $headSalesMarketingRole->givePermissionTo(['view log', 'view requisition-form', 'view report', 'view requisition-approval', 'approve requisition', 'reject requisition']);

        $staffSalesMarketingRole = Role::updateOrCreate(['name' => 'staff-SNM']);
        $staffSalesMarketingRole->givePermissionTo(['view log', 'view requisition-form', 'view report', 'view requisition', 'create requisition', 'update requisition', 'delete requisition']);

        $headRndRole = Role::updateOrCreate(['name' => 'head-R&D']);
        $headRndRole->givePermissionTo(['view log', 'view requisition-form', 'view report', 'view requisition-approval', 'approve requisition', 'reject requisition']);

        $staffRndRole = Role::updateOrCreate(['name' => 'staff-R&D']);
        $staffRndRole->givePermissionTo(['view log', 'view requisition-form', 'view report', 'view requisition', 'create requisition', 'update requisition', 'delete requisition']);

        $headQaRole = Role::updateOrCreate(['name' => 'head-QA']);
        $headQaRole->givePermissionTo(['view log', 'view requisition-form', 'view report', 'view requisition-approval', 'approve requisition', 'reject requisition']);

        $staffQaRole = Role::updateOrCreate(['name' => 'staff-QA']);
        $staffQaRole->givePermissionTo(['view log', 'view requisition-form', 'view report', 'view requisition', 'create requisition', 'update requisition', 'delete requisition']);

        $headHcdRole = Role::updateOrCreate(['name' => 'head-HCD']);
        $headHcdRole->givePermissionTo(['view log', 'view requisition-form', 'view report', 'view requisition-approval', 'approve requisition', 'reject requisition']);

        $staffHcdRole = Role::updateOrCreate(['name' => 'staff-HCD']);
        $staffHcdRole->givePermissionTo(['view log', 'view requisition-form', 'view report', 'view requisition', 'create requisition', 'update requisition', 'delete requisition']);

        $atasanRole = Role::updateOrCreate(['name' => 'atasan']);
        $atasanRole->givePermissionTo(['view log', 'view requisition-form', 'view report', 'view requisition-approval', 'approve requisition', 'reject requisition']);

        // Assign basic permissions
        $userRequisitionRole->givePermissionTo(['view log', 'view requisition-form', 'view report', 'view requisition', 'create requisition', 'update requisition', 'delete requisition']);
        $approvalRole->givePermissionTo(['view log', 'view requisition-form', 'view report', 'view approval', 'view requisition-approval', 'approve requisition', 'reject requisition']);
        $superAdminRole->givePermissionTo(Permission::all());

        // ==========================================
        // 6. Create Users
        // ==========================================

        $superAdminUser = User::updateOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'name' => 'Super Admin',
                'nik' => 'AG1111',
                'username' => 'superadmin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'department_id' => 1,
                'status' => 'active',
                'atasan_nik' => 'AG2222',
                'position_id' => $posDirector->id,
            ]
        );
        $superAdminUser->assignRole($superAdminRole);

        $userRequsition = User::updateOrCreate(
            ['email' => 'user-requisition@example.com'],
            [
                'name' => 'User Requisition',
                'nik' => 'AG2222',
                'username' => 'user-requisition',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'department_id' => 2,
                'status' => 'active',
                'atasan_nik' => 'AG3333',
                'position_id' => $posGM->id,
            ]
        );
        $userRequsition->assignRole($userRequisitionRole);

        $userApproval = User::updateOrCreate(
            ['email' => 'no-reply@example.com'],
            [
                'name' => 'User Approval',
                'nik' => 'AG3333',
                'username' => 'user-approval',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'department_id' => 3,
                'status' => 'active',
                'atasan_nik' => 'AG1111',
                'position_id' => $posManager->id,
            ]
        );
        $userApproval->assignRole($approvalRole);

        // --- WH Users ---
        $headWh = User::updateOrCreate(
            ['email' => 'head.wh@example.com'],
            [
                'name' => 'Head WH', 'nik' => 'HDWH01', 'username' => 'head.wh',
                'password' => Hash::make('password'), 'email_verified_at' => now(),
                'department_id' => 9, 'status' => 'active', 'atasan_nik' => 'AG1111',
                'position_id' => $posManager->id,
            ]
        );
        $headWh->assignRole($headWhRole);

        $staffWh1 = User::updateOrCreate(
            ['email' => 'staff.wh1@example.com'],
            [
                'name' => 'Staff WH 1', 'nik' => 'STWH01', 'username' => 'staff.wh1',
                'password' => Hash::make('password'), 'email_verified_at' => now(),
                'department_id' => 9, 'status' => 'active', 'atasan_nik' => 'HDWH01',
                'position_id' => $posStaff->id,
            ]
        );
        $staffWh1->assignRole($staffWhRole);

        // --- Material Users ---
        $headMaterial = User::updateOrCreate(
            ['email' => 'head.material@example.com'],
            [
                'name' => 'Head Material', 'nik' => 'HDMT01', 'username' => 'head.material',
                'password' => Hash::make('password'), 'email_verified_at' => now(),
                'department_id' => 8, 'status' => 'active', 'atasan_nik' => 'AG1111',
                'position_id' => $posManager->id,
            ]
        );
        $headMaterial->assignRole($headMaterialRole);

        $staffMaterial1 = User::updateOrCreate(
            ['email' => 'staff.material1@example.com'],
            [
                'name' => 'Staff Material 1', 'nik' => 'STMT01', 'username' => 'staff.material1',
                'password' => Hash::make('password'), 'email_verified_at' => now(),
                'department_id' => 8, 'status' => 'active', 'atasan_nik' => 'HDMT01',
                'position_id' => $posStaff->id,
            ]
        );
        $staffMaterial1->assignRole($staffMaterialRole);

        // --- Sales & Marketing Users ---
        $headSales = User::updateOrCreate(
            ['email' => 'head.sales@example.com'],
            [
                'name' => 'Head SNM', 'nik' => 'HDSM01', 'username' => 'head.sales',
                'password' => Hash::make('password'), 'email_verified_at' => now(),
                'department_id' => 7, 'status' => 'active', 'atasan_nik' => 'AG1111',
                'position_id' => $posManager->id,
            ]
        );
        $headSales->assignRole($headSalesMarketingRole);

        $staffSales1 = User::updateOrCreate(
            ['email' => 'staff.sales1@example.com'],
            [
                'name' => 'Staff SNM 1', 'nik' => 'STSM01', 'username' => 'staff.sales1',
                'password' => Hash::make('password'), 'email_verified_at' => now(),
                'department_id' => 7, 'status' => 'active', 'atasan_nik' => 'HDSM01',
                'position_id' => $posStaff->id,
            ]
        );
        $staffSales1->assignRole($staffSalesMarketingRole);
        $staffSales1->assignRole($salesRole); // Assign juga role 'sales' agar bisa akses customer

        $staffSales2 = User::updateOrCreate(
            ['email' => 'staff.sales2@example.com'],
            [
                'name' => 'Staff SNM 2', 'nik' => 'STSM02', 'username' => 'staff.sales2',
                'password' => Hash::make('password'), 'email_verified_at' => now(),
                'department_id' => 7, 'status' => 'active', 'atasan_nik' => 'HDSM01',
                'position_id' => $posStaff->id,
            ]
        );
        $staffSales2->assignRole($staffSalesMarketingRole);
        $staffSales2->assignRole($salesRole); // Assign juga role 'sales'

        // --- R&D Users ---
        $headRnd = User::updateOrCreate(
            ['email' => 'head.rnd@example.com'],
            [
                'name' => 'Head R&D', 'nik' => 'HDRD01', 'username' => 'head.rnd',
                'password' => Hash::make('password'), 'email_verified_at' => now(),
                'department_id' => 6, 'status' => 'active', 'atasan_nik' => 'AG1111',
                'position_id' => $posManager->id,
            ]
        );
        $headRnd->assignRole($headRndRole);

        $staffRnd1 = User::updateOrCreate(
            ['email' => 'staff.rnd1@example.com'],
            [
                'name' => 'Staff R&D 1', 'nik' => 'STRD01', 'username' => 'staff.rnd1',
                'password' => Hash::make('password'), 'email_verified_at' => now(),
                'department_id' => 6, 'status' => 'active', 'atasan_nik' => 'HDRD01',
                'position_id' => $posStaff->id,
            ]
        );
        $staffRnd1->assignRole($staffRndRole);

        $staffRnd2 = User::updateOrCreate(
            ['email' => 'staff.rnd2@example.com'],
            [
                'name' => 'Staff R&D 2', 'nik' => 'STRD02', 'username' => 'staff.rnd2',
                'password' => Hash::make('password'), 'email_verified_at' => now(),
                'department_id' => 6, 'status' => 'active', 'atasan_nik' => 'HDRD01',
                'position_id' => $posStaff->id,
            ]
        );
        $staffRnd2->assignRole($staffRndRole);

        // --- QA Users ---
        $headQa = User::updateOrCreate(
            ['email' => 'head.qa@example.com'],
            [
                'name' => 'Head QA', 'nik' => 'HDQA01', 'username' => 'head.qa',
                'password' => Hash::make('password'), 'email_verified_at' => now(),
                'department_id' => 5, 'status' => 'active', 'atasan_nik' => 'AG1111',
                'position_id' => $posManager->id,
            ]
        );
        $headQa->assignRole($headQaRole);

        $staffQa1 = User::updateOrCreate(
            ['email' => 'staff.qa1@example.com'],
            [
                'name' => 'Staff QA 1', 'nik' => 'STQA01', 'username' => 'staff.qa1',
                'password' => Hash::make('password'), 'email_verified_at' => now(),
                'department_id' => 5, 'status' => 'active', 'atasan_nik' => 'HDQA01',
                'position_id' => $posStaff->id,
            ]
        );
        $staffQa1->assignRole($staffQaRole);

        $staffQa2 = User::updateOrCreate(
            ['email' => 'staff.qa2@example.com'],
            [
                'name' => 'Staff QA 2', 'nik' => 'STQA02', 'username' => 'staff.qa2',
                'password' => Hash::make('password'), 'email_verified_at' => now(),
                'department_id' => 5, 'status' => 'active', 'atasan_nik' => 'HDQA01',
                'position_id' => $posStaff->id,
            ]
        );
        $staffQa2->assignRole($staffQaRole);

        // --- HCD Users ---
        $headHcd = User::updateOrCreate(
            ['email' => 'head.hcd@example.com'],
            [
                'name' => 'Head HCD', 'nik' => 'HDHCD01', 'username' => 'head.hcd',
                'password' => Hash::make('password'), 'email_verified_at' => now(),
                'department_id' => 3, 'status' => 'active', 'atasan_nik' => 'AG1111',
                'position_id' => $posManager->id,
            ]
        );
        $headHcd->assignRole($headHcdRole);

        $staffHcd1 = User::updateOrCreate(
            ['email' => 'staff.hcd1@example.com'],
            [
                'name' => 'Staff HCD 1', 'nik' => 'STHCD01', 'username' => 'staff.hcd1',
                'password' => Hash::make('password'), 'email_verified_at' => now(),
                'department_id' => 3, 'status' => 'active', 'atasan_nik' => 'HDHCD01',
                'position_id' => $posStaff->id,
            ]
        );
        $staffHcd1->assignRole($staffHcdRole);

        $staffHcd2 = User::updateOrCreate(
            ['email' => 'staff.hcd2@example.com'],
            [
                'name' => 'Staff HCD 2', 'nik' => 'STHCD02', 'username' => 'staff.hcd2',
                'password' => Hash::make('password'), 'email_verified_at' => now(),
                'department_id' => 3, 'status' => 'active', 'atasan_nik' => 'HDHCD01',
                'position_id' => $posStaff->id,
            ]
        );
        $staffHcd2->assignRole($staffHcdRole);

        $atasan1 = User::updateOrCreate(
            ['email' => 'atasan1@example.com'],
            [
                'name' => 'Atasan 1', 'nik' => 'ATASAN01', 'username' => 'atasan1',
                'password' => Hash::make('password'), 'email_verified_at' => now(),
                'department_id' => 1, 'status' => 'active', 'atasan_nik' => 'AG1111',
                'position_id' => $posManager->id,
            ]
        );
        $atasan1->assignRole($atasanRole);

        $anotherUserRequisition = User::updateOrCreate(
            ['email' => 'staff.eng@example.com'],
            [
                'name' => 'Staff Engineering', 'nik' => 'ST0001', 'username' => 'staff.eng',
                'password' => Hash::make('password'), 'email_verified_at' => now(),
                'department_id' => 7, 'status' => 'active', 'atasan_nik' => 'HD0001',
                'position_id' => $posStaff->id,
            ]
        );
        $anotherUserRequisition->assignRole('user-requisition');

        // --- Warehouse Specific Users ---
        $warehouseUsers = [
            [
                'name' => 'Inward WH Supervisor',
                'nik' => 'WH0001',
                'username' => 'inward.wh',
                'email' => 'inward.wh@example.com',
                'department_id' => 8,
                'position_id' => $posSupervisor->id,
            ],
            [
                'name' => 'Material Support Supervisor',
                'nik' => 'MS0001',
                'username' => 'material.support',
                'email' => 'material.support@example.com',
                'department_id' => 8,
                'position_id' => $posSupervisor->id,
            ],
            [
                'name' => 'Outward WH Supervisor',
                'nik' => 'WH0002',
                'username' => 'outward.wh',
                'email' => 'outward.wh@example.com',
                'department_id' => 8,
                'position_id' => $posSupervisor->id,
            ],
        ];

        foreach ($warehouseUsers as $whUser) {
            $user = User::updateOrCreate(
                ['email' => $whUser['email']],
                [
                    'name' => $whUser['name'],
                    'nik' => $whUser['nik'],
                    'username' => $whUser['username'],
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                    'department_id' => $whUser['department_id'],
                    'status' => 'active',
                    'atasan_nik' => 'AG1111',
                    'position_id' => $whUser['position_id'],
                ]
            );
            $user->assignRole('user-requisition');
        }

        Schema::enableForeignKeyConstraints();
    }
}
