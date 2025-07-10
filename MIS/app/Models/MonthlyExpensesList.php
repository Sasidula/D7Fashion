<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonthlyExpensesList extends Model {
    use HasFactory;

    protected $fillable = ['title'];

    public function records() { return $this->hasMany(MonthlyExpensesRecord::class, 'expense_id'); }
}
