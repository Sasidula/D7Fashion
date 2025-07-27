<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\MaterialStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class MaterialStockController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $materials = Material::withCount(['stocks as available_quantity' => function ($query) {
            $query->where('status', 'available');
        }])->get();

        return view('pages.manage-stocks', compact('materials'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $materials = Material::all();
        return view('material_stocks.create', compact('materials'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'material_id' => 'required|exists:materials,id',
            'quantity' => 'required|integer|min:1',
            'status' => 'required|in:available,unavailable,deleted',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Create multiple stock entries based on quantity
        for ($i = 0; $i < $request->quantity; $i++) {
            MaterialStock::create([
                'material_id' => $request->material_id,
                'status' => $request->status,
            ]);
        }

        return redirect()->route('stocks.index')->with('success', 'Stock(s) created.');
    }

    /**
     * manage quantity of the specified resource.
     */
    public function adjustStock(Request $request)
    {
        $validated = $request->validate([
            'material_id' => 'required|exists:materials,id',
            'quantity' => 'required|integer|min:1',
            'action' => 'required|in:delete,restore',
        ]);

        Log::info($validated);

        $materialId = $validated['material_id'];
        $quantity = $validated['quantity'];
        $action = $validated['action'];

        $targetStatus = $action === 'delete' ? 'available' : 'deleted';
        $newStatus = $action === 'delete' ? 'deleted' : 'available';

        $stocks = MaterialStock::where('material_id', $materialId)
            ->where('status', $targetStatus)
            ->limit($quantity)
            ->get();

        Log::info($stocks);

        $count = $stocks->count();

        Log::info($count);

        if ($count < $quantity) {
            return redirect()->route('stocks.manage')->withErrors([
                'quantity' => "Only $count $targetStatus stock(s) can be " . ($action === 'delete' ? 'deleted' : 'restored') . ".",
            ])->withInput();

        }

        foreach ($stocks as $stock) {
            $stock->status = $newStatus;
            $stock->save();
        }
        return redirect()->route('stocks.manage')->with('success', "$quantity stock(s) successfully " . ($action === 'delete' ? 'deleted' : 'restored') . ".");
    }


    public function softDeleteMaterial(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'material_id' => 'required|exists:materials,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Find the material
        $material = Material::findOrFail($request->material_id);

        // Soft delete the material
        $material->delete();

        // Update all related material stock statuses to 'deleted'
        MaterialStock::where('material_id', $material->id)->update([
            'status' => 'deleted'
        ]);

        return redirect()
            ->back()
            ->with('success', 'Material and related stock entries marked as deleted.');
    }

    /**
     * Display the specified resource.
     */
    public function show(MaterialStock $materialStock)
    {
        return view('material_stocks.show', compact('materialStock'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MaterialStock $materialStock)
    {
        $materials = Material::all();
        return view('material_stocks.edit', compact('materialStock', 'materials'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MaterialStock $materialStock)
    {
        $validator = Validator::make($request->all(), [
            'material_id' => 'required|exists:materials,id',
            'status' => 'required|in:available,unavailable,deleted',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $materialStock->update($request->only(['material_id', 'status']));
        return redirect()->route('stocks.manage')->with('success', 'Stock updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MaterialStock $materialStock)
    {
        $materialStock->delete();
        return redirect()->route('stocks.manage')->with('success', 'Stock deleted.');
    }
}
