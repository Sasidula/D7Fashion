<?php

namespace Database\Seeders;

use App\Models\Material;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MaterialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Material::insert([
            ['name' => 'Cotton Fabric', 'price' => 100.00, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Polyester Fabric', 'price' => 80.00, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Buttons', 'price' => 5.00, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
