@extends('layouts.app')

@section('title', 'Purchase Orders')

@section('actions')
    <a href="{{ route('purchase-orders.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Create Purchase Order
    </a>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Purchase Orders List</h5>
        </div>
        <div class="card-body">
            <div class="mb-4">
                <form action="{{ route('purchase-orders.index') }}" method="GET" class="mb-3">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <div class="input-group">
                                <input type="text" class="form-control live-search-input" placeholder="Search POs..."
                                    name="search" value="{{ request('search') }}">
                                <span class="input-group-text loading-indicator d-none">
                                    <i class="fas fa-spinner fa-spin"></i>
                                </span>
                                <button class="btn btn-outline-secondary" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-3">
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
                        <div class="col-md-3">
                            <select class="form-select" name="status">
                                <option value="">All Statuses</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending
                                </option>
                                <option value="ordered" {{ request('status') == 'ordered' ? 'selected' : '' }}>Ordered
                                </option>
                                <option value="received" {{ request('status') == 'received' ? 'selected' : '' }}>Received
                                </option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled
                                </option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex">
                                <button type="submit" class="btn btn-primary me-2">Filter</button>
                                <a href="{{ route('purchase-orders.index') }}" class="btn btn-secondary">Reset</a>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-text">Order From</span>
                                <input type="date" class="form-control" name="date_from"
                                    value="{{ request('date_from') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-text">Order To</span>
                                <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" placeholder="Min Amount" name="min_amount"
                                    value="{{ request('min_amount') }}" min="0" step="0.01">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" placeholder="Max Amount" name="max_amount"
                                    value="{{ request('max_amount') }}" min="0" step="0.01">
                            </div>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-text">Delivery From</span>
                                <input type="date" class="form-control" name="delivery_from"
                                    value="{{ request('delivery_from') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-text">Delivery To</span>
                                <input type="date" class="form-control" name="delivery_to"
                                    value="{{ request('delivery_to') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" name="sort_by">
                                <option value="order_date" {{ request('sort_by') == 'order_date' ? 'selected' : '' }}>Sort
                                    by Order Date</option>
                                <option value="expected_delivery_date"
                                    {{ request('sort_by') == 'expected_delivery_date' ? 'selected' : '' }}>Sort by Delivery
                                    Date</option>
                                <option value="total_amount" {{ request('sort_by') == 'total_amount' ? 'selected' : '' }}>
                                    Sort by Amount</option>
                                <option value="po_number" {{ request('sort_by') == 'po_number' ? 'selected' : '' }}>Sort by
                                    PO #</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" name="sort_direction">
                                <option value="desc" {{ request('sort_direction') == 'desc' ? 'selected' : '' }}>
                                    Descending</option>
                                <option value="asc" {{ request('sort_direction') == 'asc' ? 'selected' : '' }}>
                                    Ascending</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>

            <div class="mb-3">
                <a href="{{ route('export.purchase-orders') }}" class="btn btn-success">Export Purchase Orders</a>
            </div>
            <div class="content-area">
                @if ($purchaseOrders->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>PO #</th>
                                    <th>Supplier</th>
                                    <th>Date</th>
                                    <th>Expected Delivery</th>
                                    <th>Total Amount</th>
                                    <th>Status</th>
                                    <th>Created By</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($purchaseOrders as $po)
                                    <tr>
                                        <td>{{ $po->po_number }}</td>
                                        <td>{{ $po->supplier->name }}</td>
                                        <td>{{ $po->order_date->format('M d, Y') }}</td>
                                        <td>{{ $po->expected_delivery_date ? $po->expected_delivery_date->format('M d, Y') : 'N/A' }}
                                        </td>
                                        <td>${{ number_format($po->total_amount, 2) }}</td>
                                        <td>
                                            @if ($po->status == 'pending')
                                                <span class="badge bg-warning">Pending</span>
                                            @elseif($po->status == 'ordered')
                                                <span class="badge bg-info">Ordered</span>
                                            @elseif($po->status == 'received')
                                                <span class="badge bg-success">Received</span>
                                            @elseif($po->status == 'cancelled')
                                                <span class="badge bg-danger">Cancelled</span>
                                            @endif
                                        </td>
                                        <td>{{ $po->user->name }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('purchase-orders.show', $po) }}"
                                                    class="btn btn-info btn-sm" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>

                                                @if ($po->status == 'pending')
                                                    <a href="{{ route('purchase-orders.edit', $po) }}"
                                                        class="btn btn-primary btn-sm" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>

                                                    <form action="{{ route('purchase-orders.mark-as-ordered', $po) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-success btn-sm"
                                                            title="Mark as Ordered">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </form>

                                                    <form action="{{ route('purchase-orders.destroy', $po) }}"
                                                        method="POST" class="d-inline"
                                                        onsubmit="return confirm('Are you sure you want to delete this purchase order?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm"
                                                            title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @elseif($po->status == 'ordered')
                                                    <a href="{{ route('purchase-orders.receive-form', $po) }}"
                                                        class="btn btn-success btn-sm" title="Receive">
                                                        <i class="fas fa-truck-loading"></i>
                                                    </a>

                                                    <form action="{{ route('purchase-orders.cancel', $po) }}"
                                                        method="POST" class="d-inline"
                                                        onsubmit="return confirm('Are you sure you want to cancel this purchase order?');">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-danger btn-sm"
                                                            title="Cancel">
                                                            <i class="fas fa-ban"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $purchaseOrders->appends(request()->query())->links() }}
                    </div>
                @else
                    <div class="alert alert-info">
                        No purchase orders found. <a href="{{ route('purchase-orders.create') }}">Create your first
                            purchase
                            order</a>.
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
