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

    //add internal product
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'internal_product_id' => 'required|exists:internal_products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        for ($i = 0; $i < $request->quantity; $i++) {
            InternalProductItem::create([
                'internal_product_id' => $request->internal_product_id,
                'assignment_id' => null,
                'use' => 'approved',
                'status' => 'available',
                'created_by' => auth()->user()->id,
            ]);
        }

        return redirect()->route('products.add.internal')->with('success', 'Internal Product Item(s) created.');
    }


    //manage internal product quantity
    public function adjustStock(Request $request)
    {
        $validated = $request->validate([
            'internal_product_id' => 'required|exists:internal_products,id',
            'quantity' => 'required|integer|min:1',
            'action' => 'required|in:delete,restore',
        ]);

        $InternalProductId = $validated['internal_product_id'];
        $quantity = $validated['quantity'];
        $action = $validated['action'];

        $targetStatus = $action === 'delete' ? 'available' : 'sold';
        $newStatus    = $action === 'delete' ? 'sold' : 'available';

        $items = InternalProductItem::where('internal_product_id', $InternalProductId)
            ->where('status', $targetStatus)
            ->limit($quantity)
            ->get();

        $count = $items->count();

        if ($count < $quantity) {
            return redirect()->route('internal-products.items.manage')->withErrors([
                'quantity' => "Only $count $targetStatus item(s) can be " . ($action === 'delete' ? 'deleted' : 'restored') . ".",
            ])->withInput();
        }

        foreach ($items as $item) {
            $item->status = $newStatus;
            $item->save();
        }

        return redirect()->route('products.manage')
            ->with('success', "$quantity item(s) successfully " . ($action === 'delete' ? 'deleted' : 'restored') . ".");
    }


    //soft delete internal product
    public function softDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'internal_product_id' => 'required|exists:internal_products,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $product = InternalProduct::findOrFail($request->internal_product_id);
        $product->delete();

        InternalProductItem::where('internal_product_id', $product->id)
            ->update(['status' => 'deleted']);

        return redirect()->back()->with('success', 'Product and related items marked as deleted.');
    }






























    /**
     * Display a listing of the resource.
     */
    public function indexnotused()
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
    public function storenotused(Request $request)
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
