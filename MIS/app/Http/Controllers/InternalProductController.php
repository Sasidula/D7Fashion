<?php

namespace App\Http\Controllers;

use App\Models\ExternalProduct;
use App\Models\InternalProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InternalProductController extends Controller
{
    //all products
    public function allProducts()
    {
        $internalProducts =  InternalProduct::withCount([
            'items as available_quantity' => function ($query) {
                $query->where('status', 'available');
            }
        ])->get();

        $externalProducts = ExternalProduct::withCount([
            'items as available_quantity' => function ($query) {
                $query->where('status', 'available');
        }])->get();

        return view('pages.manage-product', compact('internalProducts', 'externalProducts'));
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = InternalProduct::all();
        return view('pages.add-internal-product', compact('products'));
    }

    //create internal product
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'sku_code'    => 'nullable|string|max:100',
            'price'       => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        InternalProduct::create($request->only(['name', 'description', 'sku_code', 'price']));
        return redirect()->route('products.create.internal')->with('success', 'Internal Product created.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $internalProduct = InternalProduct::find($request->input('internal_product_id'));

        $validator = Validator::make($request->all(), [
            'internal_product_id' => 'required|exists:internal_products,id',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'sku_code' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            $internalProductData = $internalProduct ? $internalProduct->only(['id', 'name', 'price', 'description', 'sku_code']) : null;

            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput()
                ->with('updatedItem', $internalProductData);
        }

        $internalProduct = InternalProduct::findOrFail($request->input('internal_product_id'));
        $internalProduct->update($request->only(['name', 'price', 'description', 'sku_code']));

        return redirect()
            ->route('products.manage')
            ->with('success', 'Product updated.')
            ->with('updatedInternalProduct', $internalProduct);
    }



































    /**
     * Remove the specified resource from storage.
     */
    public function destroy(InternalProduct $internalProduct)
    {
        $internalProduct->delete();
        return redirect()->route('internal-products.index')->with('success', 'Internal Product deleted.');
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
    public function storeunused(Request $request)
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


}
