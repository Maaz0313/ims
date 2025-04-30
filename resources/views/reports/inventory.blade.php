@extends('layouts.app')

@section('title', 'Inventory Report')

@section('actions')
    <a href="{{ route('reports.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Reports
    </a>
    <a href="{{ route('reports.export-csv') }}?type=inventory" class="btn btn-success">
        <i class="fas fa-file-csv"></i> Export CSV
    </a>
    <button class="btn btn-info" onclick="window.print()">
        <i class="fas fa-print"></i> Print
    </button>
@endsection

@section('content')
<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title">Inventory Report</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('reports.inventory') }}" method="GET" class="mb-4">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label for="category" class="form-label">Category</label>
                    <select class="form-select" id="category" name="category">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-3">
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
                <div class="col-md-3 mb-3">
                    <label for="stock_status" class="form-label">Stock Status</label>
                    <select class="form-select" id="stock_status" name="stock_status">
                        <option value="">All Stock</option>
                        <option value="in_stock" {{ request('stock_status') == 'in_stock' ? 'selected' : '' }}>In Stock</option>
                        <option value="out_of_stock" {{ request('stock_status') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                        <option value="low_stock" {{ request('stock_status') == 'low_stock' ? 'selected' : '' }}>Low Stock</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <a href="{{ route('reports.inventory') }}" class="btn btn-secondary">
                        <i class="fas fa-undo"></i> Reset
                    </a>
                </div>
            </div>
        </form>
        
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-light">
                    <div class="card-body text-center">
                        <h6 class="text-muted">Total Inventory Value</h6>
                        <h2 class="mb-0">${{ number_format($totalValue, 2) }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-light">
                    <div class="card-body text-center">
                        <h6 class="text-muted">Total Products</h6>
                        <h2 class="mb-0">{{ $products->count() }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-light">
                    <div class="card-body text-center">
                        <h6 class="text-muted">Low Stock Items</h6>
                        <h2 class="mb-0">{{ $products->where('inventory.quantity', '<', 10)->count() }}</h2>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>SKU</th>
                        <th>Category</th>
                        <th>Supplier</th>
                        <th>Quantity</th>
                        <th>Unit Price</th>
                        <th>Total Value</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr>
                            <td>
                                <a href="{{ route('products.show', $product) }}">{{ $product->name }}</a>
                            </td>
                            <td>{{ $product->sku }}</td>
                            <td>{{ $product->category->name ?? 'N/A' }}</td>
                            <td>{{ $product->supplier->name ?? 'N/A' }}</td>
                            <td>{{ $product->inventory->quantity ?? 0 }}</td>
                            <td>${{ number_format($product->price, 2) }}</td>
                            <td>${{ number_format(($product->inventory->quantity ?? 0) * $product->price, 2) }}</td>
                            <td>
                                @if(($product->inventory->quantity ?? 0) <= 0)
                                    <span class="badge bg-danger">Out of Stock</span>
                                @elseif(($product->inventory->quantity ?? 0) < 10)
                                    <span class="badge bg-warning">Low Stock</span>
                                @else
                                    <span class="badge bg-success">In Stock</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">No products found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
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
