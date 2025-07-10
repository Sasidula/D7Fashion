<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EmployeeBonusAdjustment>
 */
class EmployeeBonusAdjustmentFactory extends Factory
{
    public function definition() {
        return [
            'user_id' => User::where('role', 'employee')->inRandomOrder()->first()->id,
            'title' => $this->faker->word(),
            'amount' => $this->faker->numberBetween(100, 1000),
            'action' => $this->faker->randomElement(['add', 'remove']),
        ];
    }
}
