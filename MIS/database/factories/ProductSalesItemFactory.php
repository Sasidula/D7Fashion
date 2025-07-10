<?php

namespace Database\Factories;

use App\Models\ExternalProductItem;
use App\Models\InternalProductItem;
use App\Models\ProductSale;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductSalesItem>
 */
class ProductSalesItemFactory extends Factory
{
    public function definition() {
        $type = $this->faker->randomElement(['internal', 'external']);
        $product_id = $type === 'internal'
                ? InternalProductItem::inRandomOrder()->first()->id
                : ExternalProductItem::inRandomOrder()->first()->id;

        return [
            'product_sales_id' => ProductSale::inRandomOrder()->first()->id,
            'product_id' => $product_id,
            'product_type' => $type,
        ];
    }
}
