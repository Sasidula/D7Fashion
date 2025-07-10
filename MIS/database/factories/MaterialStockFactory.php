<?php

namespace Database\Factories;

use App\Models\Material;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MaterialStock>
 */
class MaterialStockFactory extends Factory
{
    public function definition() {
        return [
            'material_id' => Material::inRandomOrder()->first()->id,
            'status' => $this->faker->randomElement(['available', 'unavailable']),
    ];
}
}
