@extends('layouts.app')

@section('title', 'Orders')

@section('actions')
    <a href="{{ route('orders.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Create Order
    </a>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Orders List</h5>
        </div>
        <div class="card-body">
            <div class="mb-4">
                <form action="{{ route('orders.index') }}" method="GET" class="mb-3">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <div class="input-group">
                                <input type="text" class="form-control live-search-input" placeholder="Search orders..."
                                    name="search" value="{{ request('search') }}">
                                <span class="input-group-text loading-indicator d-none">
                                    <i class="fas fa-spinner fa-spin"></i>
                                </span>
                                <button class="btn btn-outline-secondary" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" name="status">
                                <option value="">All Statuses</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending
                                </option>
                                <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>
                                    Processing</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed
                                </option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled
                                </option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-text">From</span>
                                <input type="date" class="form-control" name="date_from"
                                    value="{{ request('date_from') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-text">To</span>
                                <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}">
                            </div>
                        </div>
                        <div class="col-md-1">
                            <div class="d-flex">
                                <a href="{{ route('orders.index') }}" class="btn btn-secondary">Reset</a>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-2">
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
                        <div class="col-md-3">
                            <select class="form-select" name="sort_by">
                                <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Sort
                                    by Date</option>
                                <option value="grand_total" {{ request('sort_by') == 'grand_total' ? 'selected' : '' }}>
                                    Sort by Amount</option>
                                <option value="customer_name"
                                    {{ request('sort_by') == 'customer_name' ? 'selected' : '' }}>Sort by Customer</option>
                                <option value="order_number" {{ request('sort_by') == 'order_number' ? 'selected' : '' }}>
                                    Sort by Order #</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" name="sort_direction">
                                <option value="desc" {{ request('sort_direction') == 'desc' ? 'selected' : '' }}>
                                    Descending</option>
                                <option value="asc" {{ request('sort_direction') == 'asc' ? 'selected' : '' }}>Ascending
                                </option>
                            </select>
                        </div>
                        <div class="col-md-1">
                            <button type="submit" class="btn btn-primary w-100">Filter</button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="mb-3">
                <a href="{{ route('export.orders') }}" class="btn btn-success">Export Orders</a>
            </div>
            <div class="content-area">
                @if ($orders->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Customer</th>
                                    <th>Date</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Created By</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orders as $order)
                                    <tr>
                                        <td>{{ $order->order_number }}</td>
                                        <td>{{ $order->customer_name }}</td>
                                        <td>{{ $order->created_at->format('M d, Y') }}</td>
                                        <td>${{ number_format($order->grand_total, 2) }}</td>
                                        <td>
                                            @if ($order->status == 'pending')
                                                <span class="badge bg-warning">Pending</span>
                                            @elseif($order->status == 'processing')
                                                <span class="badge bg-info">Processing</span>
                                            @elseif($order->status == 'completed')
                                                <span class="badge bg-success">Completed</span>
                                            @elseif($order->status == 'cancelled')
                                                <span class="badge bg-danger">Cancelled</span>
                                            @endif
                                        </td>
                                        <td>{{ $order->user->name }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('orders.show', $order) }}" class="btn btn-info btn-sm"
                                                    title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>

                                                @if ($order->status == 'pending')
                                                    <a href="{{ route('orders.edit', $order) }}"
                                                        class="btn btn-primary btn-sm" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>

                                                    <form action="{{ route('orders.process', $order) }}" method="POST"
                                                        class="d-inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-success btn-sm"
                                                            title="Process">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </form>

                                                    <form action="{{ route('orders.destroy', $order) }}" method="POST"
                                                        class="d-inline"
                                                        onsubmit="return confirm('Are you sure you want to delete this order?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm"
                                                            title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @elseif($order->status == 'processing')
                                                    <form action="{{ route('orders.complete', $order) }}" method="POST"
                                                        class="d-inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-success btn-sm"
                                                            title="Complete">
                                                            <i class="fas fa-check-double"></i>
                                                        </button>
                                                    </form>

                                                    <form action="{{ route('orders.cancel', $order) }}" method="POST"
                                                        class="d-inline"
                                                        onsubmit="return confirm('Are you sure you want to cancel this order?');">
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
                        {{ $orders->appends(request()->query())->links() }}
                    </div>
                @else
                    <div class="alert alert-info">
                        No orders found. <a href="{{ route('orders.create') }}">Create your first order</a>.
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
