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
                                    <a href="{{ route('purchase-orders.show', $po) }}" class="btn btn-info btn-sm"
                                        title="View">
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

                                        <form action="{{ route('purchase-orders.destroy', $po) }}" method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm('Are you sure you want to delete this purchase order?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @elseif($po->status == 'ordered')
                                        <a href="{{ route('purchase-orders.receive-form', $po) }}"
                                            class="btn btn-success btn-sm" title="Receive">
                                            <i class="fas fa-truck-loading"></i>
                                        </a>

                                        <form action="{{ route('purchase-orders.cancel', $po) }}" method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm('Are you sure you want to cancel this purchase order?');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-danger btn-sm" title="Cancel">
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
            No purchase orders found. <a href="{{ route('purchase-orders.create') }}">Create your first purchase
                order</a>.
        </div>
    @endif
</div>
