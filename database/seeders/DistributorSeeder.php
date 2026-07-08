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
            ],
            [
                'code' => 'ID5879',
                'name' => 'PT. PRIMATRANS NUSANTARA',
                'email' => 'pmlpsrg@gmail.com'
            ],
            [
                'code' => 'ID5880',
                'name' => 'PT. TRIBOGHA SENTOSA JAYA',
                'email' => 'erni.tsj@gmail.com'
            ],
            [
                'code' => 'ID2536',
                'name' => 'CV. TIRA PERSADA',
                'email' => 'merapigroup.pusat@yahoo.co.id'
            ],
            [
                'code' => 'ID6389',
                'name' => 'PT. TUJUH BERLIAN SAKTI',
                'email' => 'malangtbs@gmail.com'
            ],
            [
                'code' => 'ID5913',
                'name' => 'CV. SINAR GEMILANG',
                'email' => 'rahma1920@yahoo.com'
            ],
            [
                'code' => 'ID5920',
                'name' => 'UD. MITRA KENCANA',
                'email' => 'adm.mitrakencana@gmail.com'
            ],
            [
                'code' => 'ID6331',
                'name' => 'CV. SURYA PUTRA PERKASA',
                'email' => 'suryaputraperkasa25@gmail.com'
            ],
            [
                'code' => 'ID2643',
                'name' => 'PT. ANEKA MULTI AROMA',
                'email' => 'amaco.mdn@gmail.com'
            ],
            [
                'code' => 'ID6341',
                'name' => 'PT. PAN BARUNA',
                'email' => 'admspv.pb.pku@prp-group.co.id'
            ],
            [
                'code' => 'ID3370',
                'name' => 'PT. CAHAYA MAHAKAM SAMARINDA',
                'email' => 'ragilzikri.novandi@cmsgroup.co.id'
            ],
            [
                'code' => 'ID6352',
                'name' => 'CV. BOGA SWARNA TIMUR',
                'email' => 'office.bogaswarnatimur@gmail.com'
            ],
            [
                'code' => 'ID3100',
                'name' => 'CV. PAYUNG MAS SEJAHTERA',
                'email' => 'payungmassolo@gmail.com'
            ],
            [
                'code' => 'ID5100',
                'name' => 'PT. PRATAMA ABADI SANTOSO',
                'email' => 'spvoprtgl.pas@gmail.com'
            ],
            [
                'code' => 'ID5282',
                'name' => 'CV. TRIJAYA PERKASA',
                'email' => 'cv.trijaya_perkasa@ymail.com'
            ]
        ];

        foreach ($distributors as $item) {
            Distributor::updateOrCreate(
                ['code' => $item['code']],
                $item
            );
        }
    }
}
