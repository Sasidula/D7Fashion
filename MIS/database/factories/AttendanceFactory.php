<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Attendance>
 */
class AttendanceFactory extends Factory
{
    public function definition() {
        return [
            'user_id' => User::where('role', 'employee')->inRandomOrder()->first()->id,
            'date' => $this->faker->date(),
            'check_in' => $this->faker->time('H:i'),
            'check_out' => $this->faker->time('H:i'),
        ];
    }
}
