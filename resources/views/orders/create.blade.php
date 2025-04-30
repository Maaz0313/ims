@extends('layouts.app')

@section('title', 'Create Order')

@section('actions')
    <a href="{{ route('orders.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Orders
    </a>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Create New Order</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('orders.store') }}" method="POST" id="order-form">
                @csrf

                <div class="row">
                    <div class="col-md-6">
                        <h6 class="mb-3">Customer Information</h6>

                        <div class="mb-3">
                            <label for="customer_name" class="form-label">Customer Name <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('customer_name') is-invalid @enderror"
                                id="customer_name" name="customer_name" value="{{ old('customer_name') }}" required>
                            @error('customer_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="customer_email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('customer_email') is-invalid @enderror"
                                id="customer_email" name="customer_email" value="{{ old('customer_email') }}">
                            @error('customer_email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="customer_phone" class="form-label">Phone</label>
                            <input type="text" class="form-control @error('customer_phone') is-invalid @enderror"
                                id="customer_phone" name="customer_phone" value="{{ old('customer_phone') }}">
                            @error('customer_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <h6 class="mb-3">Order Details</h6>

                        <div class="mb-3">
                            <label for="shipping_address" class="form-label">Shipping Address</label>
                            <textarea class="form-control @error('shipping_address') is-invalid @enderror" id="shipping_address"
                                name="shipping_address" rows="3">{{ old('shipping_address') }}</textarea>
                            @error('shipping_address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Order Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="2">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <h5 class="mt-4 mb-3">Order Items</h5>

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
                                                    <option value="{{ $product->id }}" data-price="{{ $product->price }}"
                                                        data-max="{{ $product->inventory->quantity }}">
                                                        {{ $product->name }} ({{ $product->sku }}) -
                                                        {{ $product->inventory->quantity }} in stock
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
                                            <small class="text-muted" id="max-quantity"></small>
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
                                                        data-price="{{ $product->price }}"
                                                        data-max="{{ $product->inventory->quantity }}">
                                                        {{ $product->name }} ({{ $product->sku }}) -
                                                        {{ $product->inventory->quantity }} in stock
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
                                <td colspan="3" class="text-end"><strong>Subtotal:</strong></td>
                                <td>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="text" id="subtotal" class="form-control" value="0.00"
                                            readonly>
                                    </div>
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-end">
                                    <div class="input-group" style="max-width: 150px; float: right;">
                                        <input type="number" id="tax-rate" name="tax_rate" class="form-control"
                                            min="0" max="100" step="0.01"
                                            value="{{ old('tax_rate', 0) }}">
                                        <span class="input-group-text">%</span>
                                    </div>
                                    <strong class="me-2">Tax:</strong>
                                </td>
                                <td>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="text" id="tax-amount" class="form-control" value="0.00"
                                            readonly>
                                    </div>
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-end">
                                    <div class="input-group" style="max-width: 150px; float: right;">
                                        <span class="input-group-text">$</span>
                                        <input type="number" id="discount-amount" name="discount_amount"
                                            class="form-control" min="0" step="0.01"
                                            value="{{ old('discount_amount', 0) }}">
                                    </div>
                                    <strong class="me-2">Discount:</strong>
                                </td>
                                <td>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="text" id="discount-display" class="form-control" value="0.00"
                                            readonly>
                                    </div>
                                </td>
                                <td></td>
                            </tr>
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
                        <i class="fas fa-save"></i> Create Order
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
            const maxQuantityDisplay = document.getElementById('max-quantity');
            const addItemBtn = document.getElementById('add-item-btn');
            const subtotalInput = document.getElementById('subtotal');
            const taxRateInput = document.getElementById('tax-rate');
            const taxAmountInput = document.getElementById('tax-amount');
            const discountAmountInput = document.getElementById('discount-amount');
            const discountDisplayInput = document.getElementById('discount-display');
            const grandTotalInput = document.getElementById('grand-total');

            // Initialize
            calculateTotals();

            // Event Listeners
            addProductSelect.addEventListener('change', function() {
                if (this.value) {
                    const selectedOption = this.options[this.selectedIndex];
                    const price = selectedOption.getAttribute('data-price');
                    const maxQuantity = selectedOption.getAttribute('data-max');

                    addPriceInput.value = price;
                    addQuantityInput.max = maxQuantity;
                    addQuantityInput.value = Math.min(1, maxQuantity);
                    maxQuantityDisplay.textContent = `Max: ${maxQuantity}`;
                } else {
                    addPriceInput.value = '0.00';
                    addQuantityInput.max = '';
                    addQuantityInput.value = '1';
                    maxQuantityDisplay.textContent = '';
                }
            });

            addItemBtn.addEventListener('click', function() {
                const productId = addProductSelect.value;
                const productText = addProductSelect.options[addProductSelect.selectedIndex].text;
                const price = parseFloat(addPriceInput.value);
                const quantity = parseInt(addQuantityInput.value);
                const maxQuantity = parseInt(addProductSelect.options[addProductSelect.selectedIndex]
                    .getAttribute('data-max'));

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

                if (quantity > maxQuantity) {
                    alert(`Maximum available quantity is ${maxQuantity}`);
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
                        const currentQuantity = parseInt(quantityInput.value);
                        const newQuantity = currentQuantity + quantity;

                        if (newQuantity > maxQuantity) {
                            alert(
                                `Cannot add ${quantity} more units. Maximum available quantity is ${maxQuantity}. You already have ${currentQuantity} in your order.`);
                            return;
                        }

                        // Update quantity and recalculate total
                        quantityInput.value = newQuantity;
                        itemTotalInput.value = (parseFloat(unitPriceInput.value) * newQuantity).toFixed(2);

                        calculateTotals();
                        return;
                    }
                }

                // Add new row
                addProductRow(productId, productText, price, quantity, maxQuantity);

                // Reset form
                addProductSelect.value = '';
                addPriceInput.value = '0.00';
                addQuantityInput.value = '1';
                maxQuantityDisplay.textContent = '';

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

                    calculateTotals();
                }
            });

            // Delegate event for quantity and price changes
            productsTable.addEventListener('input', function(e) {
                if (e.target.classList.contains('quantity') || e.target.classList.contains('unit-price')) {
                    const row = e.target.closest('tr');
                    const unitPrice = parseFloat(row.querySelector('.unit-price').value) || 0;
                    const quantity = parseInt(row.querySelector('.quantity').value) || 0;
                    const itemTotal = row.querySelector('.item-total');

                    // Check max quantity
                    if (e.target.classList.contains('quantity')) {
                        const productSelect = row.querySelector('.product-select');
                        const selectedOption = productSelect.options[productSelect.selectedIndex];
                        const maxQuantity = parseInt(selectedOption.getAttribute('data-max'));

                        if (quantity > maxQuantity) {
                            alert(`Maximum available quantity is ${maxQuantity}`);
                            e.target.value = maxQuantity;
                            itemTotal.value = (unitPrice * maxQuantity).toFixed(2);
                            calculateTotals();
                            return;
                        }
                    }

                    itemTotal.value = (unitPrice * quantity).toFixed(2);
                    calculateTotals();
                }
            });

            // Tax and discount changes
            taxRateInput.addEventListener('input', calculateTotals);
            discountAmountInput.addEventListener('input', calculateTotals);

            // Form submission validation
            document.getElementById('order-form').addEventListener('submit', function(e) {
                const productRows = document.querySelectorAll('.product-row');
                if (productRows.length === 0) {
                    e.preventDefault();
                    alert('Please add at least one product to the order');
                }
            });

            // Functions
            function addProductRow(productId, productText, price, quantity, maxQuantity) {
                const tbody = productsTable.querySelector('tbody');
                const total = price * quantity;

                const row = document.createElement('tr');
                row.className = 'product-row';
                row.innerHTML = `
                <td>
                    <select name="products[${rowIndex}][product_id]" class="form-select product-select" required>
                        <option value="${productId}" data-max="${maxQuantity}" selected>${productText}</option>
                    </select>
                </td>
                <td>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" name="products[${rowIndex}][unit_price]" class="form-control unit-price" step="0.01" min="0" value="${price.toFixed(2)}" required>
                    </div>
                </td>
                <td>
                    <input type="number" name="products[${rowIndex}][quantity]" class="form-control quantity" min="1" max="${maxQuantity}" value="${quantity}" required>
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

                calculateTotals();
            }

            function calculateTotals() {
                const itemTotals = document.querySelectorAll('.item-total');
                let subtotal = 0;

                itemTotals.forEach(function(item) {
                    subtotal += parseFloat(item.value) || 0;
                });

                const taxRate = parseFloat(taxRateInput.value) || 0;
                const taxAmount = (subtotal * taxRate) / 100;
                const discountAmount = parseFloat(discountAmountInput.value) || 0;
                const grandTotal = subtotal + taxAmount - discountAmount;

                subtotalInput.value = subtotal.toFixed(2);
                taxAmountInput.value = taxAmount.toFixed(2);
                discountDisplayInput.value = discountAmount.toFixed(2);
                grandTotalInput.value = grandTotal.toFixed(2);
            }
        });
    </script>
@endsection
