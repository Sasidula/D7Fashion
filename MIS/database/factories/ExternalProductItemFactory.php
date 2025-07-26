<?php

namespace Database\Factories;

use App\Models\ExternalProduct;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ExternalProductItem>
 */
class ExternalProductItemFactory extends Factory
{
    public function definition() {
        return [
            'external_product_id' => ExternalProduct::inRandomOrder()->first()->id,
            'status' => $this->faker->randomElement(['available', 'sold']),
            'created_by' => User::where('role', '!=', 'employee')->inRandomOrder()->first()->id,
        ];
    }
}
