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
            [
                'name' => 'Imported Jacket',
                'description' => 'Imported denim jeans',
                'supplier' => 'XYZ Traders',
                'sku_code' => 'EXT-1001',
                'bought_price' => 1200.00,
                'sold_price' => 1800.00,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Branded Hoodie',
                'description' => 'Imported denim jeans',
                'supplier' => 'XYZ Traders',
                'sku_code' => 'EXT-1002',
                'bought_price' => 900.00,
                'sold_price' => 1500.00,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'casual Hoodie',
                'description' => 'Imported denim jeans',
                'supplier' => 'XYZ Traders',
                'sku_code' => 'EXT-1003',
                'bought_price' => 900.00,
                'sold_price' => 1500.00,
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }
}
