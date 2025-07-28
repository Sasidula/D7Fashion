<?php

namespace App\Http\Controllers;

use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MaterialController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $materials = Material::all();
        return view('pages.add-stocks', compact('materials'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'supplier' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        Material::create($request->only(['name', 'supplier', 'description', 'price']));
        return redirect()->route('pages.create-stocks')->with('success', 'Material created.');

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $material = Material::find($request->input('material_id'));

        $validator = Validator::make($request->all(), [
            'material_id' => 'required|exists:materials,id',
            'name' => 'required|string|max:255',
            'supplier' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            $materialData = $material ? $material->only(['id', 'name', 'supplier', 'description', 'price']) : null;

            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput()
                ->with('updatedMaterial', $materialData);
        }

        $material = Material::findOrFail($request->input('material_id'));
        $material->update($request->only(['name', 'supplier', 'description', 'price']));

        return redirect()
            ->route('stocks.manage')
            ->with('status', 'Material updated.')
            ->with('updatedMaterial', $material);
    }





























    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.create-stocks');
    }

    /**
     * Display the specified resource.
     */
    public function show(Material $material)
    {
        return view('pages.manage-stocks', compact('material'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Material $material)
    {
        return view('pages.manage-stocks', compact('material'));
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Material $material)
    {
        $material->delete();
        return redirect()->route('pages.manage-stocks')->with('success', 'Material deleted.');
    }
}
