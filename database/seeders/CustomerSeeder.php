<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer\Customer;
use App\Models\Customer\CustomerItem;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        $userId = User::first()->id ?? 1;

        for ($i = 0; $i < 10; $i++) {

            DB::beginTransaction();
            try {
                $bgStatus = ($i < 5) ? 'YA' : $faker->randomElement(['YA', 'TIDAK']);

                // 1. Create Customer
                $customer = Customer::create([
                    'user_id' => $userId,
                    'created_by' => $userId,
                    'code' => 'CUST-' . strtoupper($faker->bothify('??###')),
                    'name' => $faker->company,
                    'sort_name' => $faker->companySuffix,
                    'customer_class' => $faker->numberBetween(1, 5),
                    'account_group' => $faker->numberBetween(1, 3),

                    'address1' => $faker->streetAddress,
                    'address2' => "Gedung " . $faker->word . " Lantai " . $faker->randomDigitNotNull,
                    'address3' => "Kawasan Industri " . $faker->city,
                    'city' => $faker->city,
                    'postal_code' => $faker->postcode,
                    'country' => 'Indonesia',
                    'area' => $faker->state,

                    'shipping_to_name' => $faker->name,
                    'shipping_to_address' => $faker->address,

                    'purchasing_manager_name' => $faker->name,
                    'purchasing_manager_email' => $faker->email,
                    'finance_manager_name' => $faker->name,
                    'finance_manager_email' => $faker->email,

                    'penagihan_nama_kontak' => $faker->name,
                    'penagihan_telepon' => $faker->phoneNumber,
                    'penagihan_address' => $faker->address,
                    'surat_menyurat_address' => $faker->address,
                    'email' => $faker->companyEmail,

                    'tax_contact_name' => $faker->name,
                    'tax_contact_email' => $faker->email,
                    'tax_contact_phone' => $faker->phoneNumber,
                    'npwp' => $faker->numerify('##.###.###.#-###.###'),
                    'tanggal_npwp' => $faker->date(),
                    'nppkp' => $faker->numerify('##.###.###.#-###.###'),
                    'tanggal_nppkp' => $faker->date(),
                    'no_pengukuhan_kaber' => $faker->bothify('PKP-####/##'),
                    'output_tax' => $faker->randomElement(['PPN', 'NON-PPN', 'Terhutang PPN']),
                    'term_of_payment' => $faker->randomElement([30, 45, 60, 90]),
                    'lead_time' => $faker->numberBetween(7, 30),
                    'credit_limit' => $faker->randomFloat(2, 100000000, 5000000000),
                    'ccar' => $faker->sentence(3),

                    // Gunakan variabel hasil logic di atas
                    'bank_garansi' => $bgStatus,

                    'join_date' => $faker->dateTimeBetween('-5 years', 'now'),
                    'status' => 'active',
                    'status_approval' => 'approved',
                    'route_to' => '-',
                    'pembagian' => 'Wilayah ' . $faker->randomDigitNotNull,
                    'customer_total' => $faker->numberBetween(1, 10),
                ]);

                // 2. Create Customer Items
                $jumlahItem = $faker->numberBetween(3, 5);

                for ($j = 0; $j < $jumlahItem; $j++) {
                    CustomerItem::create([
                        'customer_id' => $customer->id,
                        'item_name' => ucwords($faker->words(3, true)),
                        'quantity' => $faker->numberBetween(10, 1000),
                        'price' => $faker->randomFloat(2, 50000, 5000000),
                    ]);
                }

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                $this->command->error("Error creating customer: " . $e->getMessage());
            }
        }
    }
}
