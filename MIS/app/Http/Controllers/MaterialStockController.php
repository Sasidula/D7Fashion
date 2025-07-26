<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\MaterialStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MaterialStockController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $stocks = MaterialStock::with('material')->get();
        return view('material_stocks.index', compact('stocks'));
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
            'status' => 'required|in:available,unavailable',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        MaterialStock::create($request->only(['material_id', 'status']));
        return redirect()->route('material_stocks.index')->with('success', 'Stock created.');
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
            'status' => 'required|in:available,unavailable',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $materialStock->update($request->only(['material_id', 'status']));
        return redirect()->route('material_stocks.index')->with('success', 'Stock updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MaterialStock $materialStock)
    {
        $materialStock->delete();
        return redirect()->route('material_stocks.index')->with('success', 'Stock deleted.');
    }
}
