@extends('layouts.app')

@section('title', 'Order Details')

@section('actions')
    <a href="{{ route('orders.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Orders
    </a>
    
    @if($order->status == 'pending')
        <a href="{{ route('orders.edit', $order) }}" class="btn btn-primary">
            <i class="fas fa-edit"></i> Edit
        </a>
        
        <form action="{{ route('orders.process', $order) }}" method="POST" class="d-inline">
            @csrf
            @method('PATCH')
            <button type="submit" class="btn btn-success">
                <i class="fas fa-check"></i> Process Order
            </button>
        </form>
        
        <form action="{{ route('orders.destroy', $order) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this order?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">
                <i class="fas fa-trash"></i> Delete
            </button>
        </form>
    @elseif($order->status == 'processing')
        <form action="{{ route('orders.complete', $order) }}" method="POST" class="d-inline">
            @csrf
            @method('PATCH')
            <button type="submit" class="btn btn-success">
                <i class="fas fa-check-double"></i> Complete Order
            </button>
        </form>
        
        <form action="{{ route('orders.cancel', $order) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to cancel this order?');">
            @csrf
            @method('PATCH')
            <button type="submit" class="btn btn-danger">
                <i class="fas fa-ban"></i> Cancel Order
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
        <h5 class="card-title mb-0">Order #{{ $order->order_number }}</h5>
        <span class="badge bg-{{ 
            $order->status == 'pending' ? 'warning' : 
            ($order->status == 'processing' ? 'info' : 
            ($order->status == 'completed' ? 'success' : 'danger')) 
        }} fs-6">
            {{ ucfirst($order->status) }}
        </span>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h6 class="fw-bold">Customer Information</h6>
                <p>
                    <strong>Name:</strong> {{ $order->customer_name }}<br>
                    <strong>Email:</strong> {{ $order->customer_email ?: 'N/A' }}<br>
                    <strong>Phone:</strong> {{ $order->customer_phone ?: 'N/A' }}<br>
                    <strong>Shipping Address:</strong> {{ $order->shipping_address ?: 'N/A' }}
                </p>
            </div>
            <div class="col-md-6">
                <h6 class="fw-bold">Order Information</h6>
                <p>
                    <strong>Order Date:</strong> {{ $order->created_at->format('M d, Y H:i') }}<br>
                    <strong>Created By:</strong> {{ $order->user->name }}<br>
                    <strong>Last Updated:</strong> {{ $order->updated_at->format('M d, Y H:i') }}
                </p>
            </div>
        </div>
        
        @if($order->notes)
            <div class="row mt-3">
                <div class="col-md-12">
                    <h6 class="fw-bold">Notes</h6>
                    <p>{{ $order->notes }}</p>
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
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->product->name }}</td>
                            <td>{{ $item->product->sku }}</td>
                            <td>${{ number_format($item->unit_price, 2) }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>${{ number_format($item->total_price, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="5" class="text-end"><strong>Subtotal:</strong></td>
                        <td><strong>${{ number_format($order->total_amount, 2) }}</strong></td>
                    </tr>
                    <tr>
                        <td colspan="5" class="text-end"><strong>Tax:</strong></td>
                        <td>${{ number_format($order->tax_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td colspan="5" class="text-end"><strong>Discount:</strong></td>
                        <td>${{ number_format($order->discount_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td colspan="5" class="text-end"><strong>Grand Total:</strong></td>
                        <td><strong>${{ number_format($order->grand_total, 2) }}</strong></td>
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
