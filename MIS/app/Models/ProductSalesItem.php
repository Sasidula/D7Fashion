<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSalesItem extends Model {
    use HasFactory;

    protected $fillable = ['product_sales_id', 'product_id', 'product_type'];

    public function sale() { return $this->belongsTo(ProductSale::class); }
}
