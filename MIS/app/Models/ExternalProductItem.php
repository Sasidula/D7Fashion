<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExternalProductItem extends Model {
    use HasFactory;

    protected $fillable = ['external_product_id'];

    public function product() { return $this->belongsTo(ExternalProduct::class); }
}
