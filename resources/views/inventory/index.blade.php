@extends('layouts.app')

@section('title', 'Inventory')

@section('actions')
    <a href="{{ route('batch.inventory') }}" class="btn btn-primary me-2">
        <i class="fas fa-layer-group"></i> Batch Update
    </a>
    <a href="{{ route('inventory.create') }}" class="btn btn-success">
        <i class="fas fa-plus"></i> Add Inventory
    </a>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="card-title">Inventory List</h5>
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <form action="{{ route('inventory.index') }}" method="GET" class="d-flex">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Search inventory..." name="search" value="{{ request('search') }}">
                        <button class="btn btn-outline-secondary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>
            <div>
                <div class="btn-group">
                    <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ route('inventory.index', ['filter' => 'low_stock']) }}">Low Stock</a></li>
                        <li><a class="dropdown-item" href="{{ route('inventory.index', ['filter' => 'out_of_stock']) }}">Out of Stock</a></li>
                        <li><a class="dropdown-item" href="{{ route('inventory.index', ['filter' => 'in_stock']) }}">In Stock</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="{{ route('inventory.index') }}">Clear Filter</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="mb-3">
            <a href="{{ route('export.inventory') }}" class="btn btn-success">Export Inventory</a>
        </div>
        @if(count($inventory) > 0)
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Product</th>
                            <th>SKU</th>
                            <th>Quantity</th>
                            <th>Reorder Level</th>
                            <th>Status</th>
                            <th>Location</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($inventory as $item)
                            <tr>
                                <td>{{ $item->id }}</td>
                                <td>
                                    <a href="{{ route('products.show', $item->product) }}">
                                        {{ $item->product->name }}
                                    </a>
                                </td>
                                <td>{{ $item->product->sku }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ $item->reorder_level }}</td>
                                <td>
                                    @if($item->quantity <= 0)
                                        <span class="badge bg-danger">Out of Stock</span>
                                    @elseif($item->quantity <= $item->reorder_level)
                                        <span class="badge bg-warning">Low Stock</span>
                                    @else
                                        <span class="badge bg-success">In Stock</span>
                                    @endif
                                </td>
                                <td>{{ $item->location ?? 'N/A' }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('inventory.edit', $item) }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#adjustModal{{ $item->id }}">
                                            <i class="fas fa-plus-minus"></i>
                                        </button>
                                    </div>
                                    
                                    <!-- Adjust Inventory Modal -->
                                    <div class="modal fade" id="adjustModal{{ $item->id }}" tabindex="-1" aria-labelledby="adjustModalLabel{{ $item->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="adjustModalLabel{{ $item->id }}">Adjust Inventory: {{ $item->product->name }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <form action="{{ route('inventory.update', $item) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label for="current_quantity" class="form-label">Current Quantity</label>
                                                            <input type="text" class="form-control" id="current_quantity" value="{{ $item->quantity }}" disabled>
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
                                                            <label for="reorder_level" class="form-label">Reorder Level</label>
                                                            <input type="number" class="form-control" id="reorder_level" name="reorder_level" value="{{ $item->reorder_level }}" min="0" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="location" class="form-label">Storage Location</label>
                                                            <input type="text" class="form-control" id="location" name="location" value="{{ $item->location }}">
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="notes" class="form-label">Notes</label>
                                                            <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
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
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-4">
                {{ $inventory->links('pagination::bootstrap-4') }}
            </div>
        @else
            <div class="alert alert-info">
                No inventory records found.
            </div>
        @endif
    </div>
</div>
@endsection
