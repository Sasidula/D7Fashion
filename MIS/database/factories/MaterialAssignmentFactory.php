<?php

namespace Database\Factories;

use App\Models\MaterialAssignment;
use App\Models\MaterialStock;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MaterialAssignment>
 */
class MaterialAssignmentFactory extends Factory
{

    protected $model = MaterialAssignment::class;

    public function definition(): array {
        $employee = User::where('role', 'employee')->inRandomOrder()->first();
        $assigner = User::where('role', '!=', 'employee')->inRandomOrder()->first();

        return [
            'user_id' => $employee ? $employee->id : User::factory()->create(['role' => 'employee'])->id,
            'assigned_by' => $assigner ? $assigner->id : User::factory()->create(['role' => 'manager'])->id,
            'status' => $this->faker->randomElement(['incomplete', 'complete']),
            'notes' => $this->faker->sentence(),
        ];
    }

}
