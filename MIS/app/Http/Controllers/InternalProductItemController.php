<?php

namespace App\Http\Controllers;

use App\Models\InternalProduct;
use App\Models\InternalProductItem;
use App\Models\MaterialAssignment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InternalProductItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = InternalProductItem::with(['product', 'assignment', 'creator'])->get();
        return view('internal_product_items.index', compact('items'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = InternalProduct::all();
        $assignments = MaterialAssignment::all();
        $users = User::all();
        return view('internal_product_items.create', compact('products', 'assignments', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'internal_product_id' => 'required|exists:internal_products,id',
            'assignment_id' => 'required|exists:material_assignments,id',
            'use' => 'required|in:approved,rejected',
            'status' => 'required|in:available,sold',
            'created_by' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        InternalProductItem::create($request->only(['internal_product_id', 'assignment_id', 'use', 'status', 'created_by']));
        return redirect()->route('internal_product_items.index')->with('success', 'Item created.');
    }

    /**
     * Display the specified resource.
     */
    public function show(InternalProductItem $internalProductItem)
    {
        return view('internal_product_items.show', compact('internalProductItem'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(InternalProductItem $internalProductItem)
    {
        $products = InternalProduct::all();
        $assignments = MaterialAssignment::all();
        $users = User::all();
        return view('internal_product_items.edit', compact('internalProductItem', 'products', 'assignments', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, InternalProductItem $internalProductItem)
    {
        $validator = Validator::make($request->all(), [
            'internal_product_id' => 'required|exists:internal_products,id',
            'assignment_id' => 'required|exists:material_assignments,id',
            'use' => 'required|in:approved,rejected',
            'status' => 'required|in:available,sold',
            'created_by' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $internalProductItem->update($request->only(['internal_product_id', 'assignment_id', 'use', 'status', 'created_by']));
        return redirect()->route('internal_product_items.index')->with('success', 'Item updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(InternalProductItem $internalProductItem)
    {
        $internalProductItem->delete();
        return redirect()->route('internal_product_items.index')->with('success', 'Item deleted.');
    }
}
