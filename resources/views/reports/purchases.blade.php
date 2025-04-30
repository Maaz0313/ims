@extends('layouts.app')

@section('title', 'Purchase Report')

@section('actions')
    <a href="{{ route('reports.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Reports
    </a>
    <a href="{{ route('reports.export-csv') }}?type=purchases&start_date={{ $startDate->format('Y-m-d') }}&end_date={{ $endDate->format('Y-m-d') }}" class="btn btn-success">
        <i class="fas fa-file-csv"></i> Export CSV
    </a>
    <button class="btn btn-info" onclick="window.print()">
        <i class="fas fa-print"></i> Print
    </button>
@endsection

@section('content')
<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title">Purchase Report</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('reports.purchases') }}" method="GET" class="mb-4">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $startDate->format('Y-m-d') }}">
                </div>
                <div class="col-md-3 mb-3">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $endDate->format('Y-m-d') }}">
                </div>
                <div class="col-md-2 mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="ordered" {{ request('status') == 'ordered' ? 'selected' : '' }}>Ordered</option>
                        <option value="received" {{ request('status') == 'received' ? 'selected' : '' }}>Received</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-2 mb-3">
                    <label for="supplier" class="form-label">Supplier</label>
                    <select class="form-select" id="supplier" name="supplier">
                        <option value="">All Suppliers</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ request('supplier') == $supplier->id ? 'selected' : '' }}>
                                {{ $supplier->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 mb-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <a href="{{ route('reports.purchases') }}" class="btn btn-secondary">
                        <i class="fas fa-undo"></i> Reset
                    </a>
                </div>
            </div>
        </form>
        
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card bg-light">
                    <div class="card-body text-center">
                        <h6 class="text-muted">Total Purchases</h6>
                        <h2 class="mb-0">${{ number_format($totalPurchases, 2) }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card bg-light">
                    <div class="card-body text-center">
                        <h6 class="text-muted">Total Purchase Orders</h6>
                        <h2 class="mb-0">{{ $totalOrders }}</h2>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Purchases by Supplier</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="supplierChart" height="200"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Purchase Orders by Status</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="statusChart" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Top Purchased Products</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Quantity Purchased</th>
                                        <th>Total Cost</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse(array_slice($purchasesByProduct, 0, 5) as $product)
                                        <tr>
                                            <td>{{ $product['name'] }}</td>
                                            <td>{{ $product['quantity'] }}</td>
                                            <td>${{ number_format($product['amount'], 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center">No data available</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Purchase Orders List</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>PO #</th>
                                <th>Date</th>
                                <th>Supplier</th>
                                <th>Expected Delivery</th>
                                <th>Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($purchaseOrders as $po)
                                <tr>
                                    <td>
                                        <a href="{{ route('purchase-orders.show', $po) }}">{{ $po->po_number }}</a>
                                    </td>
                                    <td>{{ $po->created_at->format('M d, Y') }}</td>
                                    <td>{{ $po->supplier->name }}</td>
                                    <td>{{ $po->expected_delivery_date ? $po->expected_delivery_date->format('M d, Y') : 'N/A' }}</td>
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
                                    <td colspan="6" class="text-center">No purchase orders found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        .btn, .navbar, .sidebar, footer, form {
            display: none !important;
        }
        
        .card {
            border: none !important;
            box-shadow: none !important;
        }
        
        .card-header {
            background-color: #fff !important;
            color: #000 !important;
        }
        
        body {
            padding: 0 !important;
            margin: 0 !important;
        }
        
        .container-fluid {
            padding: 0 !important;
            margin: 0 !important;
        }
        
        .col-md-9, .col-lg-10 {
            width: 100% !important;
            max-width: 100% !important;
            flex: 0 0 100% !important;
        }
        
        .table {
            width: 100% !important;
        }
    }
</style>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Supplier chart
        const supplierChartCtx = document.getElementById('supplierChart').getContext('2d');
        
        const supplierData = @json($purchasesBySupplier);
        const supplierLabels = supplierData.map(item => item.name);
        const supplierAmounts = supplierData.map(item => item.amount);
        
        new Chart(supplierChartCtx, {
            type: 'bar',
            data: {
                labels: supplierLabels,
                datasets: [{
                    label: 'Purchase Amount',
                    data: supplierAmounts,
                    backgroundColor: 'rgba(54, 162, 235, 0.8)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value;
                            }
                        }
                    }
                }
            }
        });
        
        // Status chart
        const statusChartCtx = document.getElementById('statusChart').getContext('2d');
        
        const pendingCount = {{ $purchaseOrders->where('status', 'pending')->count() }};
        const orderedCount = {{ $purchaseOrders->where('status', 'ordered')->count() }};
        const receivedCount = {{ $purchaseOrders->where('status', 'received')->count() }};
        const cancelledCount = {{ $purchaseOrders->where('status', 'cancelled')->count() }};
        
        new Chart(statusChartCtx, {
            type: 'doughnut',
            data: {
                labels: ['Pending', 'Ordered', 'Received', 'Cancelled'],
                datasets: [{
                    data: [pendingCount, orderedCount, receivedCount, cancelledCount],
                    backgroundColor: [
                        'rgba(255, 193, 7, 0.8)',
                        'rgba(23, 162, 184, 0.8)',
                        'rgba(40, 167, 69, 0.8)',
                        'rgba(220, 53, 69, 0.8)'
                    ],
                    borderColor: [
                        'rgba(255, 193, 7, 1)',
                        'rgba(23, 162, 184, 1)',
                        'rgba(40, 167, 69, 1)',
                        'rgba(220, 53, 69, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    });
</script>
@endsection
