<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSalesItem extends Model {
    use HasFactory;

    protected $fillable = ['product_sales_id', 'product_id', 'product_type'];

    public function sale() { return $this->belongsTo(ProductSale::class); }

    public function internalProductItem()
    {
        return $this->belongsTo(InternalProductItem::class, 'product_id')
            ->where('product_type', 'internal');
    }

    public function externalProductItem()
    {
        return $this->belongsTo(ExternalProductItem::class, 'product_id')
            ->where('product_type', 'external');
    }

    public function getActualProductItemAttribute()
    {
        return $this->product_type === 'internal'
            ? InternalProductItem::find($this->product_id)
            : ExternalProductItem::find($this->product_id);
    }


}


