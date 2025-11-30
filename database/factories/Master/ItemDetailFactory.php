<?php

namespace Database\Factories\Master;

use App\Models\Master\ItemDetail;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ItemDetail>
 */
class ItemDetailFactory extends Factory
{
    protected $model = ItemDetail::class;
    public function definition(): array
    {
        return [
            'material_type'    => $this->faker->randomElement(['Raw', 'Semi-Finished', 'Finished']),
            'item_detail_code' => strtoupper($this->faker->unique()->bothify('ID-####')),
            'item_detail_name' => $this->faker->words(2, true),
            'unit'             => $this->faker->randomElement(['PCS', 'KG', 'L', 'BOX']),
            'net_weight'       => $this->faker->randomFloat(2, 0.1, 100),
        ];
    }
}
