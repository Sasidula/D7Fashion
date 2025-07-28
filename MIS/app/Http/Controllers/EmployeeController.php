<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;

class EmployeeController extends Controller
{

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:6',
            'phone_number' => 'required|digits:10',
            'email' => 'required|email|unique:users,email',
            'salary_amount' => 'required|numeric|min:0',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone_number' => $validated['phone_number'],
            'role' => 'employee',
            'salary_type' => 'hourly',
            'salary_amount' => $validated['salary_amount'],
        ]);

        return Redirect::route('dashboard', ['page' => 'add-employee'])
            ->with('success', 'Employee created successfully!');
    }

    public function index()
    {
        $employees = User::where('role', 'employee')->get();
        return view('pages.manage-employee', compact('employees'));
    }



    /**
     * Update the user's profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone_number' => 'nullable|string',
            'salary_amount' => 'nullable|numeric',
        ]);

        $employee = User::find($validated['employee_id']);
        $employee->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone_number' => $validated['phone_number'],
            'salary_amount' => $validated['salary_amount'],
        ]);

        return redirect()->route('employees.index')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'employee_id' => 'required|exists:users,id',
        ]);

        $user = User::findOrFail($request->employee_id);

        // Prevent deleting own account (optional safety check)
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('employees.index')->with('deleted', 'employee-deleted');
    }


    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:users,id',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $employee = User::find($validated['employee_id']);
        $employee->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('employees.index')->with('status', 'password-updated');
    }


}
