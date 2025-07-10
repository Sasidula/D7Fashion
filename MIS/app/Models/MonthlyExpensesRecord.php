<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonthlyExpensesRecord extends Model {
    use HasFactory;

    protected $fillable = ['expense_id', 'amount', 'type'];

    public function expense() { return $this->belongsTo(MonthlyExpensesList::class, 'expense_id'); }
}
