<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaterialAssignmentItems extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['material_assignment_id', 'material_stock_id', 'quantity'];

    public function assignment() {
        return $this->belongsTo(MaterialAssignment::class);
    }

    public function stock() {
        return $this->belongsTo(MaterialStock::class);
    }

    // Add this to access the actual Material through stock
    public function material() {
        return $this->hasOneThrough(
            Material::class,         // final model
            MaterialStock::class,    // intermediate model
            'id',                    // foreign key on MaterialStock (local key for stock)
            'id',                    // foreign key on Material (primary key)
            'material_stock_id',     // local key on this model
            'material_id'            // local key on MaterialStock pointing to Material
        );
    }
}

