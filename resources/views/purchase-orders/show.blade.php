@extends('layouts.app')

@section('title', 'Purchase Order Details')

@section('actions')
    <a href="{{ route('purchase-orders.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Purchase Orders
    </a>
    
    @if($purchaseOrder->status == 'pending')
        <a href="{{ route('purchase-orders.edit', $purchaseOrder) }}" class="btn btn-primary">
            <i class="fas fa-edit"></i> Edit
        </a>
        
        <form action="{{ route('purchase-orders.mark-as-ordered', $purchaseOrder) }}" method="POST" class="d-inline">
            @csrf
            @method('PATCH')
            <button type="submit" class="btn btn-success">
                <i class="fas fa-check"></i> Mark as Ordered
            </button>
        </form>
        
        <form action="{{ route('purchase-orders.destroy', $purchaseOrder) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this purchase order?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">
                <i class="fas fa-trash"></i> Delete
            </button>
        </form>
    @elseif($purchaseOrder->status == 'ordered')
        <a href="{{ route('purchase-orders.receive-form', $purchaseOrder) }}" class="btn btn-success">
            <i class="fas fa-truck-loading"></i> Receive
        </a>
        
        <form action="{{ route('purchase-orders.cancel', $purchaseOrder) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to cancel this purchase order?');">
            @csrf
            @method('PATCH')
            <button type="submit" class="btn btn-danger">
                <i class="fas fa-ban"></i> Cancel
            </button>
        </form>
    @endif
    
    <button class="btn btn-info" onclick="window.print()">
        <i class="fas fa-print"></i> Print
    </button>
@endsection

@section('content')
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Purchase Order #{{ $purchaseOrder->po_number }}</h5>
        <span class="badge bg-{{ 
            $purchaseOrder->status == 'pending' ? 'warning' : 
            ($purchaseOrder->status == 'ordered' ? 'info' : 
            ($purchaseOrder->status == 'received' ? 'success' : 'danger')) 
        }} fs-6">
            {{ ucfirst($purchaseOrder->status) }}
        </span>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h6 class="fw-bold">Supplier Information</h6>
                <p>
                    <strong>Name:</strong> {{ $purchaseOrder->supplier->name }}<br>
                    <strong>Contact:</strong> {{ $purchaseOrder->supplier->contact_person ?: 'N/A' }}<br>
                    <strong>Email:</strong> {{ $purchaseOrder->supplier->email ?: 'N/A' }}<br>
                    <strong>Phone:</strong> {{ $purchaseOrder->supplier->phone ?: 'N/A' }}<br>
                    <strong>Address:</strong> {{ $purchaseOrder->supplier->address ?: 'N/A' }}
                </p>
            </div>
            <div class="col-md-6">
                <h6 class="fw-bold">Order Information</h6>
                <p>
                    <strong>Order Date:</strong> {{ $purchaseOrder->order_date->format('M d, Y') }}<br>
                    <strong>Expected Delivery:</strong> {{ $purchaseOrder->expected_delivery_date ? $purchaseOrder->expected_delivery_date->format('M d, Y') : 'N/A' }}<br>
                    <strong>Delivery Date:</strong> {{ $purchaseOrder->delivery_date ? $purchaseOrder->delivery_date->format('M d, Y') : 'N/A' }}<br>
                    <strong>Created By:</strong> {{ $purchaseOrder->user->name }}<br>
                    <strong>Created At:</strong> {{ $purchaseOrder->created_at->format('M d, Y H:i') }}
                </p>
            </div>
        </div>
        
        @if($purchaseOrder->notes)
            <div class="row mt-3">
                <div class="col-md-12">
                    <h6 class="fw-bold">Notes</h6>
                    <p>{{ $purchaseOrder->notes }}</p>
                </div>
            </div>
        @endif
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="card-title">Order Items</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Product</th>
                        <th>SKU</th>
                        <th>Unit Price</th>
                        <th>Quantity</th>
                        <th>Received</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($purchaseOrder->items as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->product->name }}</td>
                            <td>{{ $item->product->sku }}</td>
                            <td>${{ number_format($item->unit_price, 2) }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>
                                @if($purchaseOrder->status == 'received')
                                    {{ $item->received_quantity }}
                                    @if($item->received_quantity != $item->quantity)
                                        <span class="badge bg-warning">Partial</span>
                                    @endif
                                @else
                                    -
                                @endif
                            </td>
                            <td>${{ number_format($item->total_price, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="6" class="text-end"><strong>Grand Total:</strong></td>
                        <td><strong>${{ number_format($purchaseOrder->total_amount, 2) }}</strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<style>
    @media print {
        .btn, .navbar, .sidebar, footer {
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
    }
</style>
@endsection
