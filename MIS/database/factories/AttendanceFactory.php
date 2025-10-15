<?php

namespace Database\Factories;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Attendance>
 */
class AttendanceFactory extends Factory
{
    public function definition()
    {
        $user = User::where('role', 'employee')->inRandomOrder()->first();

        $checkIn = $this->faker->time('H:i');
        $checkOut = Carbon::parse($checkIn)->addHours(rand(6, 10))->format('H:i');

        return [
            'user_id' => $user->id,
            'date' => $this->faker->date(),
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'salary_type' => $user->salary_type,
            'salary_rate' => $user->salary_amount ?? 0.00,
            'calculated_salary' => 0.00, // will be updated after check_in/out via method
        ];
    }

}
