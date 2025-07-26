<?php

namespace App\Http\Controllers;

use App\Models\ProductSale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductSaleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sales = ProductSale::with('items')->get();
        return view('product_sales.index', compact('sales'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('product_sales.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        ProductSale::create($request->only(['price']));
        return redirect()->route('product_sales.index')->with('success', 'Sale created.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductSale $productSale)
    {
        return view('product_sales.show', compact('productSale'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductSale $productSale)
    {
        return view('product_sales.edit', compact('productSale'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProductSale $productSale)
    {
        $validator = Validator::make($request->all(), [
            'price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $productSale->update($request->only(['price']));
        return redirect()->route('product_sales.index')->with('success', 'Sale updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductSale $productSale)
    {
        $productSale->delete();
        return redirect()->route('product_sales.index')->with('success', 'Sale deleted.');
    }
}
