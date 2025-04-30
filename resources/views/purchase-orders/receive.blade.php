@extends('layouts.app')

@section('title', 'Receive Purchase Order')

@section('actions')
    <a href="{{ route('purchase-orders.show', $purchaseOrder) }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Purchase Order
    </a>
@endsection

@section('content')
<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title">Receive Purchase Order #{{ $purchaseOrder->po_number }}</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h6 class="fw-bold">Supplier Information</h6>
                <p>
                    <strong>Name:</strong> {{ $purchaseOrder->supplier->name }}<br>
                    <strong>Contact:</strong> {{ $purchaseOrder->supplier->contact_person ?: 'N/A' }}<br>
                    <strong>Email:</strong> {{ $purchaseOrder->supplier->email ?: 'N/A' }}<br>
                    <strong>Phone:</strong> {{ $purchaseOrder->supplier->phone ?: 'N/A' }}
                </p>
            </div>
            <div class="col-md-6">
                <h6 class="fw-bold">Order Information</h6>
                <p>
                    <strong>Order Date:</strong> {{ $purchaseOrder->order_date->format('M d, Y') }}<br>
                    <strong>Expected Delivery:</strong> {{ $purchaseOrder->expected_delivery_date ? $purchaseOrder->expected_delivery_date->format('M d, Y') : 'N/A' }}<br>
                    <strong>Total Amount:</strong> ${{ number_format($purchaseOrder->total_amount, 2) }}
                </p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="card-title">Receive Items</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('purchase-orders.receive', $purchaseOrder) }}" method="POST">
            @csrf
            @method('PATCH')
            
            <div class="mb-3">
                <label for="delivery_date" class="form-label">Delivery Date <span class="text-danger">*</span></label>
                <input type="date" class="form-control @error('delivery_date') is-invalid @enderror" id="delivery_date" name="delivery_date" value="{{ old('delivery_date', date('Y-m-d')) }}" required>
                @error('delivery_date')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="table-responsive mb-3">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th>SKU</th>
                            <th>Ordered Quantity</th>
                            <th>Received Quantity</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($purchaseOrder->items as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item->product->name }}</td>
                                <td>{{ $item->product->sku }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>
                                    <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item->id }}">
                                    <input type="number" class="form-control @error('items.' . $index . '.received_quantity') is-invalid @enderror" 
                                        name="items[{{ $index }}][received_quantity]" 
                                        value="{{ old('items.' . $index . '.received_quantity', $item->quantity) }}" 
                                        min="0" max="{{ $item->quantity }}">
                                    @error('items.' . $index . '.received_quantity')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Enter 0 if item was not received</small>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Receiving this purchase order will update your inventory quantities. This action cannot be undone.
            </div>
            
            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <button type="submit" class="btn btn-primary" onclick="return confirm('Are you sure you want to receive this purchase order? This will update your inventory.')">
                    <i class="fas fa-save"></i> Complete Receiving
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
