@extends('layouts.app')

@section('title', 'User Details')

@section('actions')
    <a href="{{ route('users.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Users
    </a>
    <a href="{{ route('users.edit', $user) }}" class="btn btn-primary">
        <i class="fas fa-edit"></i> Edit
    </a>
    @if(auth()->id() !== $user->id)
        <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this user?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">
                <i class="fas fa-trash"></i> Delete
            </button>
        </form>
    @endif
@endsection

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title">User Information</h5>
            </div>
            <div class="card-body">
                <table class="table">
                    <tr>
                        <th style="width: 150px;">ID</th>
                        <td>{{ $user->id }}</td>
                    </tr>
                    <tr>
                        <th>Name</th>
                        <td>{{ $user->name }}</td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td>{{ $user->email }}</td>
                    </tr>
                    <tr>
                        <th>Role</th>
                        <td>
                            @foreach($user->roles as $role)
                                <span class="badge bg-info">{{ $role->display_name }}</span>
                            @endforeach
                        </td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            @if($user->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Inactive</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Admin</th>
                        <td>
                            @if($user->is_admin)
                                <span class="badge bg-warning">Yes</span>
                            @else
                                <span class="badge bg-secondary">No</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Created At</th>
                        <td>{{ $user->created_at->format('M d, Y H:i') }}</td>
                    </tr>
                    <tr>
                        <th>Updated At</th>
                        <td>{{ $user->updated_at->format('M d, Y H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title">Role Permissions</h5>
            </div>
            <div class="card-body">
                @if($user->roles->isNotEmpty())
                    @foreach($user->roles as $role)
                        <h6>{{ $role->display_name }} Permissions:</h6>
                        @if($role->permissions->isNotEmpty())
                            <div class="mb-3">
                                @foreach($role->permissions as $permission)
                                    <span class="badge bg-primary m-1">{{ $permission->display_name }}</span>
                                @endforeach
                            </div>
                        @else
                            <div class="alert alert-info">
                                No permissions assigned to this role.
                            </div>
                        @endif
                    @endforeach
                @else
                    <div class="alert alert-warning">
                        No roles assigned to this user.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title">Recent Orders</h5>
            </div>
            <div class="card-body">
                @if($user->orders->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($user->orders->take(5) as $order)
                                    <tr>
                                        <td>
                                            <a href="{{ route('orders.show', $order) }}">
                                                {{ $order->order_number }}
                                            </a>
                                        </td>
                                        <td>{{ $order->created_at->format('M d, Y') }}</td>
                                        <td>${{ number_format($order->grand_total, 2) }}</td>
                                        <td>
                                            <span class="badge bg-{{ $order->status == 'completed' ? 'success' : ($order->status == 'pending' ? 'warning' : 'info') }}">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info">
                        No orders found for this user.
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title">Recent Purchase Orders</h5>
            </div>
            <div class="card-body">
                @if($user->purchaseOrders->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>PO #</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($user->purchaseOrders->take(5) as $po)
                                    <tr>
                                        <td>
                                            <a href="{{ route('purchase-orders.show', $po) }}">
                                                {{ $po->po_number }}
                                            </a>
                                        </td>
                                        <td>{{ $po->order_date->format('M d, Y') }}</td>
                                        <td>${{ number_format($po->total_amount, 2) }}</td>
                                        <td>
                                            <span class="badge bg-{{ $po->status == 'received' ? 'success' : ($po->status == 'pending' ? 'warning' : 'info') }}">
                                                {{ ucfirst($po->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info">
                        No purchase orders found for this user.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
