@extends('layouts.app')

@section('title', 'Suppliers')

@section('actions')
    <a href="{{ route('suppliers.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add Supplier
    </a>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Suppliers List</h5>
        </div>
        <div class="card-body">
            <div class="mb-4">
                <form action="{{ route('suppliers.index') }}" method="GET" class="mb-3">
                    <div class="row g-3">
                        <div class="col-md-5">
                            <div class="input-group">
                                <input type="text" class="form-control live-search-input"
                                    placeholder="Search suppliers..." name="search" value="{{ request('search') }}">
                                <span class="input-group-text loading-indicator d-none">
                                    <i class="fas fa-spinner fa-spin"></i>
                                </span>
                                <button class="btn btn-outline-secondary" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-text">Min Products</span>
                                <input type="number" class="form-control" name="min_products"
                                    value="{{ request('min_products') }}" min="0">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" name="sort_by">
                                <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Sort by Name
                                </option>
                                <option value="products_count"
                                    {{ request('sort_by') == 'products_count' ? 'selected' : '' }}>Sort by Products</option>
                                <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Sort
                                    by Date Added</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <div class="d-flex">
                                <select class="form-select me-2" name="sort_direction">
                                    <option value="asc" {{ request('sort_direction') == 'asc' ? 'selected' : '' }}>
                                        Ascending</option>
                                    <option value="desc" {{ request('sort_direction') == 'desc' ? 'selected' : '' }}>
                                        Descending</option>
                                </select>
                                <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-redo"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="mb-3">
                <a href="{{ route('export.suppliers') }}" class="btn btn-success">Export Suppliers</a>
            </div>
            <div class="content-area">
                @if ($suppliers->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Contact Person</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Products</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($suppliers as $supplier)
                                    <tr>
                                        <td>{{ $supplier->id }}</td>
                                        <td>{{ $supplier->name }}</td>
                                        <td>{{ $supplier->contact_person ?? 'N/A' }}</td>
                                        <td>{{ $supplier->email ?? 'N/A' }}</td>
                                        <td>{{ $supplier->phone ?? 'N/A' }}</td>
                                        <td>{{ $supplier->products->count() }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('suppliers.show', $supplier) }}"
                                                    class="btn btn-info btn-sm">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('suppliers.edit', $supplier) }}"
                                                    class="btn btn-primary btn-sm">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST"
                                                    class="d-inline"
                                                    onsubmit="return confirm('Are you sure you want to delete this supplier?');">
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
                        {{ $suppliers->appends(request()->query())->links() }}
                    </div>
                @else
                    <div class="alert alert-info">
                        No suppliers found. <a href="{{ route('suppliers.create') }}">Create your first supplier</a>.
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
