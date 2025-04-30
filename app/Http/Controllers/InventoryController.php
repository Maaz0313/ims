<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Inventory::with('product');

        // Apply search
        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('product', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        // Apply filters
        if ($request->has('filter')) {
            switch ($request->filter) {
                case 'low_stock':
                    $query->whereRaw('quantity <= reorder_level AND quantity > 0');
                    break;
                case 'out_of_stock':
                    $query->where('quantity', '<=', 0);
                    break;
                case 'in_stock':
                    $query->whereRaw('quantity > reorder_level');
                    break;
            }
        }

        $inventory = $query->paginate(15);

        return view('inventory.index', compact('inventory'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = Product::whereDoesntHave('inventory')->get();
        return view('inventory.create', compact('products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id|unique:inventory,product_id',
            'quantity' => 'required|integer|min:0',
            'reorder_level' => 'required|integer|min:0',
            'location' => 'nullable|string|max:255',
        ]);

        $inventory = new Inventory();
        $inventory->product_id = $request->product_id;
        $inventory->quantity = $request->quantity;
        $inventory->reorder_level = $request->reorder_level;
        $inventory->location = $request->location;
        $inventory->save();

        return redirect()->route('inventory.index')
            ->with('success', 'Inventory record created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Inventory $inventory)
    {
        $inventory->load('product');
        return view('inventory.show', compact('inventory'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Inventory $inventory)
    {
        $inventory->load('product');
        return view('inventory.edit', compact('inventory'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Inventory $inventory)
    {
        $request->validate([
            'quantity' => 'sometimes|required|integer|min:0',
            'reorder_level' => 'required|integer|min:0',
            'location' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            // Handle different adjustment types
            if ($request->has('adjustment_type')) {
                switch ($request->adjustment_type) {
                    case 'add':
                        $inventory->quantity += $request->quantity;
                        break;
                    case 'subtract':
                        $inventory->quantity = max(0, $inventory->quantity - $request->quantity);
                        break;
                    case 'set':
                        $inventory->quantity = $request->quantity;
                        break;
                }
            } else {
                $inventory->quantity = $request->quantity;
            }

            $inventory->reorder_level = $request->reorder_level;
            $inventory->location = $request->location;
            $inventory->save();

            // Create inventory history record if notes are provided
            if ($request->has('notes') && !empty($request->notes)) {
                // You can implement inventory history here if needed
            }

            DB::commit();
            return redirect()->route('inventory.index')
                ->with('success', 'Inventory updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'An error occurred: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Inventory $inventory)
    {
        $inventory->delete();

        return redirect()->route('inventory.index')
            ->with('success', 'Inventory record deleted successfully.');
    }
}
