<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PettyCash>
 */
class PettyCashFactory extends Factory
{
    public function definition() {
        return [
            'title' => $this->faker->sentence(3),
            'amount' => $this->faker->numberBetween(100, 2000),
            'type' => $this->faker->randomElement(['income', 'expense']),
        ];
    }
}
