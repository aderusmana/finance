<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Master\ApprovalPath;
use Spatie\Permission\Models\Role;

class BgLampiranDApprovalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!Role::where('name', 'manager-finance')->exists()) {
            Role::create(['name' => 'manager-finance', 'guard_name' => 'web']);
        }

        ApprovalPath::updateOrCreate(
            [
                'category'     => 'BG',
                'sub_category' => 'Lampiran D',
            ],
            [
                'sequence_approvers' => ['manager-finance'],

                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $this->command->info('Approval Path [BG - Lampiran D] berhasil dibuat untuk Manager Finance.');
    }
}
