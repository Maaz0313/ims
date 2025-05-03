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
            <div class="mb-4">
                <form action="{{ route('products.index') }}" method="GET" class="mb-3">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <div class="input-group">
                                <input type="text" class="form-control live-search-input"
                                    placeholder="Search products..." name="search" value="{{ request('search') }}">
                                <span class="input-group-text loading-indicator d-none">
                                    <i class="fas fa-spinner fa-spin"></i>
                                </span>
                                <button class="btn btn-outline-secondary" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" name="category_id">
                                <option value="">All Categories</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" name="supplier_id">
                                <option value="">All Suppliers</option>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}"
                                        {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" name="stock_status">
                                <option value="">All Stock Status</option>
                                <option value="in_stock" {{ request('stock_status') == 'in_stock' ? 'selected' : '' }}>In
                                    Stock</option>
                                <option value="out_of_stock"
                                    {{ request('stock_status') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                                <option value="low_stock" {{ request('stock_status') == 'low_stock' ? 'selected' : '' }}>
                                    Low Stock</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex">
                                <button type="submit" class="btn btn-primary me-2">Filter</button>
                                <a href="{{ route('products.index') }}" class="btn btn-secondary">Reset</a>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" placeholder="Min Price" name="min_price"
                                    value="{{ request('min_price') }}" min="0" step="0.01">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" placeholder="Max Price" name="max_price"
                                    value="{{ request('max_price') }}" min="0" step="0.01">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" name="sort_by">
                                <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Sort by Name
                                </option>
                                <option value="price" {{ request('sort_by') == 'price' ? 'selected' : '' }}>Sort by Price
                                </option>
                                <option value="stock" {{ request('sort_by') == 'stock' ? 'selected' : '' }}>Sort by Stock
                                </option>
                                <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Sort
                                    by Date Added</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" name="sort_direction">
                                <option value="asc" {{ request('sort_direction') == 'asc' ? 'selected' : '' }}>Ascending
                                </option>
                                <option value="desc" {{ request('sort_direction') == 'desc' ? 'selected' : '' }}>
                                    Descending</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>

            <div class="mb-3">
                <a href="{{ route('export.products') }}" class="btn btn-success">Export Products</a>
            </div>
            <div class="content-area">
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
                                                <img src="{{ asset('storage/' . $product->image) }}"
                                                    alt="{{ $product->name }}" width="50">
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
                                                <a href="{{ route('products.show', $product) }}"
                                                    class="btn btn-info btn-sm">
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
                    <div class="mt-3">
                        {{ $products->appends(request()->query())->links() }}
                    </div>
                @else
                    <div class="alert alert-info">
                        No products found. <a href="{{ route('products.create') }}">Create your first product</a>.
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
