<?php

namespace App\Http\Controllers;

use App\Models\ExternalProduct;
use App\Models\ExternalProductItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExternalProductItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = ExternalProductItem::with(['external_product', 'creator'])->get();
        return view('external_product_items.index', compact('items'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = ExternalProduct::all();
        $users = User::all();
        return view('external_product_items.create', compact('products', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'external_product_id' => 'required|exists:external_products,id',
            'status' => 'required|in:available,sold',
            'created_by' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        ExternalProductItem::create($request->only(['external_product_id', 'status', 'created_by']));
        return redirect()->route('external_product_items.index')->with('success', 'Item created.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ExternalProductItem $externalProductItem)
    {
        return view('external_product_items.show', compact('externalProductItem'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ExternalProductItem $externalProductItem)
    {
        $products = ExternalProduct::all();
        $users = User::all();
        return view('external_product_items.edit', compact('externalProductItem', 'products', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ExternalProductItem $externalProductItem)
    {
        $validator = Validator::make($request->all(), [
            'external_product_id' => 'required|exists:external_products,id',
            'status' => 'required|in:available,sold',
            'created_by' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $externalProductItem->update($request->only(['external_product_id', 'status', 'created_by']));
        return redirect()->route('external_product_items.index')->with('success', 'Item updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ExternalProductItem $externalProductItem)
    {
        $externalProductItem->delete();
        return redirect()->route('external_product_items.index')->with('success', 'Item deleted.');
    }
}
