@extends('layouts.app')

@section('title', 'Create Purchase Order')

@section('actions')
    <a href="{{ route('purchase-orders.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Purchase Orders
    </a>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Create New Purchase Order</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('purchase-orders.store') }}" method="POST" id="purchase-order-form">
                @csrf

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="supplier_id" class="form-label">Supplier <span class="text-danger">*</span></label>
                            <select class="form-select @error('supplier_id') is-invalid @enderror" id="supplier_id"
                                name="supplier_id" required>
                                <option value="">Select Supplier</option>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}"
                                        {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('supplier_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="order_date" class="form-label">Order Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('order_date') is-invalid @enderror"
                                id="order_date" name="order_date" value="{{ old('order_date', date('Y-m-d')) }}" required>
                            @error('order_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="expected_delivery_date" class="form-label">Expected Delivery Date</label>
                            <input type="date" class="form-control @error('expected_delivery_date') is-invalid @enderror"
                                id="expected_delivery_date" name="expected_delivery_date"
                                value="{{ old('expected_delivery_date') }}">
                            @error('expected_delivery_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="notes" class="form-label">Notes</label>
                    <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="2">{{ old('notes') }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="card-title mb-0">Add Product</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-5">
                                        <div class="mb-3">
                                            <label for="add-product" class="form-label">Product</label>
                                            <select class="form-select" id="add-product">
                                                <option value="">Select Product</option>
                                                @foreach ($products as $product)
                                                    <option value="{{ $product->id }}"
                                                        data-price="{{ $product->cost_price ?: $product->price }}">
                                                        {{ $product->name }} ({{ $product->sku }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="add-price" class="form-label">Unit Price</label>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="number" class="form-control" id="add-price" step="0.01"
                                                    min="0" value="0.00">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="mb-3">
                                            <label for="add-quantity" class="form-label">Quantity</label>
                                            <input type="number" class="form-control" id="add-quantity" min="1"
                                                value="1">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="mb-3">
                                            <label class="form-label">&nbsp;</label>
                                            <button type="button" class="btn btn-success w-100" id="add-item-btn">
                                                <i class="fas fa-plus"></i> Add
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <h5 class="mt-4 mb-3">Order Items</h5>

                <div class="table-responsive mb-3">
                    <table class="table table-bordered" id="products-table">
                        <thead>
                            <tr>
                                <th style="width: 40%">Product</th>
                                <th style="width: 20%">Unit Price</th>
                                <th style="width: 15%">Quantity</th>
                                <th style="width: 20%">Total</th>
                                <th style="width: 5%">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr id="empty-row" class="{{ old('products') ? 'd-none' : '' }}">
                                <td colspan="5" class="text-center">No products added. Use the form above to add
                                    products.</td>
                            </tr>

                            @if (old('products'))
                                @foreach (old('products') as $index => $item)
                                    <tr class="product-row">
                                        <td>
                                            <select name="products[{{ $index }}][product_id]"
                                                class="form-select product-select" required>
                                                <option value="">Select Product</option>
                                                @foreach ($products as $product)
                                                    <option value="{{ $product->id }}"
                                                        {{ $item['product_id'] == $product->id ? 'selected' : '' }}
                                                        data-price="{{ $product->cost_price ?: $product->price }}">
                                                        {{ $product->name }} ({{ $product->sku }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="number" name="products[{{ $index }}][unit_price]"
                                                    class="form-control unit-price" step="0.01" min="0"
                                                    value="{{ $item['unit_price'] }}" required>
                                            </div>
                                        </td>
                                        <td>
                                            <input type="number" name="products[{{ $index }}][quantity]"
                                                class="form-control quantity" min="1"
                                                value="{{ $item['quantity'] }}" required>
                                        </td>
                                        <td>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="text" class="form-control item-total"
                                                    value="{{ number_format($item['unit_price'] * $item['quantity'], 2) }}"
                                                    readonly>
                                            </div>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-danger btn-sm remove-row">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-end"><strong>Grand Total:</strong></td>
                                <td>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="text" id="grand-total" class="form-control" value="0.00"
                                            readonly>
                                    </div>
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>



                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="submit" class="btn btn-primary" id="submit-btn">
                        <i class="fas fa-save"></i> Create Purchase Order
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Variables
            let rowIndex = {{ old('products') ? count(old('products')) : 0 }};
            const productsTable = document.getElementById('products-table');
            const emptyRow = document.getElementById('empty-row');
            const addProductSelect = document.getElementById('add-product');
            const addPriceInput = document.getElementById('add-price');
            const addQuantityInput = document.getElementById('add-quantity');
            const addItemBtn = document.getElementById('add-item-btn');
            const grandTotalInput = document.getElementById('grand-total');

            // Initialize
            calculateGrandTotal();

            // Event Listeners
            addProductSelect.addEventListener('change', function() {
                if (this.value) {
                    const selectedOption = this.options[this.selectedIndex];
                    const price = selectedOption.getAttribute('data-price');
                    addPriceInput.value = price;
                } else {
                    addPriceInput.value = '0.00';
                }
            });

            addItemBtn.addEventListener('click', function() {
                const productId = addProductSelect.value;
                const productText = addProductSelect.options[addProductSelect.selectedIndex].text;
                const price = parseFloat(addPriceInput.value);
                const quantity = parseInt(addQuantityInput.value);

                if (!productId) {
                    alert('Please select a product');
                    return;
                }

                if (isNaN(price) || price <= 0) {
                    alert('Please enter a valid price');
                    return;
                }

                if (isNaN(quantity) || quantity <= 0) {
                    alert('Please enter a valid quantity');
                    return;
                }

                // Check if product already exists in the table
                const existingRows = document.querySelectorAll('.product-row');
                for (let i = 0; i < existingRows.length; i++) {
                    const row = existingRows[i];
                    const productSelect = row.querySelector('.product-select');

                    if (productSelect && productSelect.value === productId) {
                        const quantityInput = row.querySelector('.quantity');
                        const unitPriceInput = row.querySelector('.unit-price');
                        const itemTotalInput = row.querySelector('.item-total');

                        // Update quantity and recalculate total
                        quantityInput.value = parseInt(quantityInput.value) + quantity;
                        itemTotalInput.value = (parseFloat(unitPriceInput.value) * parseInt(quantityInput
                            .value)).toFixed(2);

                        calculateGrandTotal();
                        return;
                    }
                }

                // Add new row
                addProductRow(productId, productText, price, quantity);

                // Reset form
                addProductSelect.value = '';
                addPriceInput.value = '0.00';
                addQuantityInput.value = '1';

                // Hide empty row if visible
                if (!emptyRow.classList.contains('d-none')) {
                    emptyRow.classList.add('d-none');
                }
            });

            // Delegate event for remove buttons
            productsTable.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-row') || e.target.closest('.remove-row')) {
                    const row = e.target.closest('tr');
                    row.remove();

                    // Show empty row if no products
                    const productRows = document.querySelectorAll('.product-row');
                    if (productRows.length === 0) {
                        emptyRow.classList.remove('d-none');
                    }

                    calculateGrandTotal();
                }
            });

            // Delegate event for quantity and price changes
            productsTable.addEventListener('input', function(e) {
                if (e.target.classList.contains('quantity') || e.target.classList.contains('unit-price')) {
                    const row = e.target.closest('tr');
                    const unitPrice = parseFloat(row.querySelector('.unit-price').value) || 0;
                    const quantity = parseInt(row.querySelector('.quantity').value) || 0;
                    const itemTotal = row.querySelector('.item-total');

                    itemTotal.value = (unitPrice * quantity).toFixed(2);
                    calculateGrandTotal();
                }
            });

            // Form submission validation
            document.getElementById('purchase-order-form').addEventListener('submit', function(e) {
                const productRows = document.querySelectorAll('.product-row');
                if (productRows.length === 0) {
                    e.preventDefault();
                    alert('Please add at least one product to the purchase order');
                }
            });

            // Functions
            function addProductRow(productId, productText, price, quantity) {
                const tbody = productsTable.querySelector('tbody');
                const total = price * quantity;

                const row = document.createElement('tr');
                row.className = 'product-row';
                row.innerHTML = `
                <td>
                    <select name="products[${rowIndex}][product_id]" class="form-select product-select" required>
                        <option value="${productId}" selected>${productText}</option>
                    </select>
                </td>
                <td>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" name="products[${rowIndex}][unit_price]" class="form-control unit-price" step="0.01" min="0" value="${price.toFixed(2)}" required>
                    </div>
                </td>
                <td>
                    <input type="number" name="products[${rowIndex}][quantity]" class="form-control quantity" min="1" value="${quantity}" required>
                </td>
                <td>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="text" class="form-control item-total" value="${total.toFixed(2)}" readonly>
                    </div>
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm remove-row">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            `;

                tbody.appendChild(row);
                rowIndex++;

                calculateGrandTotal();
            }

            function calculateGrandTotal() {
                const itemTotals = document.querySelectorAll('.item-total');
                let grandTotal = 0;

                itemTotals.forEach(function(item) {
                    grandTotal += parseFloat(item.value) || 0;
                });

                grandTotalInput.value = grandTotal.toFixed(2);
            }
        });
    </script>
@endsection
