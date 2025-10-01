<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialAssignment extends Model {
    use HasFactory;

    protected $fillable = ['user_id', 'assigned_by', 'status', 'notes'];

    public function user() {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function assigner() {
        return $this->belongsTo(User::class, 'assigned_by')->withTrashed();
    }

    public function items() {
        return $this->hasMany(MaterialAssignmentItems::class);
    }

    public function material() {
        return $this->belongsTo(Material::class);
    }
}
