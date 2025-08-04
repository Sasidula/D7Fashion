<?php

namespace App\Http\Controllers;

use App\Models\InternalProduct;
use App\Models\InternalProductItem;
use App\Models\Material;
use App\Models\MaterialAssignment;
use App\Models\MaterialStock;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MaterialAssignmentController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $employees = User::where('role', 'employee')->get();

        return view('pages.accept-assignment', compact('employees'));
    }


    public function createindex()
    {
        $materials = Material::withCount(['stocks as available_quantity' => function ($query) {
            $query->where('status', 'available');
        }])->get();
        $users = User::where('role', 'employee')->get();

        return view('pages.add-assignment', compact('materials', 'users'));
    }



    public function reviewIndex()
    {
        $products = InternalProduct::withCount([
            'items as reviewing_count' => function ($query) {
                $query->where('use', 'reviewing');
            }
        ])->get();

        $rejectedproducts = InternalProduct::withCount([
            'items as rejected_count' => function ($query) {
                $query->where('use', 'rejected');
            }
        ])->get();

        $completedassignments = InternalProductItem::with([
            'assignment.user',
            'internalProduct'
        ])->where('use', 'reviewing')->get();

        $rejectedassignments = InternalProductItem::with([
            'assignment.user',
            'internalProduct'
        ])->where('use', 'rejected')->get();

        return view('pages.manage-assignment', compact(
            'products',
            'rejectedproducts',
            'completedassignments',
            'rejectedassignments'
        ));
    }




    public function tobecompletedindex(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $assignments = MaterialAssignment::query()
            ->select('materials.id as material_id', 'materials.name', \DB::raw('count(material_assignments.id) as assignment_count'))
            ->join('material_stocks', 'material_assignments.material_stock_id', '=', 'material_stocks.id')
            ->join('materials', 'material_stocks.material_id', '=', 'materials.id')
            ->where('material_assignments.status', 'incomplete')
            ->where('material_assignments.user_id', $request->user_id)
            ->groupBy('materials.id', 'materials.name')
            ->get();

        $fullAssignments = MaterialAssignment::where('material_assignments.status', 'incomplete')
            ->where('material_assignments.user_id', $request->user_id)
            ->count();

        $products = InternalProduct::all();

        $user = User::find($request->user_id);

        return view('pages.accept-assignment', [
            'employees' => User::where('role', 'employee')->get(),
            'assignments' => $assignments,
            'user' => $user,
            'fullAssignments' => $fullAssignments,
            'availableProducts' => InternalProduct::withCount(['items as available_count' => function ($query) {
                $query->where('status', 'available');
            }])->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'material_id' => 'required|exists:materials,id',
            'user_id' => 'required|exists:users,id',
            'notes' => 'nullable|string',
            'quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();

        try {
            $availableStocks = MaterialStock::where('material_id', $request->material_id)
                ->where('status', 'available')
                ->take($request->quantity)
                ->lockForUpdate()
                ->get();

            if ($availableStocks->count() < $request->quantity) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Not enough available stock for the requested quantity.');
            }

            foreach ($availableStocks as $stock) {
                $stock->update(['status' => 'unavailable']);

                MaterialAssignment::create([
                    'material_stock_id' => $stock->id,
                    'user_id' => $request->user_id,
                    'assigned_by' => auth()->id(),
                    'status' => 'incomplete',
                    'notes' => $request->notes,
                ]);
            }

            DB::commit();

            return redirect()->route('page.assignments.accept')->with('success', 'Assignment created.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }



    /**
     * Update the specified resource quantity in storage.
     */
    public function updateassignment(Request $request)
    {
        $validated = $request->validate([
            'material_id' => 'required|exists:materials,id',
            'user_id' => 'required|exists:users,id',
            'quantity' => 'required|integer|min:1',
            'action' => 'required|in:delete,restore',
        ]);

        $materialId = $validated['material_id'];
        $userId = $validated['user_id'];
        $quantity = $validated['quantity'];
        $action = $validated['action'];


        $targetStatus = $action === 'delete' ? 'incomplete' : 'deleted';
        $newStatus = $action === 'delete' ? 'deleted' : 'incomplete';

        $assignments = MaterialAssignment::query()
            ->select('material_assignments.*', 'materials.name as material_name')
            ->join('material_stocks', 'material_assignments.material_stock_id', '=', 'material_stocks.id')
            ->join('materials', 'material_stocks.material_id', '=', 'materials.id')
            ->where('material_assignments.status', $targetStatus)
            ->where('material_assignments.user_id', $userId)
            ->where('material_stocks.material_id', $materialId)
            ->limit($quantity)
            ->get();

        $count = $assignments->count();

        if ($count < $quantity) {
            return redirect()->route('page.assignments.accept')->withErrors([
                'quantity' => "Only $count $targetStatus assignment(s) can be " . ($action === 'delete' ? 'deleted' : 'restored') . ".",
            ])->withInput();
        }

        DB::beginTransaction();
        try {
            foreach ($assignments as $assignment) {

                if ($action === 'delete') {
                    $assignment->update([
                        'status' => $newStatus,
                    ]);
                    MaterialStock::where('id', $assignment->material_stock_id)->update(['status' => 'available']);
                }else{
                    $materialItem = MaterialStock::where('material_id', $materialId)
                        ->where('status', 'available')
                        ->first();

                    if (!$materialItem) {
                        return redirect()->route('page.assignments.accept')->withErrors([
                            'stock' => "No available stock found for this material.",
                        ]);
                    }
                    $materialItem->update(['status' => 'unavailable']);
                    $assignment->update([
                        'status' => $newStatus,
                        'material_stock_id' => $materialItem->id,
                    ]);
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('page.assignments.accept')->withErrors(['error' => 'Update failed.']);
        }

        return redirect()->route('page.assignments.accept')->with('success', "$count $targetStatus assignment(s) " . ($action === 'delete' ? 'deleted' : 'restored') . ".");
    }


    public function complete(Request $request)
    {
        $validated = $request->validate([
            'material_id' => 'required|exists:materials,id',
            'user_id' => 'required|exists:users,id',
            'internal_product_id' => 'required|exists:internal_products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $materialId = $validated['material_id'];
        $userId = $validated['user_id'];
        $internalProductId = $validated['internal_product_id'];
        $quantity = $validated['quantity'];

        $assignments = MaterialAssignment::query()
            ->select('material_assignments.*', 'materials.name as material_name')
            ->join('material_stocks', 'material_assignments.material_stock_id', '=', 'material_stocks.id')
            ->join('materials', 'material_stocks.material_id', '=', 'materials.id')
            ->where('material_assignments.status', 'incomplete')
            ->where('material_assignments.user_id', $userId)
            ->where('material_stocks.material_id', $materialId)
            ->limit($quantity)
            ->get();

        $count = $assignments->count();

        if ($count < $quantity) {
            return redirect()->route('page.assignments.accept')->withErrors([
                'quantity' => "Only $count incomplete assignment(s) can be marked as complete.",
            ])->withInput();
        }

        DB::transaction(function () use ($assignments, $internalProductId) {
            foreach ($assignments as $assignment) {
                $assignment->update([
                    'status' => 'complete',
                ]);

                InternalProductItem::create([
                    'internal_product_id' => $internalProductId,
                    'assignment_id' => $assignment->id,
                    'use' => 'reviewing',
                    'status' => 'available',
                    'created_by' => $assignment->user_id,
                ]);
            }
        });

        return redirect()->route('page.assignments.accept')
            ->with('success', "$quantity incomplete assignment(s) marked as complete and added as internal product items.");
    }


    public function review()
    {
        $validated = request()->validate([
            'internal_product_id' => 'required|exists:internal_products,id',
            'use' => 'required|in:approved,rejected',
            'quantity' => 'required|integer|min:1',
        ]);

        $internalProductId = $validated['internal_product_id'];
        $use = $validated['use'];
        $quantity = $validated['quantity'];

        $items = InternalProductItem::where('internal_product_id', $internalProductId)
            ->where('use', 'reviewing')
            ->limit($quantity)
            ->get();

        if ($items->count() < $quantity) {
            return back()->with('error', 'Not enough reviewing items available to fulfill the request.');
        }


        foreach ($items as $item) {
            $item->update([
                'use' => $use
            ]);
        }

        $message = "$quantity internal product item(s) marked as " . ($use === 'approved' ? 'approved' : 'rejected') . ".";
        return redirect()->route('page.assignments.manage')->with('success', $message);
    }



    public function revieweach(Request $request)
    {
        $validated = $request->validate([
            'internal_product_item_id' => 'required|exists:internal_product_items,id',
            'use' => 'required|in:approved,rejected',
        ]);

        $internalProductItemId = $validated['internal_product_item_id'];
        $use = $validated['use'];

        $product = InternalProductItem::findOrFail($internalProductItemId);
        $product->update(['use' => $use]); // ✅ Update the correct column

        $message = "internal product item marked as " . ($use === 'approved' ? 'approved' : 'rejected') . ".";
        return redirect()->route('page.assignments.manage')->with('success', $message);
    }




















    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MaterialAssignment $materialAssignment)
    {
        $validator = Validator::make($request->all(), [
            'material_stock_id' => 'required|exists:material_stocks,id',
            'user_id' => 'required|exists:users,id',
            'assigned_by' => 'required|exists:users,id',
            'status' => 'required|in:incomplete,complete',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $materialAssignment->update($request->only(['material_stock_id', 'user_id', 'assigned_by', 'status', 'notes']));
        return redirect()->route('material_assignments.index')->with('success', 'Assignment updated.');
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $stocks = MaterialStock::all();
        $users = User::all();
        return view('material_assignments.create', compact('stocks', 'users'));
    }


    /**
     * Display the specified resource.
     */
    public function show(MaterialAssignment $materialAssignment)
    {
        return view('material_assignments.show', compact('materialAssignment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MaterialAssignment $materialAssignment)
    {
        $stocks = MaterialStock::all();
        $users = User::all();
        return view('material_assignments.edit', compact('materialAssignment', 'stocks', 'users'));
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MaterialAssignment $materialAssignment)
    {
        $materialAssignment->delete();
        return redirect()->route('material_assignments.index')->with('success', 'Assignment deleted.');
    }
}
