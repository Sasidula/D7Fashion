<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExternalProductItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'external_product_id',
        'bought_price',
        'sold_price',
        'status',
        'created_by',
    ];

    public function external_product()
    {
        return $this->belongsTo(ExternalProduct::class)->withTrashed();
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by')->withTrashed();
    }

    /**
     * Automatically snapshot prices from the external product when creating
     */
    protected static function booted()
    {
        static::creating(function ($item) {
            if (!isset($item->bought_price) || !isset($item->sold_price)) {
                $product = ExternalProduct::withTrashed()->find($item->external_product_id);
                if ($product) {
                    $item->bought_price = $product->bought_price;
                    $item->sold_price = $product->sold_price;
                }
            }
        });
    }
}
