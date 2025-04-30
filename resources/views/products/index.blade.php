@extends('layouts.app')

@section('title', 'Products')

@section('actions')
    <a href="{{ route('batch.prices') }}" class="btn btn-primary me-2">
        <i class="fas fa-tags"></i> Batch Price Update
    </a>
    <a href="{{ route('products.create') }}" class="btn btn-success">
        <i class="fas fa-plus"></i> Add Product
    </a>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Products List</h5>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <a href="{{ route('export.products') }}" class="btn btn-success">Export Products</a>
            </div>
            @if ($products->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Image</th>
                                <th>Name</th>
                                <th>SKU</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $product)
                                <tr>
                                    <td>{{ $product->id }}</td>
                                    <td>
                                        @if ($product->image)
                                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}"
                                                width="50">
                                        @else
                                            <span class="text-muted">No image</span>
                                        @endif
                                    </td>
                                    <td>{{ $product->name }}</td>
                                    <td>{{ $product->sku }}</td>
                                    <td>{{ $product->category->name }}</td>
                                    <td>${{ number_format($product->price, 2) }}</td>
                                    <td>{{ $product->inventory->quantity ?? 0 }}</td>
                                    <td>
                                        @if ($product->inventory && $product->inventory->quantity <= $product->inventory->reorder_level)
                                            <span class="badge bg-danger">Low Stock</span>
                                        @elseif($product->inventory && $product->inventory->quantity > 0)
                                            <span class="badge bg-success">In Stock</span>
                                        @else
                                            <span class="badge bg-warning">Out of Stock</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('products.show', $product) }}" class="btn btn-info btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('products.edit', $product) }}"
                                                class="btn btn-primary btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('products.destroy', $product) }}" method="POST"
                                                class="d-inline"
                                                onsubmit="return confirm('Are you sure you want to delete this product?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-info">
                    No products found. <a href="{{ route('products.create') }}">Create your first product</a>.
                </div>
            @endif
        </div>
    </div>
@endsection
