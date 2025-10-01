<?php

namespace Database\Factories;

use App\Models\InternalProduct;
use App\Models\MaterialAssignment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InternalProductItem>
 */
class InternalProductItemFactory extends Factory
{
    public function definition()
    {
        $product = InternalProduct::inRandomOrder()->first();
        $assignment = MaterialAssignment::inRandomOrder()->first();
        $user = User::where('role', '!=', 'employee')->inRandomOrder()->first();

        return [
            'internal_product_id' => $product->id,
            'assignment_id' => $assignment->id,
            'price' => $product->price, // ✅ snapshot of internal product price
            'use' => $this->faker->randomElement(['approved', 'rejected']),
            'status' => $this->faker->randomElement(['available', 'sold']),
            'created_by' => $user->id,
        ];
    }

}
