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
            <div class="mb-3">
                <a href="{{ route('export.suppliers') }}" class="btn btn-success">Export Suppliers</a>
            </div>
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
                                            <a href="{{ route('suppliers.show', $supplier) }}" class="btn btn-info btn-sm">
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
            @else
                <div class="alert alert-info">
                    No suppliers found. <a href="{{ route('suppliers.create') }}">Create your first supplier</a>.
                </div>
            @endif
        </div>
    </div>
@endsection
