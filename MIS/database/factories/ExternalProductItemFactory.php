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
    public function definition()
    {
        $product = ExternalProduct::inRandomOrder()->first();
        $user = User::where('role', '!=', 'employee')->inRandomOrder()->first();

        return [
            'external_product_id' => $product->id,
            'bought_price' => $product->bought_price, // ✅ snapshot from product
            'sold_price' => $product->sold_price,     // ✅ snapshot from product
            'status' => $this->faker->randomElement(['available', 'sold']),
            'created_by' => $user->id,
        ];
    }

}
