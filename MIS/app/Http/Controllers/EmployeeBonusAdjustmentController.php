<?php

namespace App\Http\Controllers;

use App\Models\EmployeeBonusAdjustment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class EmployeeBonusAdjustmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $employees = User::where('role', 'employee')->get();
        return view('pages.add-bonus-deduction', compact('employees'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::all();
        return view('pages.add-bonus-deduction', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate and automatically retrieve validated data
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'action' => 'required|in:add,remove',
        ]);

        // Store the bonus adjustment
        EmployeeBonusAdjustment::create($validated);

        return redirect()->route('employees.bonus')->with('success', 'Adjustment created.');
    }

    /**
     * Display the specified resource.
     */
    public function show(EmployeeBonusAdjustment $employeeBonusAdjustment)
    {
        return view('employee_bonus_adjustments.show', compact('employeeBonusAdjustment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EmployeeBonusAdjustment $employeeBonusAdjustment)
    {
        $users = User::all();
        return view('employee_bonus_adjustments.edit', compact('employeeBonusAdjustment', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, EmployeeBonusAdjustment $employeeBonusAdjustment)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'action' => 'required|in:add,remove',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $employeeBonusAdjustment->update($request->only(['user_id', 'title', 'amount', 'action']));
        return redirect()->route('employee_bonus_adjustments.index')->with('success', 'Adjustment updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EmployeeBonusAdjustment $employeeBonusAdjustment)
    {
        $employeeBonusAdjustment->delete();
        return redirect()->route('employee_bonus_adjustments.index')->with('success', 'Adjustment deleted.');
    }
}
