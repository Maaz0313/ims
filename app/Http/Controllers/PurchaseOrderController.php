<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Supplier;
use App\Models\Inventory;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PurchaseOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $purchaseOrders = PurchaseOrder::with(['supplier', 'user'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('purchase-orders.index', compact('purchaseOrders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $suppliers = Supplier::orderBy('name')->get();
        $products = Product::with(['supplier', 'category'])
            ->orderBy('name')
            ->get();

        return view('purchase-orders.create', compact('suppliers', 'products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'order_date' => 'required|date',
            'expected_delivery_date' => 'nullable|date|after_or_equal:order_date',
            'notes' => 'nullable|string',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            // Calculate total amount
            $totalAmount = 0;
            foreach ($request->products as $item) {
                $totalAmount += $item['quantity'] * $item['unit_price'];
            }

            // Generate PO number
            $poNumber = 'PO-' . date('Ymd') . '-' . Str::padLeft(PurchaseOrder::count() + 1, 4, '0');

            // Create purchase order
            $purchaseOrder = PurchaseOrder::create([
                'po_number' => $poNumber,
                'supplier_id' => $request->supplier_id,
                'user_id' => auth()->id(),
                'order_date' => $request->order_date,
                'expected_delivery_date' => $request->expected_delivery_date,
                'total_amount' => $totalAmount,
                'status' => 'pending',
                'notes' => $request->notes,
            ]);

            // Create purchase order items
            foreach ($request->products as $item) {
                PurchaseOrderItem::create([
                    'purchase_order_id' => $purchaseOrder->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['quantity'] * $item['unit_price'],
                    'received_quantity' => 0,
                ]);
            }

            DB::commit();

            return redirect()->route('purchase-orders.show', $purchaseOrder)
                ->with('success', 'Purchase order created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'An error occurred: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['supplier', 'user', 'items.product']);
        return view('purchase-orders.show', compact('purchaseOrder'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PurchaseOrder $purchaseOrder)
    {
        // Only allow editing if the purchase order is pending
        if ($purchaseOrder->status !== 'pending') {
            return redirect()->route('purchase-orders.show', $purchaseOrder)
                ->with('error', 'Only pending purchase orders can be edited.');
        }

        $suppliers = Supplier::orderBy('name')->get();
        $products = Product::with(['supplier', 'category'])
            ->orderBy('name')
            ->get();
        $purchaseOrder->load('items.product');

        return view('purchase-orders.edit', compact('purchaseOrder', 'suppliers', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        // Only allow updating if the purchase order is pending
        if ($purchaseOrder->status !== 'pending') {
            return redirect()->route('purchase-orders.show', $purchaseOrder)
                ->with('error', 'Only pending purchase orders can be updated.');
        }

        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'order_date' => 'required|date',
            'expected_delivery_date' => 'nullable|date|after_or_equal:order_date',
            'notes' => 'nullable|string',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            // Calculate total amount
            $totalAmount = 0;
            foreach ($request->products as $item) {
                $totalAmount += $item['quantity'] * $item['unit_price'];
            }

            // Update purchase order
            $purchaseOrder->update([
                'supplier_id' => $request->supplier_id,
                'order_date' => $request->order_date,
                'expected_delivery_date' => $request->expected_delivery_date,
                'total_amount' => $totalAmount,
                'notes' => $request->notes,
            ]);

            // Delete existing items
            $purchaseOrder->items()->delete();

            // Create new purchase order items
            foreach ($request->products as $item) {
                PurchaseOrderItem::create([
                    'purchase_order_id' => $purchaseOrder->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['quantity'] * $item['unit_price'],
                    'received_quantity' => 0,
                ]);
            }

            DB::commit();

            return redirect()->route('purchase-orders.show', $purchaseOrder)
                ->with('success', 'Purchase order updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'An error occurred: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PurchaseOrder $purchaseOrder)
    {
        // Only allow deleting if the purchase order is pending
        if ($purchaseOrder->status !== 'pending') {
            return redirect()->route('purchase-orders.index')
                ->with('error', 'Only pending purchase orders can be deleted.');
        }

        DB::beginTransaction();

        try {
            // Delete purchase order items
            $purchaseOrder->items()->delete();

            // Delete purchase order
            $purchaseOrder->delete();

            DB::commit();

            return redirect()->route('purchase-orders.index')
                ->with('success', 'Purchase order deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Change the status of the purchase order to ordered.
     */
    public function markAsOrdered(PurchaseOrder $purchaseOrder)
    {
        // Only allow changing status if the purchase order is pending
        if ($purchaseOrder->status !== 'pending') {
            return redirect()->route('purchase-orders.show', $purchaseOrder)
                ->with('error', 'Only pending purchase orders can be marked as ordered.');
        }

        $purchaseOrder->update([
            'status' => 'ordered',
        ]);

        return redirect()->route('purchase-orders.show', $purchaseOrder)
            ->with('success', 'Purchase order marked as ordered.');
    }

    /**
     * Show the form for receiving a purchase order.
     */
    public function showReceiveForm(PurchaseOrder $purchaseOrder)
    {
        // Only allow receiving if the purchase order is ordered
        if ($purchaseOrder->status !== 'ordered') {
            return redirect()->route('purchase-orders.show', $purchaseOrder)
                ->with('error', 'Only ordered purchase orders can be received.');
        }

        $purchaseOrder->load('items.product');

        return view('purchase-orders.receive', compact('purchaseOrder'));
    }

    /**
     * Process receiving a purchase order.
     */
    public function receiveOrder(Request $request, PurchaseOrder $purchaseOrder)
    {
        // Only allow receiving if the purchase order is ordered
        if ($purchaseOrder->status !== 'ordered') {
            return redirect()->route('purchase-orders.show', $purchaseOrder)
                ->with('error', 'Only ordered purchase orders can be received.');
        }

        $request->validate([
            'delivery_date' => 'required|date',
            'items' => 'required|array',
            'items.*.id' => 'required|exists:purchase_order_items,id',
            'items.*.received_quantity' => 'required|integer|min:0',
        ]);

        DB::beginTransaction();

        try {
            // Update purchase order
            $purchaseOrder->update([
                'delivery_date' => $request->delivery_date,
                'status' => 'received',
            ]);

            // Update inventory for each item
            foreach ($request->items as $item) {
                $poItem = PurchaseOrderItem::findOrFail($item['id']);

                // Update received quantity
                $poItem->update([
                    'received_quantity' => $item['received_quantity'],
                ]);

                // Update inventory
                if ($item['received_quantity'] > 0) {
                    $inventory = Inventory::where('product_id', $poItem->product_id)->first();

                    if ($inventory) {
                        $inventory->update([
                            'quantity' => $inventory->quantity + $item['received_quantity'],
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()->route('purchase-orders.show', $purchaseOrder)
                ->with('success', 'Purchase order received successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'An error occurred: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Cancel a purchase order.
     */
    public function cancelOrder(PurchaseOrder $purchaseOrder)
    {
        // Only allow cancelling if the purchase order is pending or ordered
        if (!in_array($purchaseOrder->status, ['pending', 'ordered'])) {
            return redirect()->route('purchase-orders.show', $purchaseOrder)
                ->with('error', 'Only pending or ordered purchase orders can be cancelled.');
        }

        $purchaseOrder->update([
            'status' => 'cancelled',
        ]);

        return redirect()->route('purchase-orders.show', $purchaseOrder)
            ->with('success', 'Purchase order cancelled successfully.');
    }
}
