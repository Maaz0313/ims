<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Inventory;
use App\Models\PurchaseOrder;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProductsExport;
use App\Exports\SuppliersExport;
use App\Exports\InventoryExport;
use App\Exports\PurchaseOrdersExport;
use App\Exports\OrdersExport;
use App\Exports\CategoriesExport;

class ReportsController extends Controller
{
    /**
     * Display the reports dashboard.
     */
    public function index()
    {
        // Get summary data for dashboard
        $totalProducts = Product::count();
        $totalCategories = Category::count();
        $totalSuppliers = Supplier::count();
        
        // Inventory value
        $inventoryValue = Inventory::join('products', 'inventory.product_id', '=', 'products.id')
            ->select(DB::raw('SUM(inventory.quantity * products.price) as total_value'))
            ->value('total_value');
            
        // Low stock products
        $lowStockThreshold = 10; // This could be a setting
        $lowStockProducts = Product::with(['category', 'inventory'])
            ->whereHas('inventory', function($query) use ($lowStockThreshold) {
                $query->where('quantity', '<', $lowStockThreshold);
            })
            ->take(5)
            ->get();
            
        // Recent orders
        $recentOrders = Order::with('user')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
            
        // Recent purchase orders
        $recentPurchaseOrders = PurchaseOrder::with(['supplier', 'user'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
            
        // Sales data for current month
        $currentMonthStart = Carbon::now()->startOfMonth();
        $currentMonthEnd = Carbon::now()->endOfMonth();
        
        $currentMonthSales = Order::where('status', 'completed')
            ->whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])
            ->sum('grand_total');
            
        // Sales data for previous month
        $previousMonthStart = Carbon::now()->subMonth()->startOfMonth();
        $previousMonthEnd = Carbon::now()->subMonth()->endOfMonth();
        
        $previousMonthSales = Order::where('status', 'completed')
            ->whereBetween('created_at', [$previousMonthStart, $previousMonthEnd])
            ->sum('grand_total');
            
        // Calculate percentage change
        $salesPercentageChange = 0;
        if ($previousMonthSales > 0) {
            $salesPercentageChange = (($currentMonthSales - $previousMonthSales) / $previousMonthSales) * 100;
        }
        
        return view('reports.index', compact(
            'totalProducts', 
            'totalCategories', 
            'totalSuppliers', 
            'inventoryValue', 
            'lowStockProducts', 
            'recentOrders', 
            'recentPurchaseOrders',
            'currentMonthSales',
            'previousMonthSales',
            'salesPercentageChange'
        ));
    }
    
    /**
     * Display inventory report.
     */
    public function inventory(Request $request)
    {
        $query = Product::with(['category', 'supplier', 'inventory']);
        
        // Apply filters
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }
        
        if ($request->filled('supplier')) {
            $query->where('supplier_id', $request->supplier);
        }
        
        if ($request->filled('stock_status')) {
            if ($request->stock_status === 'in_stock') {
                $query->whereHas('inventory', function($q) {
                    $q->where('quantity', '>', 0);
                });
            } elseif ($request->stock_status === 'out_of_stock') {
                $query->whereHas('inventory', function($q) {
                    $q->where('quantity', '=', 0);
                });
            } elseif ($request->stock_status === 'low_stock') {
                $query->whereHas('inventory', function($q) {
                    $q->where('quantity', '>', 0)
                      ->where('quantity', '<', 10); // This could be a setting
                });
            }
        }
        
        // Get products with inventory data
        $products = $query->orderBy('name')->get();
        
        // Calculate total inventory value
        $totalValue = 0;
        foreach ($products as $product) {
            $totalValue += $product->inventory->quantity * $product->price;
        }
        
        // Get categories and suppliers for filters
        $categories = Category::orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();
        
