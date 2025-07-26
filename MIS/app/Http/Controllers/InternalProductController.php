<?php

namespace App\Http\Controllers;

use App\Models\InternalProduct;
use Illuminate\Http\Request;

class InternalProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = InternalProduct::withTrashed()->get();
        return view('internal_products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('internal_products.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'sku_code' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        InternalProduct::create($request->only(['name', 'price', 'description', 'sku_code']));
        return redirect()->route('internal_products.index')->with('success', 'Product created.');
    }

    /**
     * Display the specified resource.
     */
    public function show(InternalProduct $internalProduct)
    {
        return view('internal_products.show', compact('internalProduct'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(InternalProduct $internalProduct)
    {
        return view('internal_products.edit', compact('internalProduct'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, InternalProduct $internalProduct)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'sku_code' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $internalProduct->update($request->only(['name', 'price', 'description', 'sku_code']));
        return redirect()->route('internal_products.index')->with('success', 'Product updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(InternalProduct $internalProduct)
    {
        $internalProduct->delete();
        return redirect()->route('internal_products.index')->with('success', 'Product deleted.');
    }
}
