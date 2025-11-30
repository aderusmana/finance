<?php

namespace Database\Factories\Master;

use App\Models\Master\ItemMaster;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ItemMaster>
 */
class ItemMasterFactory extends Factory
{
    protected $model = ItemMaster::class;

    public function definition(): array
    {
        return [
            'item_master_code' => strtoupper($this->faker->unique()->bothify('IM-###')),
            'item_master_name' => $this->faker->words(3, true),
            'unit'             => $this->faker->randomElement(['PCS', 'KG', 'L', 'BOX']),
        ];
    }
}
