@extends('layouts.app')

@section('title', 'Edit Inventory')

@section('actions')
    <a href="{{ route('inventory.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Inventory
    </a>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="card-title">Edit Inventory: {{ $inventory->product->name }}</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('inventory.update', $inventory) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="mb-3">
                <label for="product_name" class="form-label">Product</label>
                <input type="text" class="form-control" id="product_name" value="{{ $inventory->product->name }} ({{ $inventory->product->sku }})" disabled>
            </div>
            
            <div class="mb-3">
                <label for="quantity" class="form-label">Quantity <span class="text-danger">*</span></label>
                <input type="number" class="form-control @error('quantity') is-invalid @enderror" id="quantity" name="quantity" value="{{ old('quantity', $inventory->quantity) }}" min="0" required>
                @error('quantity')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="reorder_level" class="form-label">Reorder Level <span class="text-danger">*</span></label>
                <input type="number" class="form-control @error('reorder_level') is-invalid @enderror" id="reorder_level" name="reorder_level" value="{{ old('reorder_level', $inventory->reorder_level) }}" min="0" required>
                @error('reorder_level')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">Set the minimum quantity at which you want to be notified to reorder.</div>
            </div>
            
            <div class="mb-3">
                <label for="location" class="form-label">Storage Location</label>
                <input type="text" class="form-control @error('location') is-invalid @enderror" id="location" name="location" value="{{ old('location', $inventory->location) }}">
                @error('location')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">Where this product is stored (e.g., "Warehouse A, Shelf 3").</div>
            </div>
            
            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Inventory
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
