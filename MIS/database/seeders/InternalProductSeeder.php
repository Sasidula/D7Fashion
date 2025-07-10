<?php

namespace Database\Seeders;

use App\Models\InternalProduct;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InternalProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        InternalProduct::insert([
            ['name' => 'Plain T-Shirt', 'price' => 500.00, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Formal Shirt', 'price' => 800.00, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
