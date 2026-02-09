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
        Schema::disableForeignKeyConstraints();

        // ==========================================
        // 1. Create Permissions
        // ==========================================
        $permissions = [
            'view master data menu',
            'view master management menu',
            'view customers menu',
            'view bank garansi menu',
            'view dashboard',
            'view role', 'create role', 'update role', 'delete role',
            'view permission', 'create permission', 'update permission', 'delete permission',
            'view user', 'create user', 'update user', 'delete user',
            'view department', 'create department', 'update department', 'delete department',
            'view item', 'create item', 'update item', 'delete item', 
            'view customer', 'create customer', 'update customer', 'delete customer',
            'view bg', 'create bg', 'update bg', 'delete bg',
            'view bg-approval', 'approve bg', 'reject bg',
            'view log', 'view report', 'view approval', 
            'view approval-path', 'view revision',
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
        // 5. Create Roles & Assign Permissions
        // ==========================================
        
        // --- SUPER ADMIN ---
        $superAdminRole = Role::updateOrCreate(['name' => 'super-admin']);
        $superAdminRole->givePermissionTo(Permission::all());

        // --- ROLE SALES ---
        $salesRole = Role::updateOrCreate(['name' => 'sales']);
        $salesRole->syncPermissions([
            'view dashboard',
            'view customers menu',
            'view customer',
            'create customer',
            'update customer',
            'view report'
        ]);

        // --- ROLE FINANCE ---
        $financePerms = [
            'view dashboard',
            'view customers menu', 'view customer', 'update customer', 
            'view bank garansi menu', 
            'view bg', 'create bg', 'update bg', 'delete bg', 
            'view bg-approval', 'approve bg', 'reject bg',
            'view master management menu',
            'view log', 'view report', 'view approval'
        ];

        $managerFinanceRole = Role::updateOrCreate(['name' => 'manager-finance']);
        $managerFinanceRole->syncPermissions($financePerms);

        $headFinanceRole = Role::updateOrCreate(['name' => 'head-finance']);
        $headFinanceRole->syncPermissions($financePerms);

        // --- ROLE IT ---
        $itRole = Role::updateOrCreate(['name' => 'it']);
        $itRole->syncPermissions([
            'view dashboard',
            'view master data menu', 
            'view master management menu',
            'view customers menu', 'view customer', 'update customer',
            'view role', 'view permission', 'view user',
            'view log', 'view approval-path'
        ]);

        $userRequisitionRole = Role::updateOrCreate(['name' => 'user-requisition']);
        $userRequisitionRole->syncPermissions(['view log', 'view report']);

        $approvalRole = Role::updateOrCreate(['name' => 'user-approval']);
        $approvalRole->syncPermissions(['view log', 'view report', 'view approval']);

        // --- HEAD & STAFF DEPARTMENTS ---
        $deptRoles = [
            'wh-supervisor', 'wh-staff',
            'material-supervisor', 'material-staff',
            'head-SNM', 'staff-SNM',
            'head-R&D', 'staff-R&D',
            'head-QA', 'staff-QA',
            'head-HCD', 'staff-HCD',
            'atasan'
        ];

        $basicPerms = ['view log', 'view report'];

        foreach($deptRoles as $rName) {
            $r = Role::updateOrCreate(['name' => $rName]);
            $r->syncPermissions($basicPerms);
        }

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
                'name' => 'User Requisition', 'nik' => 'AG2222', 'username' => 'user-requisition',
                'password' => Hash::make('password'), 'email_verified_at' => now(),
                'department_id' => 2, 'status' => 'active', 'atasan_nik' => 'AG3333',
                'position_id' => $posGM->id,
            ]
        );
        $userRequsition->assignRole($userRequisitionRole);

        $userApproval = User::updateOrCreate(
            ['email' => 'no-reply@example.com'],
            [
                'name' => 'User Approval', 'nik' => 'AG3333', 'username' => 'user-approval',
                'password' => Hash::make('password'), 'email_verified_at' => now(),
                'department_id' => 3, 'status' => 'active', 'atasan_nik' => 'AG1111',
                'position_id' => $posManager->id,
            ]
        );
        $userApproval->assignRole($approvalRole);

        // --- SALES TEAM ---
        $staffSales1 = User::updateOrCreate(
            ['email' => 'staff.sales1@example.com'],
            [
                'name' => 'Staff SNM 1', 'nik' => 'STSM01', 'username' => 'staff.sales1',
                'password' => Hash::make('password'), 'email_verified_at' => now(),
                'department_id' => 7, 'status' => 'active', 'atasan_nik' => 'HDSM01',
                'position_id' => $posStaff->id,
            ]
        );
        $staffSales1->assignRole('staff-SNM');
        $staffSales1->assignRole($salesRole);

        $staffSales2 = User::updateOrCreate(
            ['email' => 'staff.sales2@example.com'],
            [
                'name' => 'Staff SNM 2', 'nik' => 'STSM02', 'username' => 'staff.sales2',
                'password' => Hash::make('password'), 'email_verified_at' => now(),
                'department_id' => 7, 'status' => 'active', 'atasan_nik' => 'HDSM01',
                'position_id' => $posStaff->id,
            ]
        );
        $staffSales2->assignRole('staff-SNM');
        $staffSales2->assignRole($salesRole);

        // --- FINANCE TEAM ---
        $managerFinance = User::updateOrCreate(
            ['email' => 'ziddanazzahra10@gmail.com'],
            [
                'name' => 'Manager Finance', 'nik' => 'MF001', 'username' => 'manager.finance',
                'password' => Hash::make('password'), 'email_verified_at' => now(),
                'no_telepon' => '081234567890', 'department_id' => 2, 'status' => 'active',
                'atasan_nik' => 'HDFN01', 'position_id' => $posManager->id,
            ]
        );
        $managerFinance->assignRole($managerFinanceRole);

        $headFinance = User::updateOrCreate(
            ['email' => 'head.finance@example.com'],
            [
                'name' => 'Dept Head Finance', 'nik' => 'HDFIN01', 'username' => 'head.finance',
                'password' => Hash::make('password'), 'email_verified_at' => now(),
                'no_telepon' => '081234567891', 'department_id' => 2, 'status' => 'active',
                'atasan_nik' => 'AG1111', 'position_id' => $posManager->id,
            ]
        );
        $headFinance->assignRole($headFinanceRole);

        // --- IT TEAM ---
        $itEngineer = User::updateOrCreate(
            ['email' => 'it.engineer@example.com'],
            [
                'name' => 'IT Engineer', 'nik' => 'IT001', 'username' => 'it.engineer',
                'password' => Hash::make('password'), 'email_verified_at' => now(),
                'department_id' => 1, 'status' => 'active', 'atasan_nik' => 'AG1111',
                'position_id' => $posStaff->id,
            ]
        );
        $itEngineer->assignRole($itRole);
        
        $warehouseUsers = [
            [
                'name' => 'Inward WH Supervisor', 'nik' => 'WH0001', 'username' => 'inward.wh',
                'email' => 'inward.wh@example.com', 'department_id' => 8, 'position_id' => $posSupervisor->id,
            ],
            [
                'name' => 'Material Support Supervisor', 'nik' => 'MS0001', 'username' => 'material.support',
                'email' => 'material.support@example.com', 'department_id' => 8, 'position_id' => $posSupervisor->id,
            ],
            [
                'name' => 'Outward WH Supervisor', 'nik' => 'WH0002', 'username' => 'outward.wh',
                'email' => 'outward.wh@example.com', 'department_id' => 8, 'position_id' => $posSupervisor->id,
            ],
        ];

        foreach ($warehouseUsers as $whUser) {
            $user = User::updateOrCreate(
                ['email' => $whUser['email']],
                [
                    'name' => $whUser['name'], 'nik' => $whUser['nik'], 'username' => $whUser['username'],
                    'password' => Hash::make('password'), 'email_verified_at' => now(),
                    'department_id' => $whUser['department_id'], 'status' => 'active',
                    'atasan_nik' => 'AG1111', 'position_id' => $whUser['position_id'],
                ]
            );
            $user->assignRole($userRequisitionRole);
        }

        Schema::enableForeignKeyConstraints();
    }
}
