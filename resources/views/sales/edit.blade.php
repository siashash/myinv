@extends('headerfooter')

@push('styles')
<style>
    .sales-table {
        min-width: 1600px;
    }
    .sales-table th,
    .sales-table td {
        vertical-align: middle;
    }
    .sales-table .col-qty { width: 90px; }
    .sales-table .col-rate,
    .sales-table .col-amount,
    .sales-table .col-gst-amount,
    .sales-table .col-total { width: 100px; }
    .sales-table .col-gst-percent { width: 80px; }
    .remove-x-btn {
        min-width: 36px;
        padding: 0.2rem 0.55rem;
        font-weight: 700;
    }
    .sales-card-wide {
        min-width: 1500px;
    }
</style>
@endpush

@section('content')
@php
    $productRows = $products->map(function ($p) {
        return [
            'id' => (string) $p->id,
            'product_name' => $p->product_name,
            'product_code' => $p->product_code,
            'uom' => $p->uom,
            'sales_uom' => $p->sales_uom,
            'rate_bu' => (float) ($p->sales_price_bu ?? $p->sale_price ?? 0),
            'rate_su' => (float) ($p->sales_price_su ?? $p->sale_price ?? 0),
            'cgst_percent' => (float) ($p->cgst_percent ?? 0),
            'sgst_percent' => (float) ($p->sgst_percent ?? 0),
            'igst_percent' => (float) ($p->igst_percent ?? 0),
        ];
    })->values();

    $customerRows = $customers->map(function ($c) {
        return [
            'id' => (string) $c->customer_id,
            'name' => $c->customer_name,
        ];
    })->values();

    $prefillRows = $sale->details->map(function ($d) {
        return [
            'product_id' => (string) $d->product_id,
            'product_name' => $d->product_name,
            'product_code' => $d->item_code,
            'unit_name' => $d->unit_name ?: 'uom',
            'qty' => (float) $d->qty,
        ];
    })->values();
@endphp

