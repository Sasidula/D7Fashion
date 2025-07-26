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
    public function definition() {
        return [
            'internal_product_id' => InternalProduct::inRandomOrder()->first()->id,
            'assignment_id' => MaterialAssignment::inRandomOrder()->first()->id,
            'use' => $this->faker->randomElement(['approved', 'rejected']),
            'status' => $this->faker->randomElement(['available', 'sold']),
            'created_by' => User::where('role', '!=', 'employee')->inRandomOrder()->first()->id,
        ];
    }
}
