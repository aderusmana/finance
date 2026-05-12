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
        // 1. Create Permissions (Tetap dipertahankan biar lengkap)
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
            'view customer', 'create customer', 'update customer', 'delete customer', 'import customer',
            'view bg', 'create bg', 'update bg', 'delete bg',
            'view bg-approval', 'approve bg', 'reject bg',
            'view log', 'view report', 'view approval',
            'view approval-path', 'view revision',
            'view customer dashboard', 'view bg dashboard', 'view dashboard area',
            'view logistic fees menu', 'view logistic-fees', 'create logistic-fee', 'update logistic-fee', 'delete logistic-fee',
            'view logistic-orders menu', 'view logistic-orders', 'create logistic-order', 'update logistic-order', 'delete logistic-order',
        ];

        foreach ($permissions as $perm) {
            Permission::updateOrCreate(['name' => $perm]);
        }

        $now = Carbon::now();

        // ==========================================
        // 2. Create Departments
        // ==========================================
        $departments = [
            ['id' => 1, 'name' => 'Board of Directors', 'code' => 'BOD', 'slug' => 'bod'],
            ['id' => 2, 'name' => 'Finance Admin', 'code' => '-', 'slug' => 'finance-admin'],
            ['id' => 7, 'name' => 'Sales & Marketing', 'code' => '5300', 'slug' => 'sales-marketing'],
            ['id' => 10, 'name' => 'Information Technology', 'code' => 'IT', 'slug' => 'it'],
        ];

        foreach ($departments as $dept) {
            Department::updateOrCreate(['id' => $dept['id']], $dept);
        }

        // ==========================================
        // 3. Create Positions
        // ==========================================
        $posDirector = Position::updateOrCreate(['position_name' => 'Director']);
        $posManager = Position::updateOrCreate(['position_name' => 'Manager']);
        $posHead = Position::updateOrCreate(['position_name' => 'Dept Head']);
        $posStaff = Position::updateOrCreate(['position_name' => 'Staff']);

        // ==========================================
        // 4. Create Roles
        // ==========================================

        // Super Admin
        $superAdminRole = Role::updateOrCreate(['name' => 'super-admin']);
        $superAdminRole->givePermissionTo(Permission::all());

        $atasanApprovalRole = Role::updateOrCreate(['name' => 'atasan']);
        $atasanApprovalRole->syncPermissions([
            'view dashboard', 'view customers menu', 'view customer', 'create customer', 'update customer',
            'view bg-approval', 'approve bg', 'reject bg',
            'view approval'
        ]);

        // Finance Roles
        $financePerms = [
            'view dashboard', 'view customers menu', 'view customer', 'create customer', 'update customer', 'delete customer',
            'view bank garansi menu', 'view bg', 'create bg', 'update bg', 'delete bg',
            'view bg-approval', 'approve bg', 'reject bg',
            'view log', 'view report', 'view approval'
        ];

        $managerFinanceRole = Role::updateOrCreate(['name' => 'manager-finance']);
        $managerFinanceRole->syncPermissions($financePerms);

        $headFinanceRole = Role::updateOrCreate(['name' => 'head-finance']);
        $headFinanceRole->syncPermissions($financePerms);

        // Sales Role (Head SNM)
        $headSnmRole = Role::updateOrCreate(['name' => 'head-SNM']);
        $headSnmRole->syncPermissions([
            'view dashboard', 'view customers menu', 'view customer',
            'create customer', 'update customer', 'delete customer', 'view report', 'view approval'
        ]);

        // IT Role
        $itRole = Role::updateOrCreate(['name' => 'it']);
        $itRole->syncPermissions([
            'view dashboard', 'view master data menu', 'view master management menu',
            'view customers menu', 'view customer', 'update customer', 'delete customer',
            'view role', 'view permission', 'view user',
            'view log', 'view approval-path'
        ]);

        // Staff Sales
        $staffSalesRole = Role::updateOrCreate(['name' => 'staff-sales']);
        $staffSalesRole->syncPermissions([
            'view dashboard', 'view customers menu', 'view customer', 'view customer dashboard',
            'create customer', 'update customer', 'delete customer'
        ]);

        // Staff Finance
        $staffFinanceRole = Role::updateOrCreate(['name' => 'staff-finance']);
        $staffFinanceRole->syncPermissions([
            'view dashboard', 'view bg dashboard', 'view bank garansi menu',
            'view bg', 'create bg', 'update bg', 'delete bg',
        ]);

        // ==========================================
        // 5. Create Users (Sesuai List Permintaan)
        // ==========================================

        // 1. Superadmin
        $superAdmin = User::updateOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'name' => 'Super Admin',
                'nik' => 'AG1111',
                'username' => 'superadmin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'department_id' => 10, // IT
                'status' => 'active',
                'atasan_nik' => 'AG2222', // Atasannya User No 6
                'position_id' => $posDirector->id,
            ]
        );
        $superAdmin->assignRole($superAdminRole);

        $atasanApprovalUser = User::updateOrCreate(
            ['email' => 'userapproval@example.com'],
            [
                'name' => 'User Approval',
                'nik' => 'AG2222',
                'username' => 'user.approval',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'department_id' => 10, // IT
                'status' => 'active',
                'atasan_nik' => 'AG1111', // Atasannya User No 6
                'position_id' => $posManager->id,
            ]
        );
        $atasanApprovalUser->assignRole($atasanApprovalRole);

        // 3. Head Finance (Atasannya Manager Finance)
        $headFinance = User::updateOrCreate(
            ['email' => 'head.finance@example.com'],
            [
                'name' => 'Head Finance',
                'nik' => 'HDFN01',
                'username' => 'head.finance',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'no_telepon' => '081234567891',
                'department_id' => 2,
                'status' => 'active',
                'atasan_nik' => 'AG1111', // Langsung ke Director
                'position_id' => $posHead->id,
            ]
        );
        $headFinance->assignRole($headFinanceRole);

        // 2. Manager Finance
        $managerFinance = User::updateOrCreate(
            ['email' => 'ziddanazzahra10@gmail.com'],
            [
                'name' => 'Manager Finance',
                'nik' => 'MF001',
                'username' => 'manager.finance',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'no_telepon' => '081234567890',
                'department_id' => 2,
                'status' => 'active',
                'atasan_nik' => 'AG1111', // Atasannya Head Finance
                'position_id' => $posManager->id,
            ]
        );
        $managerFinance->assignRole($managerFinanceRole);

        // 4. Head SNM (Sales & Marketing)
        $headSnm = User::updateOrCreate(
            ['email' => 'head.snm@example.com'],
            [
                'name' => 'Head SNM',
                'nik' => 'HDSNM01',
                'username' => 'head.snm',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'department_id' => 7,
                'status' => 'active',
                'atasan_nik' => 'AG1111', // Langsung ke Director
                'position_id' => $posHead->id,
            ]
        );
        $headSnm->assignRole($headSnmRole);

        // 5. IT Engineer
        $itUser = User::updateOrCreate(
            ['email' => 'it.engineer@example.com'],
            [
                'name' => 'IT Engineer',
                'nik' => 'IT001',
                'username' => 'it.engineer',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'department_id' => 10,
                'status' => 'active',
                'atasan_nik' => 'AG1111', // Lapor ke Superadmin
                'position_id' => $posStaff->id,
            ]
        );
        $itUser->assignRole($itRole);

        // Staff Sales
        $staffSales = User::updateOrCreate(
            ['email' => 'staff.sales@example.com'],
            [
                'name' => 'Staff Sales',
                'nik' => 'SS001',
                'username' => 'staff.sales',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'department_id' => 7,
                'status' => 'active',
                'atasan_nik' => 'HDSNM01', // Lapor ke Head SNM
                'position_id' => $posStaff->id,
            ]
        );
        $staffSales->assignRole($staffSalesRole);

        // Staff Finance
        $staffFinance = User::updateOrCreate(
            ['email' => 'staff.finance@example.com'],
            [
                'name' => 'Staff Finance',
                'nik' => 'SF001',
                'username' => 'staff.finance',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'department_id' => 2,
                'status' => 'active',
                'atasan_nik' => 'MF001', // Lapor ke Manager Finance
                'position_id' => $posStaff->id,
            ]
        );
        $staffFinance->assignRole($staffFinanceRole);

        Schema::enableForeignKeyConstraints();
    }
}
