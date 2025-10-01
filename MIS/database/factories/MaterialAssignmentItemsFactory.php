<?php

namespace Database\Factories;

use App\Models\MaterialAssignmentItems;
use App\Models\MaterialAssignment;
use App\Models\MaterialStock;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MaterialAssignmentItems>
 */
class MaterialAssignmentItemsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = MaterialAssignmentItems::class;

    public function definition(): array {
        return [
            'material_assignment_id' => MaterialAssignment::factory(),
            'material_stock_id' => MaterialStock::inRandomOrder()->first()?->id ?? MaterialStock::factory(),
            'quantity' => $this->faker->numberBetween(1, 20),
        ];
    }
}