        return view('reports.inventory', compact('products', 'totalValue', 'categories', 'suppliers'));
    }
    
    /**
     * Display sales report.
     */
    public function sales(Request $request)
    {
        // Default to current month if no dates provided
        $startDate = $request->filled('start_date') 
            ? Carbon::parse($request->start_date) 
            : Carbon::now()->startOfMonth();
            
        $endDate = $request->filled('end_date') 
            ? Carbon::parse($request->end_date)->endOfDay() 
            : Carbon::now()->endOfDay();
            
        $query = Order::with(['user', 'items.product'])
            ->whereBetween('created_at', [$startDate, $endDate]);
            
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Get orders
        $orders = $query->orderBy('created_at', 'desc')->get();
        
        // Calculate totals
        $totalSales = $orders->where('status', 'completed')->sum('grand_total');
        $totalOrders = $orders->where('status', 'completed')->count();
        $averageOrderValue = $totalOrders > 0 ? $totalSales / $totalOrders : 0;
        
        // Get sales by product
        $salesByProduct = [];
        foreach ($orders->where('status', 'completed') as $order) {
            foreach ($order->items as $item) {
                $productId = $item->product_id;
                $productName = $item->product->name;
                
                if (!isset($salesByProduct[$productId])) {
                    $salesByProduct[$productId] = [
                        'name' => $productName,
                        'quantity' => 0,
                        'revenue' => 0
                    ];
                }
                
                $salesByProduct[$productId]['quantity'] += $item->quantity;
                $salesByProduct[$productId]['revenue'] += $item->total_price;
            }
        }
        
        // Sort by revenue (highest first)
        usort($salesByProduct, function($a, $b) {
            return $b['revenue'] <=> $a['revenue'];
        });
        
        // Get sales by day for chart
        $salesByDay = [];
        $currentDate = clone $startDate;
        
        while ($currentDate <= $endDate) {
            $dayKey = $currentDate->format('Y-m-d');
            $salesByDay[$dayKey] = 0;
            $currentDate->addDay();
        }
        
        foreach ($orders->where('status', 'completed') as $order) {
            $dayKey = $order->created_at->format('Y-m-d');
            if (isset($salesByDay[$dayKey])) {
                $salesByDay[$dayKey] += $order->grand_total;
            }
        }
        
        return view('reports.sales', compact(
            'orders', 
            'totalSales', 
            'totalOrders', 
            'averageOrderValue', 
            'salesByProduct',
            'salesByDay',
            'startDate',
            'endDate'
        ));
    }
    
    /**
     * Display purchase report.
     */
    public function purchases(Request $request)
    {
        // Default to current month if no dates provided
        $startDate = $request->filled('start_date') 
            ? Carbon::parse($request->start_date) 
            : Carbon::now()->startOfMonth();
            
        $endDate = $request->filled('end_date') 
            ? Carbon::parse($request->end_date)->endOfDay() 
            : Carbon::now()->endOfDay();
            
        $query = PurchaseOrder::with(['supplier', 'user', 'items.product'])
            ->whereBetween('created_at', [$startDate, $endDate]);
            
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter by supplier
        if ($request->filled('supplier')) {
            $query->where('supplier_id', $request->supplier);
        }
        
        // Get purchase orders
        $purchaseOrders = $query->orderBy('created_at', 'desc')->get();
        
        // Calculate totals
        $totalPurchases = $purchaseOrders->where('status', 'received')->sum('total_amount');
        $totalOrders = $purchaseOrders->where('status', 'received')->count();
        
        // Get purchases by supplier
        $purchasesBySupplier = [];
        foreach ($purchaseOrders->where('status', 'received') as $po) {
            $supplierId = $po->supplier_id;
            $supplierName = $po->supplier->name;
            
            if (!isset($purchasesBySupplier[$supplierId])) {
                $purchasesBySupplier[$supplierId] = [
                    'name' => $supplierName,
                    'orders' => 0,
                    'amount' => 0
                ];
            }
            
            $purchasesBySupplier[$supplierId]['orders']++;
            $purchasesBySupplier[$supplierId]['amount'] += $po->total_amount;
        }
        
        // Sort by amount (highest first)
        usort($purchasesBySupplier, function($a, $b) {
            return $b['amount'] <=> $a['amount'];
        });
        
        // Get purchases by product
        $purchasesByProduct = [];
        foreach ($purchaseOrders->where('status', 'received') as $po) {
            foreach ($po->items as $item) {
                $productId = $item->product_id;
                $productName = $item->product->name;
                
                if (!isset($purchasesByProduct[$productId])) {
                    $purchasesByProduct[$productId] = [
                        'name' => $productName,
                        'quantity' => 0,
                        'amount' => 0
                    ];
                }
                
                $purchasesByProduct[$productId]['quantity'] += $item->received_quantity;
                $purchasesByProduct[$productId]['amount'] += $item->unit_price * $item->received_quantity;
            }
        }
        
        // Sort by amount (highest first)
        usort($purchasesByProduct, function($a, $b) {
            return $b['amount'] <=> $a['amount'];
        });
        
        // Get suppliers for filter
        $suppliers = Supplier::orderBy('name')->get();
        
        return view('reports.purchases', compact(
            'purchaseOrders', 
            'totalPurchases', 
            'totalOrders', 
            'purchasesBySupplier',
            'purchasesByProduct',
            'suppliers',
            'startDate',
            'endDate'
        ));
    }
    
    /**
     * Display profit and loss report.
     */
    public function profitLoss(Request $request)
    {
        // Default to current month if no dates provided
        $startDate = $request->filled('start_date') 
            ? Carbon::parse($request->start_date) 
            : Carbon::now()->startOfMonth();
            
        $endDate = $request->filled('end_date') 
            ? Carbon::parse($request->end_date)->endOfDay() 
            : Carbon::now()->endOfDay();
            
        // Get completed orders in date range
        $orders = Order::with('items')
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();
            
        // Get received purchase orders in date range
        $purchaseOrders = PurchaseOrder::with('items')
            ->where('status', 'received')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();
            
        // Calculate revenue
        $totalRevenue = $orders->sum('grand_total');
        $totalTax = $orders->sum('tax_amount');
        $totalDiscounts = $orders->sum('discount_amount');
        $netRevenue = $totalRevenue - $totalTax;
        
        // Calculate cost of goods sold
        $costOfGoodsSold = 0;
        foreach ($orders as $order) {
            foreach ($order->items as $item) {
                // Use cost price if available, otherwise estimate
                $costPrice = $item->product->cost_price ?? ($item->unit_price * 0.6); // 60% of selling price as fallback
                $costOfGoodsSold += $costPrice * $item->quantity;
            }
        }
        
        // Calculate gross profit
        $grossProfit = $netRevenue - $costOfGoodsSold;
        $grossProfitMargin = $netRevenue > 0 ? ($grossProfit / $netRevenue) * 100 : 0;
        
        // Calculate purchases
        $totalPurchases = $purchaseOrders->sum('total_amount');
        
        // Calculate inventory change (simplified)
        $inventoryChange = $totalPurchases - $costOfGoodsSold;
        
        // Prepare monthly data for chart
        $monthlyData = [];
        $currentDate = clone $startDate;
        $currentDate->startOfMonth();
        $endMonth = clone $endDate;
        $endMonth->endOfMonth();
        
        while ($currentDate <= $endMonth) {
            $monthKey = $currentDate->format('Y-m');
            $monthLabel = $currentDate->format('M Y');
            
            $monthlyData[$monthKey] = [
                'label' => $monthLabel,
                'revenue' => 0,
                'cost' => 0,
                'profit' => 0
            ];
            
            $currentDate->addMonth();
        }
        
        // Fill in monthly revenue
        foreach ($orders as $order) {
            $monthKey = $order->created_at->format('Y-m');
            if (isset($monthlyData[$monthKey])) {
                $monthlyData[$monthKey]['revenue'] += $order->grand_total - $order->tax_amount;
            }
        }
        
        // Fill in monthly cost and calculate profit
        foreach ($orders as $order) {
            $monthKey = $order->created_at->format('Y-m');
            if (isset($monthlyData[$monthKey])) {
                $orderCost = 0;
                foreach ($order->items as $item) {
                    $costPrice = $item->product->cost_price ?? ($item->unit_price * 0.6);
                    $orderCost += $costPrice * $item->quantity;
                }
                $monthlyData[$monthKey]['cost'] += $orderCost;
                $monthlyData[$monthKey]['profit'] = $monthlyData[$monthKey]['revenue'] - $monthlyData[$monthKey]['cost'];
            }
        }
        
        return view('reports.profit-loss', compact(
            'totalRevenue',
            'totalTax',
            'totalDiscounts',
            'netRevenue',
            'costOfGoodsSold',
            'grossProfit',
            'grossProfitMargin',
            'totalPurchases',
            'inventoryChange',
            'monthlyData',
            'startDate',
            'endDate'
        ));
    }
    
    public function exportProducts()
    {
        return Excel::download(new ProductsExport, 'products.xlsx');
    }

    public function exportSuppliers()
    {
        return Excel::download(new SuppliersExport, 'suppliers.xlsx');
    }

    public function exportInventory()
    {
        return Excel::download(new InventoryExport, 'inventory.xlsx');
    }

    public function exportPurchaseOrders()
    {
        return Excel::download(new PurchaseOrdersExport, 'purchase_orders.xlsx');
    }

    public function exportOrders()
    {
        return Excel::download(new OrdersExport, 'orders.xlsx');
    }

    public function exportCategories()
    {
        return Excel::download(new CategoriesExport, 'categories.xlsx');
    }
}
