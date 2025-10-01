<?php

namespace Database\Factories;

use App\Models\Material;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MaterialStock>
 */
class MaterialStockFactory extends Factory
{
    public function definition()
    {
        // Pick a random material
        $material = Material::inRandomOrder()->first();

        return [
            'material_id' => $material->id,
            'price' => $material->price, // ✅ snapshot price from material table
            'status' => $this->faker->randomElement(['available', 'unavailable']),
        ];
    }
}
