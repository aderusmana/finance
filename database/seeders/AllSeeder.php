<?php

namespace Database\Seeders;

use App\Models\Master\Department;
use App\Models\Master\Revision;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AllSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        // Create Permissions - Menggunakan updateOrCreate
        Permission::updateOrCreate(['name' => 'view role']);
        Permission::updateOrCreate(['name' => 'create role']);
        Permission::updateOrCreate(['name' => 'update role']);
        Permission::updateOrCreate(['name' => 'delete role']);

        Permission::updateOrCreate(['name' => 'view permission']);
        Permission::updateOrCreate(['name' => 'create permission']);
        Permission::updateOrCreate(['name' => 'update permission']);
        Permission::updateOrCreate(['name' => 'delete permission']);

        Permission::updateOrCreate(['name' => 'view user']);
        Permission::updateOrCreate(['name' => 'create user']);
        Permission::updateOrCreate(['name' => 'update user']);
        Permission::updateOrCreate(['name' => 'delete user']);

        Permission::updateOrCreate(['name' => 'view department']);
        Permission::updateOrCreate(['name' => 'create department']);
        Permission::updateOrCreate(['name' => 'update department']);
        Permission::updateOrCreate(['name' => 'delete department']);

        Permission::updateOrCreate(['name' => 'view requisition']);
        Permission::updateOrCreate(['name' => 'create requisition']);
        Permission::updateOrCreate(['name' => 'update requisition']);
        Permission::updateOrCreate(['name' => 'delete requisition']);

        Permission::updateOrCreate(['name' => 'view approval']);

        Permission::updateOrCreate(['name' => 'view item']);
        Permission::updateOrCreate(['name' => 'create item']);
        Permission::updateOrCreate(['name' => 'update item']);
        Permission::updateOrCreate(['name' => 'delete item']);


        Permission::updateOrCreate(['name' => 'view customer']);
        Permission::updateOrCreate(['name' => 'create customer']);
        Permission::updateOrCreate(['name' => 'update customer']);
        Permission::updateOrCreate(['name' => 'delete customer']);
        
        // ! permision untuk page requisition
        Permission::updateOrCreate(['name' => 'view log']);
        Permission::updateOrCreate(['name' => 'view requisition-form']);
        Permission::updateOrCreate(['name' => 'view report']);
        Permission::updateOrCreate(['name' => 'view requisition-approval']);
        
        // ! permision untuk master data (approval path dan revision)
        Permission::updateOrCreate(['name' => 'view approval-path']);
        Permission::updateOrCreate(['name' => 'view revision']);

        Permission::updateOrCreate(['name' => 'approve requisition']);
        Permission::updateOrCreate(['name' => 'reject requisition']);

        Permission::updateOrCreate(['name' => 'view dashboard']);

        $now = Carbon::now();
        $departments = [
            [
                'id' => 1,
                'name' => 'Engineering & Maintenance',
                'code' => '-',
                'slug' => 'engineering-maintainance',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'name' => 'Finance Admin',
                'code' => '-',
                'slug' => 'finance-admin',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 3,
                'name' => 'HCD',
                'code' => '-',
                'slug' => 'hcd',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 4,
                'name' => 'Manufacturing',
                'code' => '-',
                'slug' => 'manufacturing',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 5,
                'name' => 'QM & HSE',
                'code' => '5302',
                'slug' => 'qm-hse',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 6,
                'name' => 'R&D',
                'code' => '5302',
                'slug' => 'rd',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 7,
                'name' => 'Sales & Marketing',
                'code' => '5300',
                'slug' => 'sales-marketing',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 8,
                'name' => 'Supply Chain',
                'code' => '-',
                'slug' => 'supply-chain',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 9,
                'name' => 'Supply & Maintenance',
                'code' => '-',
                'slug' => 'supply-and-maintenance',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        foreach ($departments as $dept) {
            Department::updateOrCreate(['id' => $dept['id']], $dept);
        }

        // Create Revision Data
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

         //! Create Roles - Menggunakan updateOrCreate
        $superAdminRole = Role::updateOrCreate(['name' => 'super-admin']); //as super-admin
        $userRequisitionRole = Role::updateOrCreate(['name' => 'user-requisition']); // as user-requisition
        $approvalRole = Role::updateOrCreate(['name' => 'user-approval']); // as head-department

        //* wh-supervisor
        $headWhRole = Role::create(['name' => 'wh-supervisor']);
        $headWhRole->givePermissionTo([
            'view log',
            'view requisition-form',
            'view report',
            'view requisition-approval', 
            'approve requisition', 
            'reject requisition']);

        $staffWhRole = Role::create(['name' => 'wh-staff']);
        $staffWhRole->givePermissionTo([
            'view log',
            'view requisition-form',
            'view report',
            'view requisition', 
            'create requisition', 
            'update requisition', 
            'delete requisition']);

        // * material
        $headMaterialRole = Role::create(['name' => 'material-supervisor']);
        $headMaterialRole->givePermissionTo([
            'view log',
            'view requisition-form',
            'view report',
            'view requisition-approval', 
            'approve requisition', 
            'reject requisition']);

        $staffMaterialRole = Role::create(['name' => 'material-staff']);
        $staffMaterialRole->givePermissionTo([
            'view log',
            'view requisition-form',
            'view report',
            'view requisition', 
            'create requisition', 
            'update requisition', 
            'delete requisition']);
        
        //* Sales & Marketing
        $headSalesMarketingRole = Role::updateOrCreate(['name' => 'head-SNM']);
        $headSalesMarketingRole->givePermissionTo([
            'view log',
            'view requisition-form',
            'view report',
            'view requisition-approval',
            'approve requisition',
            'reject requisition']);

        $staffSalesMarketingRole = Role::updateOrCreate(['name' => 'staff-SNM']);
        $staffSalesMarketingRole->givePermissionTo([
            'view log',
            'view requisition-form',
            'view report',
            'view requisition',
            'create requisition',
            'update requisition',
            'delete requisition']);

        //* R&D
        $headRndRole = Role::updateOrCreate(['name' => 'head-R&D']);
        $headRndRole->givePermissionTo([
            'view log',
            'view requisition-form',
            'view report',
            'view requisition-approval',
            'approve requisition',
            'reject requisition']);

        $staffRndRole = Role::updateOrCreate(['name' => 'staff-R&D']);
        $staffRndRole->givePermissionTo([
            'view log',
            'view requisition-form',
            'view report',
            'view requisition',
            'create requisition',
            'update requisition',
            'delete requisition']);

        //* QA
        $headQaRole = Role::updateOrCreate(['name' => 'head-QA']);
        $headQaRole->givePermissionTo([
            'view log',
            'view requisition-form',
            'view report',
            'view requisition-approval',
            'approve requisition',
            'reject requisition']);

        $staffQaRole = Role::updateOrCreate(['name' => 'staff-QA']);
        $staffQaRole->givePermissionTo([
            'view log',
            'view requisition-form',
            'view report',
            'view requisition',
            'create requisition',
            'update requisition',
            'delete requisition']);

        //* HCD
        $headHcdRole = Role::updateOrCreate(['name' => 'head-HCD']);
        $headHcdRole->givePermissionTo([
            'view log',
            'view requisition-form',
            'view report',
            'view requisition-approval',
            'approve requisition',
            'reject requisition']);

        $staffHcdRole = Role::updateOrCreate(['name' => 'staff-HCD']);
        $staffHcdRole->givePermissionTo([
            'view log',
            'view requisition-form',
            'view report',
            'view requisition',
            'create requisition',
            'update requisition',
            'delete requisition']);

        $atasanRole = Role::updateOrCreate(['name' => 'atasan']);
        $atasanRole->givePermissionTo([
            'view log',
            'view requisition-form',
            'view report',
            'view requisition-approval', 
            'approve requisition', 
            'reject requisition']);

        //! Create Users and Assign Roles

        //* wh-Users
        $headWh = User::updateOrCreate([
            'email' => 'head.wh@example.com'],[
            'name' => 'Head WH', 
            'nik' => 'HDWH01', 
            'username' => 'head.wh', 
            'password' => Hash::make('password'),
            'email_verified_at' => now(), 
            'department_id' => 9, 
            'status' => 'active', 
            'atasan_nik' => 'AG1111',
            ]);
        $headWh->assignRole($headWhRole);

        $staffWh1 = User::updateOrCreate([
            'email' => 'staff.wh1@example.com'],[
            'name' => 'Staff WH 1', 
            'nik' => 'STWH01', 
            'username' => 'staff.wh1', 
            'password' => Hash::make('password'),
            'email_verified_at' => now(), 
            'department_id' => 9, 
            'status' => 'active', 
            'atasan_nik' => 'HDWH01',
            ]);
        $staffWh1->assignRole($staffWhRole);

        $headMaterial = User::updateOrCreate([
            'email' => 'head.material@example.com'],[
            'name' => 'Head Material', 
            'nik' => 'HDMT01', 
            'username' => 'head.material', 
            'password' => Hash::make('password'),
            'email_verified_at' => now(), 
            'department_id' => 8, 
            'status' => 'active', 
            'atasan_nik' => 'AG1111',
            ]);
        $headMaterial->assignRole($headMaterialRole);

        $staffMaterial1 = User::updateOrCreate([
            'email' => 'staff.material1@example.com'],[
            'name' => 'Staff Material 1', 
            'nik' => 'STMT01', 
            'username' => 'staff.material1', 
            'password' => Hash::make('password'),
            'email_verified_at' => now(), 
            'department_id' => 8, 
            'status' => 'active', 
            'atasan_nik' => 'HDMT01',
            ]);
        $staffMaterial1->assignRole($staffMaterialRole);
        
        //* Sales & Marketing Users
        $headSales = User::updateOrCreate([
            'email' => 'head.sales@example.com'
        ], [
            'name' => 'Head SNM',
            'nik' => 'HDSM01',
            'username' => 'head.sales',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'department_id' => 7,
            'status' => 'active',
            'atasan_nik' => 'AG1111',
        ]);
        $headSales->assignRole($headSalesMarketingRole);

        $staffSales1 = User::updateOrCreate([
            'email' => 'staff.sales1@example.com'
        ], [
            'name' => 'Staff SNM 1',
            'nik' => 'STSM01',
            'username' => 'staff.sales1',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'department_id' => 7,
            'status' => 'active',
            'atasan_nik' => 'HDSM01',
        ]);
        $staffSales1->assignRole($staffSalesMarketingRole);

        $staffSales2 = User::updateOrCreate([
            'email' => 'staff.sales2@example.com'
        ], [
            'name' => 'Staff SNM 2',
            'nik' => 'STSM02',
            'username' => 'staff.sales2',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'department_id' => 7,
            'status' => 'active',
            'atasan_nik' => 'HDSM01',
        ]);
        $staffSales2->assignRole($staffSalesMarketingRole);

        //* R&D Users
        $headRnd = User::updateOrCreate([
            'email' => 'head.rnd@example.com'
        ], [
            'name' => 'Head R&D',
            'nik' => 'HDRD01',
            'username' => 'head.rnd',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'department_id' => 6,
            'status' => 'active',
            'atasan_nik' => 'AG1111',
        ]);
        $headRnd->assignRole($headRndRole);

        $staffRnd1 = User::updateOrCreate([
            'email' => 'staff.rnd1@example.com'
        ], [
            'name' => 'Staff R&D 1',
            'nik' => 'STRD01',
            'username' => 'staff.rnd1',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'department_id' => 6,
            'status' => 'active',
            'atasan_nik' => 'HDRD01',
        ]);
        $staffRnd1->assignRole($staffRndRole);

        $staffRnd2 = User::updateOrCreate([
            'email' => 'staff.rnd2@example.com'
        ], [
            'name' => 'Staff R&D 2',
            'nik' => 'STRD02',
            'username' => 'staff.rnd2',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'department_id' => 6,
            'status' => 'active',
            'atasan_nik' => 'HDRD01',
        ]);
        $staffRnd2->assignRole($staffRndRole);

        //* QA Users
        $headQa = User::updateOrCreate([
            'email' => 'head.qa@example.com'
        ], [
            'name' => 'Head QA',
            'nik' => 'HDQA01',
            'username' => 'head.qa',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'department_id' => 5,
            'status' => 'active',
            'atasan_nik' => 'AG1111',
        ]);
        $headQa->assignRole($headQaRole);

        $staffQa1 = User::updateOrCreate([
            'email' => 'staff.qa1@example.com'
        ], [
            'name' => 'Staff QA 1',
            'nik' => 'STQA01',
            'username' => 'staff.qa1',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'department_id' => 5,
            'status' => 'active',
            'atasan_nik' => 'HDQA01',
        ]);
        $staffQa1->assignRole($staffQaRole);

        $staffQa2 = User::updateOrCreate([
            'email' => 'staff.qa2@example.com'
        ], [
            'name' => 'Staff QA 2',
            'nik' => 'STQA02',
            'username' => 'staff.qa2',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'department_id' => 5,
            'status' => 'active',
            'atasan_nik' => 'HDQA01',
        ]);
        $staffQa2->assignRole($staffQaRole);

        //* HCD Users
        $headHcd = User::updateOrCreate([
            'email' => 'head.hcd@example.com'
        ], [
            'name' => 'Head HCD',
            'nik' => 'HDHCD01',
            'username' => 'head.hcd',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'department_id' => 3,
            'status' => 'active',
            'atasan_nik' => 'AG1111',
        ]);
        $headHcd->assignRole($headHcdRole);

        $staffHcd1 = User::updateOrCreate([
            'email' => 'staff.hcd1@example.com'
        ], [
            'name' => 'Staff HCD 1',
            'nik' => 'STHCD01',
            'username' => 'staff.hcd1',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'department_id' => 3,
            'status' => 'active',
            'atasan_nik' => 'HDHCD01',
        ]);
        $staffHcd1->assignRole($staffHcdRole);

        $staffHcd2 = User::updateOrCreate([
            'email' => 'staff.hcd2@example.com'
        ], [
            'name' => 'Staff HCD 2',
            'nik' => 'STHCD02',
            'username' => 'staff.hcd2',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'department_id' => 3,
            'status' => 'active',
            'atasan_nik' => 'HDHCD01',
        ]);
        $staffHcd2->assignRole($staffHcdRole);

        $atasan1 = User::updateOrCreate([
            'email' => 'atasan1@example.com'],[
            'name' => 'Atasan 1',
            'nik' => 'ATASAN01',
            'username' => 'atasan1',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'department_id' => 1,
            'status' => 'active',
            'atasan_nik' => 'AG1111',
        ]);
        $atasan1->assignRole($atasanRole);

        // Lets give all permission to super-admin role.
        $allPermissionNames = Permission::pluck('name')->toArray();
        
        // User Requisition Role - basic permissions
        $userRequisitionRole->givePermissionTo([
            'view log',
            'view requisition-form',
            'view report',
            'view requisition',
            'create requisition',
            'update requisition',
            'delete requisition',
        ]);
        
        // Approval Role - untuk head department (approval permissions)
        $approvalRole->givePermissionTo([
            'view log',
            'view requisition-form',
            'view report',
            'view approval',
            'view requisition-approval',
            'approve requisition',
            'reject requisition',
        ]);

        // Super Admin - full access termasuk master data
        $superAdminRole->givePermissionTo($allPermissionNames);
        // Baris yang dihapus karena berpotensi error:
        // $userRequisitionRole->givePermissionTo($userRequisitionRole); 
        // $approvalRole->givePermissionTo($approvalRole);

        $superAdminUser = User::updateOrCreate(
            ['email' => 'superadmin@example.com'],
            [
            'name' => 'Super Admin',
            'nik' => 'AG1111',
            'username' => 'superadmin',
            'email' => 'superadmin@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'department_id' => 1,
            'status' => 'active',
            'atasan_nik' => 'AG2222',
            'avatar' => null,
            ]
        );

        $superAdminUser->assignRole($superAdminRole);

        $userRequsition = User::updateOrCreate(
            ['email' => 'user-requisition@example.com'],
            [
            'name' => 'User Requisition',
            'nik' => 'AG2222',
            'username' => 'user-requisition',
            'email' => 'user-requisition@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'department_id' => 2,
            'status' => 'active',
            'atasan_nik' => 'AG1111',
            'avatar' => null,
            ]
        );

        $userRequsition->assignRole($userRequisitionRole);

        $userApproval = User::updateOrCreate(
            ['email' => 'no-reply@example.com'],
            [
            'name' => 'User Approval',
            'nik' => 'AG3333',
            'username' => 'user-approval',
            'email' => 'no-reply@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'department_id' => 3,
            'status' => 'active',
            'atasan_nik' => 'AG1111',
            'avatar' => null,
            ]
        );
        $userApproval->assignRole($approvalRole);

        $anotherUserRequisition = User::updateOrCreate(
            ['email' => 'staff.eng@example.com'],
            [
                'name' => 'Staff Engineering',
                'nik' => 'ST0001',
                'username' => 'staff.eng',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'department_id' => 7,
                'status' => 'active',
                'atasan_nik' => 'HD0001',
            ]
        );
        $anotherUserRequisition->assignRole('user-requisition');

        // Users for Warehouse roles
        $warehouseUsers = [
            [
                'name' => 'Inward WH Supervisor',
                'nik' => 'WH0001',
                'username' => 'inward.wh',
                'email' => 'inward.wh@example.com',
                'department_id' => 8, // Asumsi Dept. Supply Chain
            ],
            [
                'name' => 'Material Support Supervisor',
                'nik' => 'MS0001',
                'username' => 'material.support',
                'email' => 'material.support@example.com',
                'department_id' => 8, // Asumsi Dept. Supply Chain
            ],
            [
                'name' => 'Outward WH Supervisor',
                'nik' => 'WH0002',
                'username' => 'outward.wh',
                'email' => 'outward.wh@example.com',
                'department_id' => 8, // Asumsi Dept. Supply Chain
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
                ]
            );

            $user->assignRole('user-requisition');
        }



    }

}