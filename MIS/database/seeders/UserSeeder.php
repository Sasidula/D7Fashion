<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        User::create([
            'name' => 'sasidula',
            'email' => 'sasidulajayara12@gmail.com',
            'phone_number' => '0761218782',
            'role' => 'admin',
            'salary_type' => 'none',
            'salary_amount' => null,
            'password' => Hash::make('sasidula321'),
        ]);
    }
}
