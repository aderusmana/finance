<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer\Distributor;

class DistributorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $distributors = [
            [
                'code' => 'ID3455',
                'name' => 'PT. CITRA BHOGA JAYA',
                'email' => 'ptcbj_bdg@yahoo.co.id'
            ],
            [
                'code' => 'ID6338',
                'name' => 'PT. SINAR MAYURI',
                'email' => 'admsales4.sinarmayuri@gmail.com'
            ]
        ];

        foreach ($distributors as $item) {
            Distributor::updateOrCreate(
                ['code' => $item['code']], // Patokan agar tidak duplicate
                $item
            );
        }
    }
}
