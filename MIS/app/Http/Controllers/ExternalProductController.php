<?php

namespace App\Http\Controllers;

use App\Models\ExternalProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class ExternalProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = ExternalProduct::withTrashed()->get();
        return view('external_products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('external_products.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'supplier' => 'nullable|string|max:255',
            'sku_code' => 'nullable|string|max:255',
            'bought_price' => 'required|numeric|min:0',
            'sold_price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        ExternalProduct::create($request->only(['name', 'description', 'supplier', 'sku_code', 'bought_price', 'sold_price']));
        return redirect()->route('external_products.index')->with('success', 'Product created.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ExternalProduct $externalProduct)
    {
        return view('external_products.show', compact('externalProduct'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ExternalProduct $externalProduct)
    {
        return view('external_products.edit', compact('externalProduct'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ExternalProduct $externalProduct)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'supplier' => 'nullable|string|max:255',
            'sku_code' => 'nullable|string|max:255',
            'bought_price' => 'required|numeric|min:0',
            'sold_price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $externalProduct->update($request->only(['name', 'description', 'supplier', 'sku_code', 'bought_price', 'sold_price']));
        return redirect()->route('external_products.index')->with('success', 'Product updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ExternalProduct $externalProduct)
    {
        $externalProduct->delete();
        return redirect()->route('external_products.index')->with('success', 'Product deleted.');
    }
}
