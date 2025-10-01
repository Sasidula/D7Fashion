<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InternalProductItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'internal_product_id',
        'assignment_id',
        'price',
        'use',
        'status',
        'created_by',
    ];

    public function internalProduct()
    {
        return $this->belongsTo(InternalProduct::class)->withTrashed();
    }

    public function assignment()
    {
        return $this->belongsTo(MaterialAssignment::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by')->withTrashed();
    }

    /**
     * Automatically snapshot price from the internal product when creating.
     */
    protected static function booted()
    {
        static::creating(function ($item) {
            if (!isset($item->price)) {
                $product = InternalProduct::withTrashed()->find($item->internal_product_id);
                if ($product) {
                    $item->price = $product->price;
                }
            }
        });
    }
}