<div class="container-fluid px-2 mt-4">
    @if ($errors->any())
        <div class="alert alert-danger">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <div class="card mb-4 sales-card-wide">
        <div class="card-header">
            <h5 class="mb-0">Edit sales</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('sales.update', $sale) }}" method="POST" id="sales-form">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="sale_date">Date</label>
                        <input type="date" id="sale_date" name="sale_date" class="form-control" value="{{ old('sale_date', $sale->sale_date) }}" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>Invoice Number</label>
                        <input type="text" class="form-control" value="{{ $sale->invoice_no }}" readonly>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="customer_name">Customer Name</label>
                        <input type="text" id="customer_name" name="customer_name" list="customer-name-list" class="form-control" value="{{ old('customer_name', $sale->customer_name) }}" required>
                        <input type="hidden" id="customer_id" name="customer_id" value="{{ old('customer_id', $sale->customer_id) }}">
                        <datalist id="customer-name-list">
                            @foreach ($customers as $customer)
                                <option value="{{ $customer->customer_name }}"></option>
                            @endforeach
                        </datalist>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="sale_mode">Sales Mode</label>
                        <select id="sale_mode" name="sale_mode" class="form-control" required>
                            @foreach (['Cash', 'Credit', 'UPI'] as $mode)
                                <option value="{{ $mode }}" {{ old('sale_mode', $sale->sale_mode ?? 'Cash') === $mode ? 'selected' : '' }}>{{ $mode }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered sales-table">
                        <thead class="table-light">
                            <tr>
                                <th width="280">Product</th>
                                <th width="200">Item Code</th>
                                <th width="120">Uom</th>
                                <th class="col-qty">Qty</th>
                                <th class="col-rate">Rate</th>
                                <th class="col-amount">Amount</th>
                                <th class="col-gst-percent">CGST %</th>
                                <th class="col-gst-percent">SGST %</th>
                                <th class="col-gst-percent">IGST %</th>
                                <th class="col-gst-amount">GST Amount</th>
                                <th class="col-total">Total</th>
                                <th width="70">Action</th>
                            </tr>
                        </thead>
                        <tbody id="sales-lines-body"></tbody>
                    </table>
                </div>

                <button type="button" id="add-line-btn" class="btn btn-success mb-3">Add new</button>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="taxable_total">Taxable Total</label>
                        <input type="number" step="0.01" id="taxable_total" class="form-control" value="0.00" readonly>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="gst_total">GST Total</label>
                        <input type="number" step="0.01" id="gst_total" class="form-control" value="0.00" readonly>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="discount_amount">Discount</label>
                        <input type="number" step="0.01" min="0" id="discount_amount" name="discount_amount" class="form-control" value="{{ old('discount_amount', number_format((float) ($sale->discount_amount ?? 0), 2, '.', '')) }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="grand_total">Final Amount</label>
                        <input type="number" step="0.01" id="grand_total" class="form-control" value="0.00" readonly>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Update sales</button>
                <a href="{{ route('sales.index') }}" class="btn btn-secondary">Back</a>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const products = @json($productRows);
    const customers = @json($customerRows);
    const prefillRows = @json($prefillRows);

    const body = document.getElementById('sales-lines-body');
    const addLineBtn = document.getElementById('add-line-btn');
    const grandTotal = document.getElementById('grand_total');
    const discountAmountInput = document.getElementById('discount_amount');
    const customerName = document.getElementById('customer_name');
    const customerId = document.getElementById('customer_id');

    function resolveCustomerId() {
        const typed = (customerName.value || '').trim().toLowerCase();
        const found = customers.find(function (c) {
            return (c.name || '').toLowerCase() === typed;
        });
        customerId.value = found ? found.id : '';
    }

    function productOptionsByName() {
        let html = '';
        products.forEach(function (p) {
            html += '<option value="' + p.product_name + '"></option>';
        });
        return html;
    }

    function productOptionsByCode() {
        let html = '';
        products.forEach(function (p) {
            html += '<option value="' + (p.product_code || '') + '"></option>';
        });
        return html;
    }

    function recalcTotals() {
        let taxable = 0;
        let gst = 0;
        let total = 0;
        body.querySelectorAll('tr').forEach(function (row) {
            taxable += parseFloat(row.querySelector('.amount').value || 0);
            gst += parseFloat(row.querySelector('.gst-amount').value || 0);
            total += parseFloat(row.querySelector('.line-total').value || 0);
        });
        document.getElementById('taxable_total').value = taxable.toFixed(2);
        document.getElementById('gst_total').value = gst.toFixed(2);
        const discountAmount = parseFloat(discountAmountInput.value || 0);
        const finalAmount = Math.max(0, total - discountAmount);
        grandTotal.value = finalAmount.toFixed(2);
    }

    function recalcRow(row) {
        const qty = parseFloat(row.querySelector('.qty').value || 0);
        const rate = parseFloat(row.querySelector('.rate').value || 0);
        const amount = qty * rate;
        const cgstPercent = parseFloat(row.querySelector('.cgst-percent').value || 0);
        const sgstPercent = parseFloat(row.querySelector('.sgst-percent').value || 0);
        const igstPercent = parseFloat(row.querySelector('.igst-percent').value || 0);
        const gstAmount = (amount * cgstPercent / 100) + (amount * sgstPercent / 100) + (amount * igstPercent / 100);
        const total = amount + gstAmount;
        row.querySelector('.amount').value = amount.toFixed(2);
        row.querySelector('.gst-amount').value = gstAmount.toFixed(2);
        row.querySelector('.line-total').value = total.toFixed(2);
        recalcTotals();
    }

    function syncProduct(row, source) {
        const productInput = row.querySelector('.product-name-input');
        const codeInput = row.querySelector('.item-code-input');
        const productIdInput = row.querySelector('.product-id');
        const unitSelect = row.querySelector('.uom');
        const rateInput = row.querySelector('.rate');
        const cgstInput = row.querySelector('.cgst-percent');
        const sgstInput = row.querySelector('.sgst-percent');
        const igstInput = row.querySelector('.igst-percent');

        const productByName = products.find(function (p) {
            return (p.product_name || '').toLowerCase() === (productInput.value || '').trim().toLowerCase();
        });
        const productByCode = products.find(function (p) {
            return (p.product_code || '').toLowerCase() === (codeInput.value || '').trim().toLowerCase();
        });
        let product = null;

        if (source === 'product') {
            product = productByName || null;
            if (product) {
                codeInput.value = product.product_code || '';
            }
        } else if (source === 'code') {
            product = productByCode || null;
            if (product) {
                productInput.value = product.product_name || '';
            }
        } else {
            product = productByName || productByCode || null;
            if (product) {
                productInput.value = product.product_name || '';
                codeInput.value = product.product_code || '';
            }
        }

        if (!product) {
            productIdInput.value = '';
            unitSelect.innerHTML = '<option value="uom">UOM</option>';
            rateInput.value = '0.00';
            cgstInput.value = '0.00';
            sgstInput.value = '0.00';
            igstInput.value = '0.00';
            recalcRow(row);
            return;
        }

        productIdInput.value = product.id;

        const currentUnit = unitSelect.value || 'uom';
        let uomOptions = '<option value="uom">' + (product.uom || 'UOM') + '</option>';
        if (product.sales_uom) {
            uomOptions += '<option value="sales_uom">' + product.sales_uom + '</option>';
        }
        unitSelect.innerHTML = uomOptions;
        unitSelect.value = currentUnit === 'sales_uom' && product.sales_uom ? 'sales_uom' : 'uom';

        const rate = unitSelect.value === 'sales_uom' ? Number(product.rate_su || 0) : Number(product.rate_bu || 0);
        rateInput.value = rate.toFixed(2);
        cgstInput.value = Number(product.cgst_percent || 0).toFixed(2);
        sgstInput.value = Number(product.sgst_percent || 0).toFixed(2);
        igstInput.value = Number(product.igst_percent || 0).toFixed(2);
        recalcRow(row);
    }

    function addRow(data) {
        const row = document.createElement('tr');
        row.innerHTML = ''
            + '<td><input type="hidden" name="product_id[]" class="product-id"><input type="text" class="form-control product-name-input" list="product-name-list" placeholder="Type or select product" required></td>'
            + '<td><input type="text" class="form-control item-code-input" list="item-code-list" placeholder="Type or select code"></td>'
            + '<td><select name="unit_name[]" class="form-control uom"><option value="uom">UOM</option></select></td>'
            + '<td><input type="number" step="0.001" min="0.001" name="qty[]" class="form-control qty" value="1" required></td>'
            + '<td><input type="number" step="0.01" min="0" name="rate[]" class="form-control rate" value="0.00" readonly></td>'
            + '<td><input type="number" step="0.01" class="form-control amount" value="0.00" readonly></td>'
            + '<td><input type="number" step="0.01" class="form-control cgst-percent" value="0.00" readonly></td>'
            + '<td><input type="number" step="0.01" class="form-control sgst-percent" value="0.00" readonly></td>'
            + '<td><input type="number" step="0.01" class="form-control igst-percent" value="0.00" readonly></td>'
            + '<td><input type="number" step="0.01" class="form-control gst-amount" value="0.00" readonly></td>'
            + '<td><input type="number" step="0.01" class="form-control line-total" value="0.00" readonly></td>'
            + '<td><button type="button" class="btn btn-sm btn-danger remove-row remove-x-btn">X</button></td>';

        body.appendChild(row);
        row.querySelector('.product-name-input').addEventListener('change', function () { syncProduct(row, 'product'); });
        row.querySelector('.product-name-input').addEventListener('blur', function () { syncProduct(row, 'product'); });
        row.querySelector('.item-code-input').addEventListener('change', function () { syncProduct(row, 'code'); });
        row.querySelector('.item-code-input').addEventListener('blur', function () { syncProduct(row, 'code'); });
        row.querySelector('.uom').addEventListener('change', function () { syncProduct(row, 'product'); });
        row.querySelector('.qty').addEventListener('input', function () { recalcRow(row); });
        row.querySelector('.remove-row').addEventListener('click', function () {
            if (body.querySelectorAll('tr').length === 1) {
                row.querySelector('.product-id').value = '';
                row.querySelector('.product-name-input').value = '';
                row.querySelector('.item-code-input').value = '';
                row.querySelector('.uom').innerHTML = '<option value="uom">UOM</option>';
                row.querySelector('.qty').value = '1';
                row.querySelector('.rate').value = '0.00';
                row.querySelector('.amount').value = '0.00';
                row.querySelector('.cgst-percent').value = '0.00';
                row.querySelector('.sgst-percent').value = '0.00';
                row.querySelector('.igst-percent').value = '0.00';
                row.querySelector('.gst-amount').value = '0.00';
                recalcRow(row);
                return;
            }
            row.remove();
            recalcTotals();
        });

        if (data) {
            row.querySelector('.product-name-input').value = data.product_name || '';
            row.querySelector('.item-code-input').value = data.product_code || '';
            syncProduct(row, 'prefill');
            row.querySelector('.uom').value = data.unit_name === 'sales_uom' ? 'sales_uom' : 'uom';
            syncProduct(row, 'product');
            row.querySelector('.qty').value = data.qty || 1;
            recalcRow(row);
        } else {
            syncProduct(row, 'product');
        }
    }

    const productNameList = document.createElement('datalist');
    productNameList.id = 'product-name-list';
    productNameList.innerHTML = productOptionsByName();
    document.body.appendChild(productNameList);

    const itemCodeList = document.createElement('datalist');
    itemCodeList.id = 'item-code-list';
    itemCodeList.innerHTML = productOptionsByCode();
    document.body.appendChild(itemCodeList);

    customerName.addEventListener('change', resolveCustomerId);
    customerName.addEventListener('blur', resolveCustomerId);
    discountAmountInput.addEventListener('input', recalcTotals);
    addLineBtn.addEventListener('click', function () { addRow(null); });

    if (prefillRows.length > 0) {
        prefillRows.forEach(function (r) { addRow(r); });
    } else {
        addRow(null);
    }
    resolveCustomerId();
})();
</script>
@endpush
