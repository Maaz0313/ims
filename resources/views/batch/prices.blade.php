@extends('layouts.app')

@section('title', 'Batch Price Update')

@section('actions')
    <a href="{{ route('products.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Products
    </a>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="card-title">Update Multiple Product Prices</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('batch.prices.process') }}" method="POST">
            @csrf
            
            <div class="mb-3">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Use this form to update prices for multiple products at once.
                </div>
            </div>
            
            <div class="mb-3">
                <div class="row">
                    <div class="col-md-4">
                        <div class="input-group mb-3">
                            <span class="input-group-text">Increase All By %</span>
                            <input type="number" class="form-control" id="increasePercentage" min="0" step="0.1">
                            <button class="btn btn-outline-secondary" type="button" id="applyIncreasePercentage">Apply</button>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group mb-3">
                            <span class="input-group-text">Decrease All By %</span>
                            <input type="number" class="form-control" id="decreasePercentage" min="0" step="0.1">
                            <button class="btn btn-outline-secondary" type="button" id="applyDecreasePercentage">Apply</button>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group mb-3">
                            <span class="input-group-text">Set Margin %</span>
                            <input type="number" class="form-control" id="marginPercentage" min="0" step="0.1">
                            <button class="btn btn-outline-secondary" type="button" id="applyMarginPercentage">Apply</button>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>SKU</th>
                            <th>Current Price ($)</th>
                            <th>New Price ($)</th>
                            <th>Cost Price ($)</th>
                            <th>Margin (%)</th>
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
                                <td>{{ number_format($product->price, 2) }}</td>
                                <td>
                                    <input type="number" class="form-control price-input" name="products[{{ $index }}][price]" 
                                        value="{{ old('products.'.$index.'.price', $product->price) }}" min="0" step="0.01">
                                </td>
                                <td>
                                    <input type="number" class="form-control cost-input" name="products[{{ $index }}][cost_price]" 
                                        value="{{ old('products.'.$index.'.cost_price', $product->cost_price) }}" min="0" step="0.01">
                                </td>
                                <td class="margin-cell">
                                    @if($product->cost_price && $product->cost_price > 0)
                                        {{ number_format((($product->price - $product->cost_price) / $product->price) * 100, 2) }}%
                                    @else
                                        N/A
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Prices
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Function to calculate and update margin
        function updateMargin(row) {
            const price = parseFloat($(row).find('.price-input').val()) || 0;
            const cost = parseFloat($(row).find('.cost-input').val()) || 0;
            
            if (price > 0 && cost > 0) {
                const margin = ((price - cost) / price) * 100;
                $(row).find('.margin-cell').text(margin.toFixed(2) + '%');
            } else {
                $(row).find('.margin-cell').text('N/A');
            }
        }
        
        // Update margin when price or cost changes
        $('.price-input, .cost-input').on('input', function() {
            updateMargin($(this).closest('tr'));
        });
        
        // Apply percentage increase to all prices
        $('#applyIncreasePercentage').click(function() {
            const percentage = parseFloat($('#increasePercentage').val()) || 0;
            
            if (percentage > 0) {
                $('.price-input').each(function() {
                    const currentPrice = parseFloat($(this).val()) || 0;
                    const newPrice = currentPrice * (1 + (percentage / 100));
                    $(this).val(newPrice.toFixed(2));
                    updateMargin($(this).closest('tr'));
                });
            }
        });
        
        // Apply percentage decrease to all prices
        $('#applyDecreasePercentage').click(function() {
            const percentage = parseFloat($('#decreasePercentage').val()) || 0;
            
            if (percentage > 0) {
                $('.price-input').each(function() {
                    const currentPrice = parseFloat($(this).val()) || 0;
                    const newPrice = currentPrice * (1 - (percentage / 100));
                    $(this).val(newPrice.toFixed(2));
                    updateMargin($(this).closest('tr'));
                });
            }
        });
        
        // Apply margin percentage to calculate prices based on cost
        $('#applyMarginPercentage').click(function() {
            const targetMargin = parseFloat($('#marginPercentage').val()) || 0;
            
            if (targetMargin > 0) {
                $('tbody tr').each(function() {
                    const cost = parseFloat($(this).find('.cost-input').val()) || 0;
                    
                    if (cost > 0) {
                        // Formula: price = cost / (1 - margin/100)
                        const newPrice = cost / (1 - (targetMargin / 100));
                        $(this).find('.price-input').val(newPrice.toFixed(2));
                        updateMargin($(this));
                    }
                });
            }
        });
    });
</script>
@endsection
