<?php

namespace Database\Seeders;

use App\Models\Master\ItemDetail;
use App\Models\Master\ItemMaster;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Matikan pemeriksaan Foreign Key
        Schema::disableForeignKeyConstraints();

        // 2. Bersihkan tabel terkait sebelum mengisi data baru
        // Urutan pembersihan PENTING: harus dari anak ke induk
        DB::table('item_details')->truncate();
        DB::table('item_masters')->truncate();
        
        // 3. Hidupkan kembali pemeriksaan Foreign Key
        Schema::enableForeignKeyConstraints();

        // 4. Buat data baru menggunakan factory
        ItemMaster::factory()
            ->count(50)
            ->create()
            ->each(function ($master) {
                ItemDetail::factory()
                    ->count(5)
                    ->create([
                        'item_master_id' => $master->id,
                    ]);
            });
    }
}