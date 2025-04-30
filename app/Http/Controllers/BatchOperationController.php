<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BatchOperationController extends Controller
{
    /**
     * Show the batch inventory update form.
     */
    public function showBatchInventoryForm()
    {
        $products = Product::with('inventory')->get();
        return view('batch.inventory', compact('products'));
    }

    /**
     * Process batch inventory update.
     */
    public function processBatchInventory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:0',
            'products.*.reorder_level' => 'required|integer|min:0',
            'products.*.location' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();

        try {
            foreach ($request->products as $productData) {
                if (isset($productData['id']) && isset($productData['quantity'])) {
                    $inventory = Inventory::firstOrNew(['product_id' => $productData['id']]);
                    $inventory->quantity = $productData['quantity'];
                    $inventory->reorder_level = $productData['reorder_level'];
                    $inventory->location = $productData['location'] ?? null;
                    $inventory->save();
                }
            }

            DB::commit();
            return redirect()->route('inventory.index')->with('success', 'Batch inventory update completed successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Show the batch price update form.
     */
    public function showBatchPriceForm()
    {
        $products = Product::all();
        return view('batch.prices', compact('products'));
    }

    /**
     * Process batch price update.
     */
    public function processBatchPrice(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.price' => 'required|numeric|min:0',
            'products.*.cost_price' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();

        try {
            foreach ($request->products as $productData) {
                if (isset($productData['id']) && isset($productData['price'])) {
                    $product = Product::find($productData['id']);
                    $product->price = $productData['price'];

                    if (isset($productData['cost_price'])) {
                        $product->cost_price = $productData['cost_price'];
                    }

                    $product->save();
                }
            }

            DB::commit();
            return redirect()->route('products.index')->with('success', 'Batch price update completed successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage())->withInput();
        }
    }
}
