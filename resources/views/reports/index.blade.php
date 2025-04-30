@extends('layouts.app')

@section('title', 'Reports Dashboard')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Reports</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('reports.inventory') }}" class="text-decoration-none">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-boxes fa-3x text-primary mb-3"></i>
                                    <h5 class="card-title">Inventory Report</h5>
                                    <p class="card-text text-muted">View current inventory levels, values, and stock status.</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('reports.sales') }}" class="text-decoration-none">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-chart-line fa-3x text-success mb-3"></i>
                                    <h5 class="card-title">Sales Report</h5>
                                    <p class="card-text text-muted">Analyze sales data, trends, and top-selling products.</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('reports.purchases') }}" class="text-decoration-none">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-truck-loading fa-3x text-info mb-3"></i>
                                    <h5 class="card-title">Purchase Report</h5>
                                    <p class="card-text text-muted">Track purchases, supplier performance, and costs.</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('reports.profit-loss') }}" class="text-decoration-none">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-dollar-sign fa-3x text-warning mb-3"></i>
                                    <h5 class="card-title">Profit & Loss</h5>
                                    <p class="card-text text-muted">Analyze revenue, costs, and profitability.</p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title">Sales Overview</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h6 class="text-muted mb-1">Current Month Sales</h6>
                        <h3 class="mb-0">${{ number_format($currentMonthSales, 2) }}</h3>
                    </div>
                    <div class="text-end">
                        <h6 class="text-muted mb-1">vs Previous Month</h6>
                        <h5 class="mb-0 {{ $salesPercentageChange >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ $salesPercentageChange >= 0 ? '+' : '' }}{{ number_format($salesPercentageChange, 1) }}%
                        </h5>
                    </div>
                </div>
                <div class="text-center">
                    <a href="{{ route('reports.sales') }}" class="btn btn-sm btn-outline-primary">View Detailed Report</a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title">Inventory Value</h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <h1 class="display-4">${{ number_format($inventoryValue, 2) }}</h1>
                    <p class="text-muted">Total value of current inventory</p>
                </div>
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="mb-0">{{ $totalProducts }}</h6>
                        <small class="text-muted">Products</small>
                    </div>
                    <div>
                        <h6 class="mb-0">{{ $totalCategories }}</h6>
                        <small class="text-muted">Categories</small>
                    </div>
                    <div>
                        <h6 class="mb-0">{{ $totalSuppliers }}</h6>
                        <small class="text-muted">Suppliers</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Low Stock Alert</h5>
                <span class="badge bg-warning">{{ $lowStockProducts->count() }}</span>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @forelse($lowStockProducts as $product)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">{{ $product->name }}</h6>
                                <small class="text-muted">{{ $product->category->name ?? 'No Category' }}</small>
                            </div>
                            <span class="badge bg-danger">{{ $product->inventory->quantity ?? 0 }} left</span>
                        </li>
                    @empty
                        <li class="list-group-item text-center">No low stock items</li>
                    @endforelse
                </ul>
                <div class="card-footer text-center">
                    <a href="{{ route('reports.inventory') }}?stock_status=low_stock" class="btn btn-sm btn-outline-warning">View All</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Recent Orders</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Customer</th>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentOrders as $order)
                                <tr>
                                    <td>
                                        <a href="{{ route('orders.show', $order) }}">{{ $order->order_number }}</a>
                                    </td>
                                    <td>{{ $order->customer_name }}</td>
                                    <td>{{ $order->created_at->format('M d, Y') }}</td>
                                    <td>${{ number_format($order->grand_total, 2) }}</td>
                                    <td>
                                        <span class="badge bg-{{ 
                                            $order->status == 'pending' ? 'warning' : 
                                            ($order->status == 'processing' ? 'info' : 
                                            ($order->status == 'completed' ? 'success' : 'danger')) 
                                        }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">No recent orders</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer text-center">
                <a href="{{ route('orders.index') }}" class="btn btn-sm btn-outline-primary">View All Orders</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Recent Purchase Orders</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>PO #</th>
                                <th>Supplier</th>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentPurchaseOrders as $po)
                                <tr>
                                    <td>
                                        <a href="{{ route('purchase-orders.show', $po) }}">{{ $po->po_number }}</a>
                                    </td>
                                    <td>{{ $po->supplier->name }}</td>
                                    <td>{{ $po->created_at->format('M d, Y') }}</td>
                                    <td>${{ number_format($po->total_amount, 2) }}</td>
                                    <td>
                                        <span class="badge bg-{{ 
                                            $po->status == 'pending' ? 'warning' : 
                                            ($po->status == 'ordered' ? 'info' : 
                                            ($po->status == 'received' ? 'success' : 'danger')) 
                                        }}">
                                            {{ ucfirst($po->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">No recent purchase orders</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer text-center">
                <a href="{{ route('purchase-orders.index') }}" class="btn btn-sm btn-outline-primary">View All Purchase Orders</a>
            </div>
        </div>
    </div>
</div>
@endsection
