<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExternalProduct extends Model {
    use HasFactory;

    protected $fillable = ['name', 'bought_price', 'sold_price'];

    public function items() { return $this->hasMany(ExternalProductItem::class); }
}

