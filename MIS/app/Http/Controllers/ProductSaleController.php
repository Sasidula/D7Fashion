<?php

namespace App\Http\Controllers;

use App\Models\ExternalProduct;
use App\Models\ExternalProductItem;
use App\Models\InternalProduct;
use App\Models\InternalProductItem;
use App\Models\ProductSale;
use App\Models\ProductSalesItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ProductSaleController extends Controller
{
    public function products()
    {
        $Internalproducts = InternalProduct::withCount([
            'items as product_count' => function ($query) {
                $query->where('status', 'available');
                $query->where('use', 'approved');
            }
        ])->get();

        $Externalproducts = ExternalProduct::withCount([
            'items as product_count' => function ($query) {
                $query->where('status', 'available');
            }
        ])->get();

        return view('pages.counter', compact('Internalproducts', 'Externalproducts'));

    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'price' => 'required|numeric|min:0',
            // Remove 'products' from here, we’ll validate manually
        ]);

        $products = json_decode($request->input('products'), true);
        Log::info('products:-');
        Log::info($products);

        if (!is_array($products)) {
            return back()->withErrors(['products' => 'Products must be a valid array.']);
        }

        DB::beginTransaction();

        try {
            $sale = ProductSale::create([
                'price' => $validated['price'],
            ]);

            foreach ($products as $product) {

                $count = $product['quantity'];

                for ($i = 0; $i < $count; $i++) {

                    ProductSalesItem::create([
                        'product_sales_id' => $sale->id,
                        'product_id' => $product['id'],
                        'product_type' => $product['type'],
                    ]);

                    if ($product['type'] === 'internal') {
                        $item = InternalProductItem::where('internal_product_id', $product['id'])
                            ->where('status', 'available')
                            ->firstOrFail();
                    } else {
                        $item = ExternalProductItem::where('external_product_id', $product['id'])
                            ->where('status', 'available')
                            ->firstOrFail();
                    }

                    $item->update(['status' => 'sold']);
                }
            }

            DB::commit();
            return redirect()->back()->with('success', 'Products marked as sold successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to mark products as sold. ' . $e->getMessage());
        }
    }










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
    public function storeunused(Request $request)
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
    public function showunused(ProductSale $productSale)
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
