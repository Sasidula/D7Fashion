<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialStock extends Model
{
    use HasFactory;

    protected $fillable = ['material_id', 'status', 'price'];

    public function material()
    {
        return $this->belongsTo(Material::class)->withTrashed();
    }

    /**
     * Automatically set price snapshot when creating a new MaterialStock
     */
    protected static function booted()
    {
        static::creating(function ($stock) {
            // Only snapshot if price not already manually set
            if (!isset($stock->price)) {
                $material = Material::withTrashed()->find($stock->material_id);
                if ($material) {
                    $stock->price = $material->price;
                }
            }
        });
    }
}

