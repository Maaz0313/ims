@extends('layouts.app')

@section('title', 'Product Details')

@section('actions')
    <a href="{{ route('products.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Products
    </a>
    <a href="{{ route('products.edit', $product) }}" class="btn btn-primary">
        <i class="fas fa-edit"></i> Edit
    </a>
    <form action="{{ route('products.destroy', $product) }}" method="POST" class="d-inline"
        onsubmit="return confirm('Are you sure you want to delete this product?');">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger">
            <i class="fas fa-trash"></i> Delete
        </button>
    </form>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Product Image</h5>
                </div>
                <div class="card-body text-center">
                    @if ($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}"
                            class="img-fluid rounded" style="max-height: 300px;">
                    @else
                        <div class="p-5 bg-light rounded">
                            <i class="fas fa-image fa-5x text-secondary"></i>
                            <p class="mt-3 text-muted">No image available</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Inventory Status</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0">Current Stock:</h6>
                        <span class="badge bg-primary fs-6">{{ $product->inventory->quantity ?? 0 }}</span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0">Reorder Level:</h6>
                        <span>{{ $product->inventory->reorder_level ?? 'N/A' }}</span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0">Status:</h6>
                        @if ($product->inventory && $product->inventory->quantity <= $product->inventory->reorder_level)
                            <span class="badge bg-danger">Low Stock</span>
                        @elseif($product->inventory && $product->inventory->quantity > 0)
                            <span class="badge bg-success">In Stock</span>
                        @else
                            <span class="badge bg-warning">Out of Stock</span>
                        @endif
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Storage Location:</h6>
                        <span>{{ $product->inventory->location ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Product Codes</h5>
                </div>
                <div class="card-body">
                    <ul class="nav nav-tabs" id="productCodeTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="barcode-tab" data-bs-toggle="tab" data-bs-target="#barcode"
                                type="button" role="tab" aria-controls="barcode" aria-selected="true">Barcode</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="qrcode-tab" data-bs-toggle="tab" data-bs-target="#qrcode"
                                type="button" role="tab" aria-controls="qrcode" aria-selected="false">QR Code</button>
                        </li>
                    </ul>
                    <div class="tab-content p-3" id="productCodeTabsContent">
                        <div class="tab-pane fade show active" id="barcode" role="tabpanel" aria-labelledby="barcode-tab">
                            <div class="text-center">
                                <div class="mb-3">
                                    {!! $product->getBarcode() !!}
                                </div>
                                <div class="text-muted small">{{ $product->sku }}</div>
                                <div class="mt-3">
                                    <a href="{{ $product->getBarcodePNG() }}"
                                        download="product-{{ $product->sku }}-barcode.png" class="btn btn-sm btn-primary">
                                        <i class="fas fa-download"></i> Download Barcode
                                    </a>
                                    <button onclick="window.print()" class="btn btn-sm btn-secondary">
                                        <i class="fas fa-print"></i> Print
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="qrcode" role="tabpanel" aria-labelledby="qrcode-tab">
                            <div class="text-center">
                                <div class="mb-3">
                                    <img src="{{ $product->getQRCode() }}" alt="QR Code" class="img-fluid"
                                        style="max-width: 150px;">
                                </div>
                                <div class="text-muted small">{{ $product->sku }}</div>
                                <div class="mt-3">
                                    <a href="{{ $product->getQRCode() }}"
                                        download="product-{{ $product->sku }}-qrcode.png" class="btn btn-sm btn-primary">
                                        <i class="fas fa-download"></i> Download QR Code
                                    </a>
                                    <button onclick="window.print()" class="btn btn-sm btn-secondary">
                                        <i class="fas fa-print"></i> Print
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Product Information</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <tr>
                            <th style="width: 150px;">ID</th>
                            <td>{{ $product->id }}</td>
                        </tr>
                        <tr>
                            <th>Name</th>
                            <td>{{ $product->name }}</td>
                        </tr>
                        <tr>
                            <th>SKU</th>
                            <td>{{ $product->sku }}</td>
                        </tr>
                        <tr>
                            <th>Description</th>
                            <td>{{ $product->description ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Category</th>
                            <td>
                                <a href="{{ route('categories.show', $product->category) }}">
                                    {{ $product->category->name }}
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <th>Supplier</th>
                            <td>
                                @if ($product->supplier)
                                    <a href="{{ route('suppliers.show', $product->supplier) }}">
                                        {{ $product->supplier->name }}
                                    </a>
                                @else
                                    N/A
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Price</th>
                            <td>${{ number_format($product->price, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Cost Price</th>
                            <td>{{ $product->cost_price ? '$' . number_format($product->cost_price, 2) : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Profit Margin</th>
                            <td>
                                @if ($product->cost_price && $product->cost_price > 0)
                                    {{ number_format((($product->price - $product->cost_price) / $product->price) * 100, 2) }}%
                                @else
                                    N/A
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Created At</th>
                            <td>{{ $product->created_at->format('M d, Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Updated At</th>
                            <td>{{ $product->updated_at->format('M d, Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title">Purchase History</h5>
                        </div>
                        <div class="card-body">
                            @if ($product->purchaseOrderItems->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>PO #</th>
                                                <th>Date</th>
                                                <th>Qty</th>
                                                <th>Price</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($product->purchaseOrderItems as $item)
                                                <tr>
                                                    <td>
                                                        <a
                                                            href="{{ route('purchase-orders.show', $item->purchaseOrder) }}">
                                                            {{ $item->purchaseOrder->po_number }}
                                                        </a>
                                                    </td>
                                                    <td>{{ $item->purchaseOrder->order_date->format('M d, Y') }}</td>
                                                    <td>{{ $item->quantity }}</td>
                                                    <td>${{ number_format($item->unit_price, 2) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-info">
                                    No purchase history found.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title">Sales History</h5>
                        </div>
                        <div class="card-body">
                            @if ($product->orderItems->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Order #</th>
                                                <th>Date</th>
                                                <th>Qty</th>
                                                <th>Price</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($product->orderItems as $item)
                                                <tr>
                                                    <td>
                                                        <a href="{{ route('orders.show', $item->order) }}">
                                                            {{ $item->order->order_number }}
                                                        </a>
                                                    </td>
                                                    <td>{{ $item->order->created_at->format('M d, Y') }}</td>
                                                    <td>{{ $item->quantity }}</td>
                                                    <td>${{ number_format($item->unit_price, 2) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-info">
                                    No sales history found.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
