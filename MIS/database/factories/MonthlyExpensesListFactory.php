<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MonthlyExpensesList>
 */
class MonthlyExpensesListFactory extends Factory
{
    public function definition() {
        return [
            'title' => $this->faker->word(),
        ];
    }
}
