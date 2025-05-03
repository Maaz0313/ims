<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Order::with('user');

        // Text search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_email', 'like', "%{$search}%")
                    ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }

        // Date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Amount range
        if ($request->filled('min_amount')) {
            $query->where('grand_total', '>=', $request->min_amount);
        }
        if ($request->filled('max_amount')) {
            $query->where('grand_total', '<=', $request->max_amount);
        }

        // Sorting
        $sortField = $request->input('sort_by', 'created_at');
        $sortDirection = $request->input('sort_direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        $orders = $query->paginate(15);

        if ($request->ajax()) {
            return view('orders.partials.orders_table', compact('orders'))->render();
        }

        return view('orders.index', compact('orders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = Product::with(['category', 'inventory'])
            ->whereHas('inventory', function ($query) {
                $query->where('quantity', '>', 0);
            })
            ->orderBy('name')
            ->get();

        return view('orders.create', compact('products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'shipping_address' => 'nullable|string',
            'notes' => 'nullable|string',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.unit_price' => 'required|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            // Calculate totals
            $totalAmount = 0;
            foreach ($request->products as $item) {
                $totalAmount += $item['quantity'] * $item['unit_price'];
            }

            // Calculate tax and discount
            $taxRate = $request->tax_rate ?? 0;
            $taxAmount = ($totalAmount * $taxRate) / 100;
            $discountAmount = $request->discount_amount ?? 0;

            // Calculate grand total
            $grandTotal = $totalAmount + $taxAmount - $discountAmount;

            // Generate order number
            $orderNumber = 'ORD-' . date('Ymd') . '-' . Str::padLeft(Order::count() + 1, 4, '0');

            // Create order
            $order = Order::create([
                'order_number' => $orderNumber,
                'user_id' => auth()->id(),
                'customer_name' => $request->customer_name,
                'customer_email' => $request->customer_email,
                'customer_phone' => $request->customer_phone,
                'shipping_address' => $request->shipping_address,
                'total_amount' => $totalAmount,
                'tax_amount' => $taxAmount,
                'discount_amount' => $discountAmount,
                'grand_total' => $grandTotal,
                'status' => 'pending',
                'notes' => $request->notes,
            ]);

            // Create order items and update inventory
            foreach ($request->products as $item) {
                // Check inventory
                $inventory = Inventory::where('product_id', $item['product_id'])->first();

                if (!$inventory || $inventory->quantity < $item['quantity']) {
                    throw new \Exception('Insufficient inventory for one or more products.');
                }

                // Create order item
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['quantity'] * $item['unit_price'],
                ]);

                // Update inventory
                $inventory->update([
                    'quantity' => $inventory->quantity - $item['quantity'],
                ]);
            }

            DB::commit();

            return redirect()->route('orders.show', $order)
                ->with('success', 'Order created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'An error occurred: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        $order->load(['user', 'items.product']);
        return view('orders.show', compact('order'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        // Only allow editing if the order is pending
        if ($order->status !== 'pending') {
            return redirect()->route('orders.show', $order)
                ->with('error', 'Only pending orders can be edited.');
        }

        $products = Product::with(['category', 'inventory'])
            ->orderBy('name')
            ->get();

        $order->load('items.product');

        return view('orders.edit', compact('order', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        // Only allow updating if the order is pending
        if ($order->status !== 'pending') {
            return redirect()->route('orders.show', $order)
                ->with('error', 'Only pending orders can be updated.');
        }

        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'shipping_address' => 'nullable|string',
            'notes' => 'nullable|string',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.unit_price' => 'required|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            // Get current order items to restore inventory
            $currentItems = $order->items;

            // Restore inventory for current items
            foreach ($currentItems as $item) {
                $inventory = Inventory::where('product_id', $item->product_id)->first();

                if ($inventory) {
                    $inventory->update([
                        'quantity' => $inventory->quantity + $item->quantity,
                    ]);
                }
            }

            // Calculate totals for new items
            $totalAmount = 0;
            foreach ($request->products as $item) {
                $totalAmount += $item['quantity'] * $item['unit_price'];
            }

            // Calculate tax and discount
            $taxRate = $request->tax_rate ?? 0;
            $taxAmount = ($totalAmount * $taxRate) / 100;
            $discountAmount = $request->discount_amount ?? 0;

            // Calculate grand total
            $grandTotal = $totalAmount + $taxAmount - $discountAmount;

            // Update order
            $order->update([
                'customer_name' => $request->customer_name,
                'customer_email' => $request->customer_email,
                'customer_phone' => $request->customer_phone,
                'shipping_address' => $request->shipping_address,
                'total_amount' => $totalAmount,
                'tax_amount' => $taxAmount,
                'discount_amount' => $discountAmount,
                'grand_total' => $grandTotal,
                'notes' => $request->notes,
            ]);

            // Delete existing items
            $order->items()->delete();

            // Create new order items and update inventory
            foreach ($request->products as $item) {
                // Check inventory
                $inventory = Inventory::where('product_id', $item['product_id'])->first();

                if (!$inventory || $inventory->quantity < $item['quantity']) {
                    throw new \Exception('Insufficient inventory for one or more products.');
                }

                // Create order item
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['quantity'] * $item['unit_price'],
                ]);

                // Update inventory
                $inventory->update([
                    'quantity' => $inventory->quantity - $item['quantity'],
                ]);
            }

            DB::commit();

            return redirect()->route('orders.show', $order)
                ->with('success', 'Order updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'An error occurred: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        // Only allow deleting if the order is pending
        if ($order->status !== 'pending') {
            return redirect()->route('orders.index')
                ->with('error', 'Only pending orders can be deleted.');
        }

        DB::beginTransaction();

        try {
            // Restore inventory for all items
            foreach ($order->items as $item) {
                $inventory = Inventory::where('product_id', $item->product_id)->first();

                if ($inventory) {
                    $inventory->update([
                        'quantity' => $inventory->quantity + $item->quantity,
                    ]);
                }
            }

            // Delete order items
            $order->items()->delete();

            // Delete order
            $order->delete();

            DB::commit();

            return redirect()->route('orders.index')
                ->with('success', 'Order deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Process the order.
     */
    public function processOrder(Order $order)
    {
        // Only allow processing if the order is pending
        if ($order->status !== 'pending') {
            return redirect()->route('orders.show', $order)
                ->with('error', 'Only pending orders can be processed.');
        }

        $order->update([
            'status' => 'processing',
        ]);

        return redirect()->route('orders.show', $order)
            ->with('success', 'Order marked as processing.');
    }

    /**
     * Complete the order.
     */
    public function completeOrder(Order $order)
    {
        // Only allow completing if the order is processing
        if ($order->status !== 'processing') {
            return redirect()->route('orders.show', $order)
                ->with('error', 'Only processing orders can be completed.');
        }

        $order->update([
            'status' => 'completed',
        ]);

        return redirect()->route('orders.show', $order)
            ->with('success', 'Order marked as completed.');
    }

    /**
     * Cancel the order.
     */
    public function cancelOrder(Order $order)
    {
        // Only allow cancelling if the order is pending or processing
        if (!in_array($order->status, ['pending', 'processing'])) {
            return redirect()->route('orders.show', $order)
                ->with('error', 'Only pending or processing orders can be cancelled.');
        }

        DB::beginTransaction();

        try {
            // Restore inventory for all items
            foreach ($order->items as $item) {
                $inventory = Inventory::where('product_id', $item->product_id)->first();

                if ($inventory) {
                    $inventory->update([
                        'quantity' => $inventory->quantity + $item->quantity,
                    ]);
                }
            }

            // Update order status
            $order->update([
                'status' => 'cancelled',
            ]);

            DB::commit();

            return redirect()->route('orders.show', $order)
                ->with('success', 'Order cancelled successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
}
