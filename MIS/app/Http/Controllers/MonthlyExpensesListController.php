<?php

namespace App\Http\Controllers;

use App\Models\MonthlyExpensesList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MonthlyExpensesListController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $expenses = MonthlyExpensesList::withTrashed()->get();
        return view('monthly_expenses_lists.index', compact('expenses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('monthly_expenses_lists.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        MonthlyExpensesList::create($request->only(['title']));
        return redirect()->route('monthly_expenses_lists.index')->with('success', 'Expense list created.');
    }

    /**
     * Display the specified resource.
     */
    public function show(MonthlyExpensesList $monthlyExpensesList)
    {
        return view('monthly_expenses_lists.show', compact('monthlyExpensesList'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MonthlyExpensesList $monthlyExpensesList)
    {
        return view('monthly_expenses_lists.edit', compact('monthlyExpensesList'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MonthlyExpensesList $monthlyExpensesList)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $monthlyExpensesList->update($request->only(['title']));
        return redirect()->route('monthly_expenses_lists.index')->with('success', 'Expense list updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MonthlyExpensesList $monthlyExpensesList)
    {
        $monthlyExpensesList->delete();
        return redirect()->route('monthly_expenses_lists.index')->with('success', 'Expense list deleted.');
    }
}
