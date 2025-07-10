<?php

namespace Database\Seeders;

use App\Models\ExternalProduct;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExternalProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        ExternalProduct::insert([
            ['name' => 'Imported Jacket', 'bought_price' => 1200.00, 'sold_price' => 1800.00, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Branded Hoodie', 'bought_price' => 900.00, 'sold_price' => 1500.00, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
