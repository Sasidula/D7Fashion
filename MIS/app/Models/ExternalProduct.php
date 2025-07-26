<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExternalProduct extends Model {
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'description', 'sku_code', 'supplier', 'bought_price', 'sold_price', 'status',
    ];

    public function items() { return $this->hasMany(ExternalProductItem::class); }
}

