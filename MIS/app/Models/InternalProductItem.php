<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InternalProductItem extends Model {
    use HasFactory;

    protected $fillable = ['internal_product_id', 'assignment_id', 'use', 'status', 'created_by'];

    public function internalProduct() { return $this->belongsTo(InternalProduct::class)->withTrashed(); }

    public function assignment() { return $this->belongsTo(MaterialAssignment::class); }

    public function creator() { return $this->belongsTo(User::class, 'created_by')->withTrashed(); }
}
