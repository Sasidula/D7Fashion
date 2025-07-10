<?php

namespace Database\Factories;

use App\Models\MonthlyExpensesList;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MonthlyExpensesRecord>
 */
class MonthlyExpensesRecordFactory extends Factory
{
    public function definition() {
        return [
            'expense_id' => MonthlyExpensesList::inRandomOrder()->first()->id,
            'amount' => $this->faker->numberBetween(200, 3000),
            'type' => $this->faker->randomElement(['income', 'expense']),
        ];
    }
}
