<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSale extends Model {
    use HasFactory;

    protected $fillable = ['price'];

    public function items() { return $this->hasMany(ProductSalesItem::class); }
}
