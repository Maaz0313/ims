<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     */
    public function index()
    {
        // Get counts for dashboard
        $totalProducts = Product::count();
        $lowStockProducts = Inventory::whereRaw('quantity <= reorder_level')->count();
        $totalOrders = Order::count();
        $pendingOrders = Order::where('status', 'pending')->count();
        $totalPurchaseOrders = PurchaseOrder::count();
        $pendingPurchaseOrders = PurchaseOrder::where('status', 'pending')->count();

        // Get recent orders
        $recentOrders = Order::with('user')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Get recent purchase orders
        $recentPurchaseOrders = PurchaseOrder::with('supplier')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Get low stock products
        $lowStockProductsList = Inventory::with('product')
            ->whereRaw('quantity <= reorder_level')
            ->take(5)
            ->get();

        // Get data for inventory status chart
        $inventoryStatusData = [
            'labels' => [],
            'data' => [],
            'backgroundColors' => []
        ];

        $topProducts = Inventory::with('product')
            ->orderBy('quantity', 'desc')
            ->take(10)
            ->get();

        foreach ($topProducts as $inventory) {
            $inventoryStatusData['labels'][] = $inventory->product->name;
            $inventoryStatusData['data'][] = $inventory->quantity;

            // Set color based on stock level
            if ($inventory->quantity <= $inventory->reorder_level) {
                $inventoryStatusData['backgroundColors'][] = 'rgba(255, 99, 132, 0.6)'; // Red for low stock
            } elseif ($inventory->quantity <= ($inventory->reorder_level * 2)) {
                $inventoryStatusData['backgroundColors'][] = 'rgba(255, 206, 86, 0.6)'; // Yellow for medium stock
            } else {
                $inventoryStatusData['backgroundColors'][] = 'rgba(75, 192, 192, 0.6)'; // Green for good stock
            }
        }

        // Get data for monthly orders chart
        $monthlyOrdersData = $this->getMonthlyOrdersData();

        return view('dashboard', compact(
            'totalProducts',
            'lowStockProducts',
            'totalOrders',
            'pendingOrders',
            'totalPurchaseOrders',
            'pendingPurchaseOrders',
            'recentOrders',
            'recentPurchaseOrders',
            'lowStockProductsList',
            'inventoryStatusData',
            'monthlyOrdersData'
        ));
    }

    /**
     * Get monthly orders data for the chart
     */
    private function getMonthlyOrdersData()
    {
        $data = [
            'labels' => [],
            'orders' => [],
            'purchases' => []
        ];

        // Get data for the last 6 months
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $data['labels'][] = $month->format('M Y');

            // Count orders for this month
            $data['orders'][] = Order::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();

            // Count purchase orders for this month
            $data['purchases'][] = PurchaseOrder::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
        }

        return $data;
    }
}
