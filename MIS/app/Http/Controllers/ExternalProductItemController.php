<?php

namespace App\Http\Controllers;

use App\Models\ExternalProduct;
use App\Models\ExternalProductItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExternalProductItemController extends Controller
{

    //store quantity
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'external_product_id' => 'required|exists:external_products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        for ($i = 0; $i < $request->quantity; $i++) {
            ExternalProductItem::create([
                'external_product_id' => $request->external_product_id,
                'status' => 'available',
                'created_by' => auth()->user()->id,
            ]);
        }

        return redirect()->route('products.add.external')->with('success', 'External items created.');
    }


    //manage quantity
    public function adjustStock(Request $request)
    {
        $validated = $request->validate([
            'external_product_id' => 'required|exists:external_products,id',
            'quantity' => 'required|integer|min:1',
            'action' => 'required|in:delete,restore',
        ]);

        $targetStatus = $validated['action'] === 'delete' ? 'available' : 'sold';
        $newStatus = $validated['action'] === 'delete' ? 'sold' : 'available';

        $items = ExternalProductItem::where('external_product_id', $validated['external_product_id'])
            ->where('status', $targetStatus)
            ->limit($validated['quantity'])
            ->get();

        $count = $items->count();

        if ($count < $validated['quantity']) {
            return redirect()->route('products.manage')->withErrors([
                'quantity' => "Only $count $targetStatus item(s) can be " . ($validated['action'] === 'delete' ? 'deleted' : 'restored') . ".",
            ])->withInput();
        }

        foreach ($items as $item) {
            $item->status = $newStatus;
            $item->save();
        }

        return redirect()->route('products.manage')->with('success', "$count item(s) successfully " . ($validated['action'] === 'delete' ? 'deleted' : 'restored') . ".");
    }


    //delete product
    public function softDeleteExternalProduct(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'external_product_id' => 'required|exists:external_products,id',
        ]);

        if($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $product = ExternalProduct::findOrFail($request->external_product_id);
        $product->delete();

        ExternalProductItem::where('external_product_id', $product->id)->update([
            'status' => 'deleted'
        ]);

        return redirect()
            ->back()
            ->with('success', 'Product and related items marked as deleted.');
    }














    /**
     * Display a listing of the resource.
     */
    public function indexnotused()
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
