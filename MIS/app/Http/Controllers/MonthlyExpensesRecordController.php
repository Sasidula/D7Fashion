<?php

namespace App\Http\Controllers;

use App\Models\MonthlyExpensesList;
use App\Models\MonthlyExpensesRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MonthlyExpensesRecordController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $records = MonthlyExpensesRecord::with('expense')->get();
        return view('monthly_expenses_records.index', compact('records'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $expenses = MonthlyExpensesList::all();
        return view('monthly_expenses_records.create', compact('expenses'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'expense_id' => 'required|exists:monthly_expenses_lists,id',
            'amount' => 'required|numeric|min:0',
            'type' => 'required|in:income,expense',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        MonthlyExpensesRecord::create($request->only(['expense_id', 'amount', 'type']));
        return redirect()->route('monthly_expenses_records.index')->with('success', 'Record created.');
    }

    /**
     * Display the specified resource.
     */
    public function show(MonthlyExpensesRecord $monthlyExpensesRecord)
    {
        return view('monthly_expenses_records.show', compact('monthlyExpensesRecord'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MonthlyExpensesRecord $monthlyExpensesRecord)
    {
        $expenses = MonthlyExpensesList::all();
        return view('monthly_expenses_records.edit', compact('monthlyExpensesRecord', 'expenses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MonthlyExpensesRecord $monthlyExpensesRecord)
    {
        $validator = Validator::make($request->all(), [
            'expense_id' => 'required|exists:monthly_expenses_lists,id',
            'amount' => 'required|numeric|min:0',
            'type' => 'required|in:income,expense',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $monthlyExpensesRecord->update($request->only(['expense_id', 'amount', 'type']));
        return redirect()->route('monthly_expenses_records.index')->with('success', 'Record updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MonthlyExpensesRecord $monthlyExpensesRecord)
    {
        $monthlyExpensesRecord->delete();
        return redirect()->route('monthly_expenses_records.index')->with('success', 'Record deleted.');
    }
}
