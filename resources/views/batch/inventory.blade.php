@extends('layouts.app')

@section('title', 'Batch Inventory Update')

@section('actions')
    <a href="{{ route('inventory.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Inventory
    </a>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="card-title">Update Multiple Inventory Items</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('batch.inventory.process') }}" method="POST">
            @csrf
            
            <div class="mb-3">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Use this form to update inventory quantities, reorder levels, and locations for multiple products at once.
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>SKU</th>
                            <th>Current Quantity</th>
                            <th>New Quantity</th>
                            <th>Reorder Level</th>
                            <th>Location</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $index => $product)
                            <tr>
                                <td>
                                    {{ $product->name }}
                                    <input type="hidden" name="products[{{ $index }}][id]" value="{{ $product->id }}">
                                </td>
                                <td>{{ $product->sku }}</td>
                                <td>{{ $product->inventory->quantity ?? 0 }}</td>
                                <td>
                                    <input type="number" class="form-control" name="products[{{ $index }}][quantity]" 
                                        value="{{ old('products.'.$index.'.quantity', $product->inventory->quantity ?? 0) }}" min="0">
                                </td>
                                <td>
                                    <input type="number" class="form-control" name="products[{{ $index }}][reorder_level]" 
                                        value="{{ old('products.'.$index.'.reorder_level', $product->inventory->reorder_level ?? 5) }}" min="0">
                                </td>
                                <td>
                                    <input type="text" class="form-control" name="products[{{ $index }}][location]" 
                                        value="{{ old('products.'.$index.'.location', $product->inventory->location ?? '') }}">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Inventory
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Add a button to filter the table to only show products with low stock
        $('<button type="button" class="btn btn-warning mb-3 me-2" id="showLowStock"><i class="fas fa-filter"></i> Show Low Stock Only</button>').insertBefore('.table-responsive');
        
        // Add a button to reset the filter
        $('<button type="button" class="btn btn-secondary mb-3" id="resetFilter"><i class="fas fa-undo"></i> Show All</button>').insertBefore('.table-responsive');
        
        // Add a button to set all quantities to a specific value
        $('<div class="mb-3 mt-3"><div class="input-group" style="max-width: 300px;"><input type="number" class="form-control" id="bulkQuantity" placeholder="Set all quantities to..." min="0"><div class="input-group-append"><button class="btn btn-outline-secondary" type="button" id="applyBulkQuantity">Apply</button></div></div></div>').insertBefore('.table-responsive');
        
        // Filter to show only low stock items
        $('#showLowStock').click(function() {
            $('tbody tr').each(function() {
                const currentQty = parseInt($(this).find('td:eq(2)').text());
                const reorderLevel = parseInt($(this).find('input[name$="[reorder_level]"]').val());
                
                if (currentQty > reorderLevel) {
                    $(this).hide();
                } else {
                    $(this).show();
                }
            });
        });
        
        // Reset filter to show all items
        $('#resetFilter').click(function() {
            $('tbody tr').show();
        });
        
        // Apply bulk quantity update
        $('#applyBulkQuantity').click(function() {
            const bulkQty = $('#bulkQuantity').val();
            if (bulkQty !== '') {
                $('input[name$="[quantity]"]').val(bulkQty);
            }
        });
    });
</script>
@endsection
