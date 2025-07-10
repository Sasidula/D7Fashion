<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        $salaryTypes = ['hourly', 'monthly'];

        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone_number' => $this->faker->phoneNumber(),
            'role' => 'employee',
            'salary_type' => $type = $this->faker->randomElement($salaryTypes),
            'salary_amount' => $type === 'hourly'
                ? $this->faker->randomFloat(2, 300.00, 1000.00)
                : $this->faker->randomFloat(2, 20000.00, 60000.00),
            'email_verified_at' => now(),
            'password' => Hash::make('password'), // default password
            'remember_token' => Str::random(10),
        ];
    }
}
