<?php

namespace App\Http\Controllers;

use App\Models\InternalProduct;
use App\Models\InternalProductItem;
use App\Models\Material;
use App\Models\MaterialAssignment;
use App\Models\MaterialAssignmentItems;
use App\Models\MaterialStock;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class MaterialAssignmentController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $assignments = MaterialAssignment::with(['items.material'])
            ->where('status', 'incomplete')
            ->get()
            ->groupBy('user_id') // group by user
            ->map(function ($userAssignments) {
                $user = $userAssignments->first()->user;

                $mergedAssignments = [];

                foreach ($userAssignments as $assignment) {
                    // Normalize materials for comparison: material_id => quantity
                    $materials = $assignment->items->map(function ($item) {
                        return [
                            'material_id' => $item->material->id,
                            'material_name' => $item->material->name,
                            'quantity' => $item->quantity,
                        ];
                    })->sortBy('material_id')->values()->all();

                    // Create a string key to detect duplicates
                    $key = collect($materials)->map(fn($m) => "{$m['material_id']}:{$m['quantity']}")->implode(',');

                    if (isset($mergedAssignments[$key])) {
                        $mergedAssignments[$key]['assignment_count']++;
                        $mergedAssignments[$key]['assignment_ids'][] = $assignment->id; // add the assignment id
                    } else {
                        $mergedAssignments[$key] = [
                            'materials' => $materials,
                            'assignment_count' => 1,
                            'assignment_ids' => [$assignment->id], // initialize with current id
                        ];
                    }
                }

                return [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'assignments' => array_values($mergedAssignments),
                ];
            })
            ->values();

        $fullAssignments = MaterialAssignment::where('material_assignments.status', 'incomplete')
            ->count();

        $products = InternalProduct::all();

        $employees = User::where('role', 'employee')->get();

        $availableProducts = InternalProduct::withCount(['items as available_count' => function ($query) {
            $query->where('status', 'available');
        }])->get();

        return view('pages.accept-assignment', compact(['employees', 'assignments', 'fullAssignments', 'products', 'availableProducts']));
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

        $assignments = MaterialAssignment::with(['items.material'])
            ->where('user_id', $request->user_id)
            ->where('status', 'incomplete')
            ->get()
            ->groupBy('user_id') // group by user
            ->map(function ($userAssignments) {
                $user = $userAssignments->first()->user;

                $mergedAssignments = [];

                foreach ($userAssignments as $assignment) {
                    // Normalize materials for comparison: material_id => quantity
                    $materials = $assignment->items->map(function ($item) {
                        return [
                            'material_id' => $item->material->id,
                            'material_name' => $item->material->name,
                            'quantity' => $item->quantity,
                        ];
                    })->sortBy('material_id')->values()->all();

                    // Create a string key to detect duplicates
                    $key = collect($materials)->map(fn($m) => "{$m['material_id']}:{$m['quantity']}")->implode(',');

                    if (isset($mergedAssignments[$key])) {
                        $mergedAssignments[$key]['assignment_count']++;
                        $mergedAssignments[$key]['assignment_ids'][] = $assignment->id; // add the assignment id
                    } else {
                        $mergedAssignments[$key] = [
                            'materials' => $materials,
                            'assignment_count' => 1,
                            'assignment_ids' => [$assignment->id], // initialize with current id
                        ];
                    }
                }

                return [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'assignments' => array_values($mergedAssignments),
                ];
            })
            ->values();

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
            'user_id' => 'required|exists:users,id',
            'notes' => 'nullable|string',
            'materials' => 'required|array|min:1',
            'materials.*.material_stock_id' => 'required|exists:material_stocks,id',
            'materials.*.quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();

        try {
            // Create one assignment for the employee
            $assignment = MaterialAssignment::create([
                'user_id' => $request->user_id,
                'assigned_by' => auth()->id(),
                'status' => 'incomplete',
                'notes' => $request->notes,
            ]);

            // Process each material request
            foreach ($request->materials as $material) {

                $availableStocks = MaterialStock::where('material_id', $material['material_stock_id'])
                    ->where('status', 'available')
                    ->take($request->quantity)
                    ->lockForUpdate()
                    ->get();


                if ($availableStocks->count() < $material['quantity']) {
                    DB::rollBack();
                    return redirect()->back()->with(
                        'error',
                        'Not enough available stock for material ID ' . $material['material_stock_id']
                    );
                }

                // Mark as unavailable
                $updatedCount = MaterialStock::where('material_id', $material['material_stock_id'])
                    ->where('status', 'available')
                    ->limit($material['quantity'])
                    ->update(['status' => 'unavailable']);

                if ($updatedCount < $material['quantity']) {
                    DB::rollBack();
                    return redirect()->back()->with(
                        'error',
                        'Stock update failed for material ID ' . $material['material_stock_id']
                    );
                }

                // Save assignment item
                $assignment->items()->create([
                    'material_stock_id' => $material['material_stock_id'],
                    'quantity' => $material['quantity'],
                ]);
            }

            DB::commit();

            return redirect()->route('page.assignments.create')->with('success', 'Assignment created successfully.');
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
            'assignment_ids'      => 'required|array',
            'assignment_ids.*'    => 'exists:material_assignments,id',
            'user_id'             => 'required|exists:users,id',
            'quantity'            => 'required|integer|min:1',
            'action'              => 'required|in:delete,restore',
        ]);

        $assignment_ids = $validated['assignment_ids'];
        $userId = $validated['user_id'];
        $quantity = $validated['quantity'];
        $action = $validated['action'];

        $targetStatus = $action === 'delete' ? 'incomplete' : 'deleted';
        $newStatus = $action === 'delete' ? 'deleted' : 'incomplete';

        // Initialize assignments array
        $assignments = collect();

        if ($action === 'delete') {
            // Fetch assignments for deletion
            $assignments = MaterialAssignment::whereIn('id', $assignment_ids)
                ->where('user_id', $userId)
                ->where('status', $targetStatus)
                ->limit($quantity)
                ->get();

            $count = $assignments->count();

            if ($count < $quantity) {
                return redirect()->route('page.assignments.accept')->withErrors([
                    'quantity' => "Only $count $targetStatus assignment(s) can be deleted."
                ])->withInput();
            }
        } else { // restore
            // Fetch original selected assignments
            $originalAssignments = MaterialAssignment::with('items')
                ->whereIn('id', $assignment_ids)
                ->get();

            // Fetch candidate assignments for restore
            $candidateAssignments = MaterialAssignment::with('items')
                ->where('user_id', $userId)
                ->where('status', $targetStatus)
                ->get();

            // Normalize helper: convert items to material_id => quantity array
            $normalize = function ($assignment) {
                return $assignment->items
                    ->mapWithKeys(fn($item) => [$item->material_id => $item->quantity])
                    ->sortKeys()
                    ->toArray();
            };

            // Normalized sets
            $originalSets = $originalAssignments->map($normalize)->toArray();

            // Filter candidate assignments: match original sets
            $assignments = $candidateAssignments->filter(function ($assignment) use ($originalSets, $normalize) {
                $current = $normalize($assignment);
                foreach ($originalSets as $orig) {
                    if ($current === $orig) {
                        return true;
                    }
                }
                return false;
            })->take($quantity);

            if ($assignments->count() < $quantity) {
                return redirect()->route('page.assignments.accept')->withErrors([
                    'quantity' => "The amount of deleted assignments are $assignments->count() and do not match the restore quantity of $quantity."
                ])->withInput();
            }
        }

        DB::beginTransaction();
        try {
            foreach ($assignments as $assignment) {
                if ($action === 'delete') {
                    // Mark assignment as deleted
                    $assignment->update(['status' => $newStatus]);

                    // Restore stock
                    MaterialAssignmentItems::where('material_assignment_id', $assignment->id)
                        ->get()
                        ->each(function ($item) {
                            MaterialStock::where('id', $item->material_stock_id)->update(['status' => 'available']);
                        });

                } else { // restore
                    MaterialAssignmentItems::where('material_assignment_id', $assignment->id)
                        ->get()
                        ->each(function ($item) use ($assignment) {
                            $stock = MaterialStock::where('id', $item->material_stock_id)
                                ->where('status', 'available')
                                ->first();

                            if (!$stock) {
                                throw new \Exception("Stock ID {$item->material_stock_id} for Assignment #{$assignment->id} is not available.");
                            }

                            $stock->update(['status' => 'unavailable']);
                        });

                    $assignment->update(['status' => $newStatus]);

                }
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('page.assignments.accept')->withErrors([
                'error' => 'Update failed: ' . $e->getMessage()
            ]);
        }

        return redirect()->route('page.assignments.accept')->with('success', "$quantity $targetStatus assignment(s) " . ($action === 'delete' ? 'deleted' : 'restored') . ".");
    }



    public function complete(Request $request)
    {
        $validated = $request->validate([
            'assignment_ids'      => 'required|array',
            'assignment_ids.*'    => 'exists:material_assignments,id',
            'user_id'             => 'required|exists:users,id',
            'internal_product_id' => 'required|exists:internal_products,id',
            'assignment_quantity' => 'required|integer|min:1',
            'product_quantity'    => 'required|integer|min:1',
        ]);

        $assignmentIds     = $validated['assignment_ids'];
        $userId            = $validated['user_id'];
        $internalProductId = $validated['internal_product_id'];
        $assignmentQuantity= $validated['assignment_quantity'];
        $productQuantity   = $validated['product_quantity'];

        // fetch only required number of assignments
        $assignments = MaterialAssignment::whereIn('id', $assignmentIds)
            ->where('status', 'incomplete')
            ->where('user_id', $userId)
            ->limit($assignmentQuantity)
            ->get();

        $count = $assignments->count();

        if ($count < $assignmentQuantity) {
            return redirect()->route('page.assignments.accept')->withErrors([
                'quantity' => "Only $count incomplete assignment(s) can be marked as complete.",
            ])->withInput();
        }

        DB::transaction(function () use ($assignments, $productQuantity, $internalProductId) {

            foreach ($assignments as $assignment) {
                $assignment->update([
                    'status' => 'complete',
                ]);
            }

            // pick any one assignment (since all belong to same user & batch)
            $baseAssignment = $assignments->first();

            for ($i = 0; $i < $productQuantity; $i++) {
                InternalProductItem::create([
                    'internal_product_id' => $internalProductId,
                    'assignment_id'       => $baseAssignment->id,
                    'use'                 => 'reviewing',
                    'status'              => 'available',
                    'created_by'          => $baseAssignment->user_id,
                ]);
            }
        });

        return redirect()->route('page.assignments.accept')
            ->with('success', "$assignmentQuantity incomplete assignment(s) marked as complete and added $productQuantity number of product(s) as internal product items.");
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
