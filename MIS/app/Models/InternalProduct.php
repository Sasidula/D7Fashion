<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InternalProduct extends Model {
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'description', 'sku_code', 'price', 'status',
    ];

    public function items() { return $this->hasMany(InternalProductItem::class); }
}
