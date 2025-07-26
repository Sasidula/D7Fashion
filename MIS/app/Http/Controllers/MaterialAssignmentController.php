<?php

namespace App\Http\Controllers;

use App\Models\MaterialAssignment;
use App\Models\MaterialStock;
use App\Models\User;
use Illuminate\Http\Request;

class MaterialAssignmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $assignments = MaterialAssignment::with(['stock', 'user', 'assigner'])->get();
        return view('material_assignments.index', compact('assignments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $stocks = MaterialStock::all();
        $users = User::all();
        return view('material_assignments.create', compact('stocks', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'material_stock_id' => 'required|exists:material_stocks,id',
            'user_id' => 'required|exists:users,id',
            'assigned_by' => 'required|exists:users,id',
            'status' => 'required|in:incomplete,complete',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        MaterialAssignment::create($request->only(['material_stock_id', 'user_id', 'assigned_by', 'status', 'notes']));
        return redirect()->route('material_assignments.index')->with('success', 'Assignment created.');
    }

    /**
     * Display the specified resource.
     */
    public function show(MaterialAssignment $materialAssignment)
    {
        return view('material_assignments.show', compact('materialAssignment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MaterialAssignment $materialAssignment)
    {
        $stocks = MaterialStock::all();
        $users = User::all();
        return view('material_assignments.edit', compact('materialAssignment', 'stocks', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MaterialAssignment $materialAssignment)
    {
        $validator = Validator::make($request->all(), [
            'material_stock_id' => 'required|exists:material_stocks,id',
            'user_id' => 'required|exists:users,id',
            'assigned_by' => 'required|exists:users,id',
            'status' => 'required|in:incomplete,complete',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $materialAssignment->update($request->only(['material_stock_id', 'user_id', 'assigned_by', 'status', 'notes']));
        return redirect()->route('material_assignments.index')->with('success', 'Assignment updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MaterialAssignment $materialAssignment)
    {
        $materialAssignment->delete();
        return redirect()->route('material_assignments.index')->with('success', 'Assignment deleted.');
    }
}
