<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExternalProductItem extends Model {
    use HasFactory;

    protected $fillable = ['external_product_id','status', 'created_by'];

    public function external_product() { return $this->belongsTo(ExternalProduct::class)->withTrashed(); }

    public function creator() { return $this->belongsTo(User::class, 'created_by')->withTrashed(); }
}
