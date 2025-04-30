@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="container-fluid">
        <!-- Stats Cards -->
        <div class="row">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Total Products</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalProducts }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-box fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Low Stock Products</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $lowStockProducts }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Total Orders</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalOrders }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-file-invoice fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Purchase Orders</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalPurchaseOrders }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row">
            <!-- Inventory Status Chart -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Inventory Status</h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-container" style="position: relative; height:300px;">
                            <canvas id="inventoryStatusChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Monthly Orders Chart -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Monthly Activity</h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-container" style="position: relative; height:300px;">
                            <canvas id="monthlyOrdersChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Row -->
        <div class="row">
            <!-- Recent Orders -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Recent Orders</h6>
                    </div>
                    <div class="card-body">
                        @if ($recentOrders->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Order #</th>
                                            <th>Customer</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($recentOrders as $order)
                                            <tr>
                                                <td>{{ $order->order_number }}</td>
                                                <td>{{ $order->customer_name }}</td>
                                                <td>${{ number_format($order->grand_total, 2) }}</td>
                                                <td>
                                                    <span
                                                        class="badge bg-{{ $order->status == 'completed' ? 'success' : ($order->status == 'pending' ? 'warning' : 'info') }}">
                                                        {{ ucfirst($order->status) }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-center">No recent orders found.</p>
                        @endif
                        <div class="mt-3 text-center">
                            <a href="{{ route('orders.index') }}" class="btn btn-sm btn-primary">View All Orders</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Purchase Orders -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Recent Purchase Orders</h6>
                    </div>
                    <div class="card-body">
                        @if ($recentPurchaseOrders->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>PO #</th>
                                            <th>Supplier</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($recentPurchaseOrders as $po)
                                            <tr>
                                                <td>{{ $po->po_number }}</td>
                                                <td>{{ $po->supplier->name }}</td>
                                                <td>${{ number_format($po->total_amount, 2) }}</td>
                                                <td>
                                                    <span
                                                        class="badge bg-{{ $po->status == 'received' ? 'success' : ($po->status == 'pending' ? 'warning' : 'info') }}">
                                                        {{ ucfirst($po->status) }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-center">No recent purchase orders found.</p>
                        @endif
                        <div class="mt-3 text-center">
                            <a href="{{ route('purchase-orders.index') }}" class="btn btn-sm btn-primary">View All Purchase
                                Orders</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Low Stock Products -->
        <div class="row">
            <div class="col-12 mb-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Low Stock Products</h6>
                    </div>
                    <div class="card-body">
                        @if ($lowStockProductsList->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>SKU</th>
                                            <th>Current Stock</th>
                                            <th>Reorder Level</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($lowStockProductsList as $inventory)
                                            <tr>
                                                <td>{{ $inventory->product->name }}</td>
                                                <td>{{ $inventory->product->sku }}</td>
                                                <td>{{ $inventory->quantity }}</td>
                                                <td>{{ $inventory->reorder_level }}</td>
                                                <td>
                                                    <a href="{{ route('purchase-orders.create') }}"
                                                        class="btn btn-sm btn-warning">
                                                        <i class="fas fa-shopping-cart"></i> Order
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-center">No low stock products found.</p>
                        @endif
                        <div class="mt-3 text-center">
                            <a href="{{ route('inventory.index') }}" class="btn btn-sm btn-primary">View All Inventory</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Initialize Inventory Status Chart
        const inventoryStatusCtx = document.getElementById('inventoryStatusChart').getContext('2d');
        const inventoryStatusChart = new Chart(inventoryStatusCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($inventoryStatusData['labels']) !!},
                datasets: [{
                    label: 'Current Stock',
                    data: {!! json_encode($inventoryStatusData['data']) !!},
                    backgroundColor: {!! json_encode($inventoryStatusData['backgroundColors']) !!},
                    borderColor: {!! json_encode($inventoryStatusData['backgroundColors']) !!},
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Initialize Monthly Orders Chart
        const monthlyOrdersCtx = document.getElementById('monthlyOrdersChart').getContext('2d');
        const monthlyOrdersChart = new Chart(monthlyOrdersCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($monthlyOrdersData['labels']) !!},
                datasets: [{
                        label: 'Orders',
                        data: {!! json_encode($monthlyOrdersData['orders']) !!},
                        backgroundColor: 'rgba(78, 115, 223, 0.05)',
                        borderColor: 'rgba(78, 115, 223, 1)',
                        pointBackgroundColor: 'rgba(78, 115, 223, 1)',
                        pointBorderColor: '#fff',
                        pointHoverBackgroundColor: '#fff',
                        pointHoverBorderColor: 'rgba(78, 115, 223, 1)',
                        borderWidth: 2,
                        fill: true
                    },
                    {
                        label: 'Purchase Orders',
                        data: {!! json_encode($monthlyOrdersData['purchases']) !!},
                        backgroundColor: 'rgba(28, 200, 138, 0.05)',
                        borderColor: 'rgba(28, 200, 138, 1)',
                        pointBackgroundColor: 'rgba(28, 200, 138, 1)',
                        pointBorderColor: '#fff',
                        pointHoverBackgroundColor: '#fff',
                        pointHoverBorderColor: 'rgba(28, 200, 138, 1)',
                        borderWidth: 2,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    </script>
@endsection
