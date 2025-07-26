<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MonthlyExpensesList extends Model {
    use HasFactory, SoftDeletes;

    protected $fillable = ['title'];

    public function records() { return $this->hasMany(MonthlyExpensesRecord::class, 'expense_id'); }
}
