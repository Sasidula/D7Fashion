<?php

namespace App\Http\Controllers;

use App\Models\ExternalProductItem;
use App\Models\InternalProductItem;
use App\Models\ProductSale;
use App\Models\ProductSalesItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductSalesItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = ProductSalesItem::with(['sale', 'internalProductItem', 'externalProductItem'])->get();
        return view('product_sales_items.index', compact('items'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $sales = ProductSale::all();
        $internalItems = InternalProductItem::all();
        $externalItems = ExternalProductItem::all();
        return view('product_sales_items.create', compact('sales', 'internalItems', 'externalItems'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_sales_id' => 'required|exists:product_sales,id',
            'product_id' => 'required|numeric',
            'product_type' => 'required|in:internal,external',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        ProductSalesItem::create($request->only(['product_sales_id', 'product_id', 'product_type']));
        return redirect()->route('product_sales_items.index')->with('success', 'Sale item created.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductSalesItem $productSalesItem)
    {
        return view('product_sales_items.show', compact('productSalesItem'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductSalesItem $productSalesItem)
    {
        $sales = ProductSale::all();
        $internalItems = InternalProductItem::all();
        $externalItems = ExternalProductItem::all();
        return view('product_sales_items.edit', compact('productSalesItem', 'sales', 'internalItems', 'externalItems'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProductSalesItem $productSalesItem)
    {
        $validator = Validator::make($request->all(), [
            'product_sales_id' => 'required|exists:product_sales,id',
            'product_id' => 'required|numeric',
            'product_type' => 'required|in:internal,external',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $productSalesItem->update($request->only(['product_sales_id', 'product_id', 'product_type']));
        return redirect()->route('product_sales_items.index')->with('success', 'Sale item updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductSalesItem $productSalesItem)
    {
        $productSalesItem->delete();
        return redirect()->route('product_sales_items.index')->with('success', 'Sale item deleted.');
    }
}
