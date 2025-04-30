@extends('layouts.app')

@section('title', 'Inventory Details')

@section('actions')
    <a href="{{ route('inventory.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Inventory
    </a>
    <a href="{{ route('inventory.edit', $inventory) }}" class="btn btn-primary">
        <i class="fas fa-edit"></i> Edit
    </a>
@endsection

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title">Inventory Information</h5>
            </div>
            <div class="card-body">
                <table class="table">
                    <tr>
                        <th style="width: 150px;">ID</th>
                        <td>{{ $inventory->id }}</td>
                    </tr>
                    <tr>
                        <th>Product</th>
                        <td>
                            <a href="{{ route('products.show', $inventory->product) }}">
                                {{ $inventory->product->name }}
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <th>SKU</th>
                        <td>{{ $inventory->product->sku }}</td>
                    </tr>
                    <tr>
                        <th>Quantity</th>
                        <td>{{ $inventory->quantity }}</td>
                    </tr>
                    <tr>
                        <th>Reorder Level</th>
                        <td>{{ $inventory->reorder_level }}</td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            @if($inventory->quantity <= 0)
                                <span class="badge bg-danger">Out of Stock</span>
                            @elseif($inventory->quantity <= $inventory->reorder_level)
                                <span class="badge bg-warning">Low Stock</span>
                            @else
                                <span class="badge bg-success">In Stock</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Location</th>
                        <td>{{ $inventory->location ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Created At</th>
                        <td>{{ $inventory->created_at->format('M d, Y H:i') }}</td>
                    </tr>
                    <tr>
                        <th>Updated At</th>
                        <td>{{ $inventory->updated_at->format('M d, Y H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title">Product Details</h5>
            </div>
            <div class="card-body">
                <table class="table">
                    <tr>
                        <th style="width: 150px;">Name</th>
                        <td>{{ $inventory->product->name }}</td>
                    </tr>
                    <tr>
                        <th>Description</th>
                        <td>{{ $inventory->product->description ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Category</th>
                        <td>
                            @if($inventory->product->category)
                                <a href="{{ route('categories.show', $inventory->product->category) }}">
                                    {{ $inventory->product->category->name }}
                                </a>
                            @else
                                N/A
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Price</th>
                        <td>${{ number_format($inventory->product->price, 2) }}</td>
                    </tr>
                    <tr>
                        <th>Cost Price</th>
                        <td>${{ number_format($inventory->product->cost_price, 2) }}</td>
                    </tr>
                </table>
                
                <div class="mt-3">
                    <a href="{{ route('products.show', $inventory->product) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-eye"></i> View Full Product Details
                    </a>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#adjustModal">
                        <i class="fas fa-plus-minus"></i> Adjust Inventory
                    </button>
                    <a href="{{ route('purchase-orders.create') }}" class="btn btn-primary">
                        <i class="fas fa-shopping-cart"></i> Create Purchase Order
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Adjust Inventory Modal -->
<div class="modal fade" id="adjustModal" tabindex="-1" aria-labelledby="adjustModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="adjustModalLabel">Adjust Inventory: {{ $inventory->product->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('inventory.update', $inventory) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="current_quantity" class="form-label">Current Quantity</label>
                        <input type="text" class="form-control" id="current_quantity" value="{{ $inventory->quantity }}" disabled>
                    </div>
                    <div class="mb-3">
                        <label for="adjustment_type" class="form-label">Adjustment Type</label>
                        <select class="form-select" id="adjustment_type" name="adjustment_type" required>
                            <option value="add">Add to Inventory</option>
                            <option value="subtract">Remove from Inventory</option>
                            <option value="set">Set Exact Quantity</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="quantity" class="form-label">Quantity</label>
                        <input type="number" class="form-control" id="quantity" name="quantity" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                        <div class="form-text">Reason for adjustment, reference numbers, etc.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
