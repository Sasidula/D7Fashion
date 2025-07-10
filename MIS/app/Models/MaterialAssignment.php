<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialAssignment extends Model {
    use HasFactory;

    protected $fillable = ['material_stock_id', 'user_id', 'assigned_by', 'status', 'notes'];

    public function stock() { return $this->belongsTo(MaterialStock::class); }

    public function user() { return $this->belongsTo(User::class); }

    public function assigner() { return $this->belongsTo(User::class, 'assigned_by'); }
}
