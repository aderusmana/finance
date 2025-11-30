<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ApprovalPathSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1. Path untuk SnM Requester (sub_category: SNM_PATH)
        // Alur: Atasan (SnM Manager) -> Business Controller
        $snmPathSequence = [ 
            [
                "level" => 1,
                "type" => "atasan" // SnM Manager (atasan requester)
            ],
            [
                "level" => 2,
                "type" => "role",
                "value" => "user-approval" // Business Controller
            ]
        ];

        DB::table('approval_paths')->updateOrInsert(
            ['category' => 'FREE GOODS', 'sub_category' => 'SNM_PATH'],
            [
                'category' => 'FREE GOODS',
                'sub_category' => 'SNM_PATH',
                'sequence_approvers' => json_encode($snmPathSequence),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        
        // 2. Path untuk Non-SnM Requester (sub_category: NON_SNM_PATH)
        // Alur: HCD Dept. Head -> Business Controller (TIDAK ADA ATASAN)
        $nonSnmPathSequence = [
            [
                "level" => 1,
                "type" => "role",
                "value" => "head-HCD" // HCD Dept. Head
            ],
            [
                "level" => 2,
                "type" => "role",
                "value" => "user-approval" // Business Controller
            ]
        ];

        DB::table('approval_paths')->updateOrInsert(
            ['category' => 'FREE GOODS', 'sub_category' => 'NON_SNM_PATH'],
            [
                'category' => 'FREE GOODS',
                'sub_category' => 'NON_SNM_PATH',
                'sequence_approvers' => json_encode($nonSnmPathSequence),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // --- TAMBAHKAN PATH DEFAULT UNTUK KATEGORI SAMPLE ---
        $sampleDefaultSequence = [
             [
                "level" => 1,
                "type" => "atasan" 
            ],
        ];

        DB::table('approval_paths')->updateOrInsert(
            ['category' => 'SAMPLE', 'sub_category' => 'Packaging'],
            [
                'category' => 'SAMPLE',
                'sub_category' => 'Packaging',
                'sequence_approvers' => json_encode($sampleDefaultSequence), 
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}