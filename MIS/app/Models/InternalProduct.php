<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InternalProduct extends Model {
    use HasFactory;

    protected $fillable = ['name', 'price'];

    public function items() { return $this->hasMany(InternalProductItem::class); }
}
