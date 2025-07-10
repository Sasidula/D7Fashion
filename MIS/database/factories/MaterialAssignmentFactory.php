<?php

namespace Database\Factories;

use App\Models\MaterialStock;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MaterialAssignment>
 */
class MaterialAssignmentFactory extends Factory
{
    public function definition() {
        $user = User::where('role', 'employee')->inRandomOrder()->first();
    return [
        'material_stock_id' => MaterialStock::inRandomOrder()->first()->id,
        'user_id' => $user->id,
        'assigned_by' => User::where('role', '!=', 'employee')->inRandomOrder()->first()->id,
        'status' => $this->faker->randomElement(['incomplete', 'complete']),
        'notes' => $this->faker->sentence(),
    ];
}
}
