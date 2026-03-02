@extends('headerfooter')

@section('content')
@php
    $prefillRows = [];
    if (is_array(old('product_id')) && count(old('product_id')) > 0) {
        foreach (old('product_id') as $i => $productId) {
            $prefillRows[] = [
                'product_id' => (string) $productId,
                'unit_name' => old('unit_name.' . $i, 'uom'),
                'qty' => old('qty.' . $i),
                'cgst_percent' => old('cgst_percent.' . $i, '0'),
                'sgst_percent' => old('sgst_percent.' . $i, '0'),
                'igst_percent' => old('igst_percent.' . $i, '0'),
            ];
        }
    } else {
        foreach ($purchase->details as $detail) {
            $product = $products->firstWhere('id', $detail->product_id);
            $unitName = 'uom';
            if ($product && (string) $detail->sales_unit === (string) $product->sales_uom) {
                $unitName = 'sales_uom';
            }
            $prefillRows[] = [
                'product_id' => $detail->product_id ? (string) $detail->product_id : '',
                'unit_name' => $unitName,
                'qty' => $detail->qty,
                'cgst_percent' => $detail->cgst_percent,
                'sgst_percent' => $detail->sgst_percent,
                'igst_percent' => $detail->igst_percent,
            ];
        }
    }

    if (count($prefillRows) === 0) {
        $prefillRows[] = [
            'product_id' => '',
            'unit_name' => 'uom',
            'qty' => '',
            'cgst_percent' => '0',
            'sgst_percent' => '0',
            'igst_percent' => '0',
        ];
    }

    $productRows = $products->map(function ($p) {
        return [
            'id' => (string) $p->id,
            'product_name' => $p->product_name,
            'uom' => $p->uom,
            'sales_uom' => $p->sales_uom,
            'sales_price_bu' => (float) ($p->sales_price_bu ?? 0),
            'sales_price_su' => (float) ($p->sales_price_su ?? 0),
            'cgst_percent' => (float) ($p->cgst_percent ?? 0),
            'sgst_percent' => (float) ($p->sgst_percent ?? 0),
            'igst_percent' => (float) ($p->igst_percent ?? 0),
        ];
    })->values();
@endphp

<div class="container-fluid px-4 mt-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Edit purchase</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('purchases.update', $purchase) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label for="entry_date">Entry date</label>
                                <input type="date" id="entry_date" name="entry_date" class="form-control" value="{{ old('entry_date', $purchase->entry_date) }}" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="supplier_id">Supplier name</label>
                                <select id="supplier_id" name="supplier_id" class="form-control" required>
                                    <option value="">Select supplier</option>
                                    @foreach ($suppliers as $supplier)
                                        <option value="{{ $supplier->supplier_id }}" {{ old('supplier_id', $purchase->supplier_id) == $supplier->supplier_id ? 'selected' : '' }}>
                                            {{ $supplier->supplier_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="supplier_inv_no">Supplier inv no</label>
                                <input type="text" id="supplier_inv_no" name="supplier_inv_no" class="form-control" value="{{ old('supplier_inv_no', $purchase->supplier_inv_no) }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="purchase_date">Purchase date</label>
                                <input type="date" id="purchase_date" name="purchase_date" class="form-control" value="{{ old('purchase_date', $purchase->purchase_date) }}" required>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Product name</th>
                                        <th>Uom</th>
                                        <th>Qty</th>
                                        <th>Price</th>
                                        <th>Total</th>
                                        <th>Cgst %</th>
                                        <th>Sgst %</th>
                                        <th>Igst %</th>
                                        <th>Gst amount</th>
                                        <th>Net amount</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="purchase-lines-body"></tbody>
                            </table>
                        </div>

                        <button type="button" id="add-row-btn" class="btn btn-success mb-3">Add new</button>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="tot_taxable_amount">Tot taxable amount</label>
                                <input type="number" step="0.01" id="tot_taxable_amount" class="form-control" readonly>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="tot_gst_amount">Tot gst amount</label>
                                <input type="number" step="0.01" id="tot_gst_amount" class="form-control" readonly>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="invoice_amount">Invoice amount</label>
                                <input type="number" step="0.01" id="invoice_amount" class="form-control" readonly>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="round_off">Round off</label>
                                <input type="number" step="1" min="0" max="4" id="round_off" class="form-control" value="2">
                                <small class="form-text text-muted">Digits after decimal for invoice amount</small>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Update purchase</button>
                        <a href="{{ route('purchases.index') }}" class="btn btn-secondary">Back</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    (function () {
        const products = @json($productRows);
        const prefillRows = @json($prefillRows);
        const body = document.getElementById('purchase-lines-body');
        const roundOffInput = document.getElementById('round_off');

        function productOptions(selectedId) {
            let html = '<option value="">Select product</option>';
            products.forEach(function (product) {
                const selected = String(selectedId) === String(product.id) ? ' selected' : '';
                html += '<option value="' + product.id + '"' + selected + '>' + product.product_name + '</option>';
            });
            return html;
        }

        function createRow(data) {
            const row = document.createElement('tr');
            row.innerHTML = '' +
                '<td><select name="product_id[]" class="form-control product-id" style="min-width: 320px;" required>' + productOptions(data.product_id || '') + '</select></td>' +
                '<td><select name="unit_name[]" class="form-control unit-name" style="min-width: 140px;" required></select></td>' +
                '<td><input type="number" step="0.001" min="0.001" name="qty[]" class="form-control qty" value="' + (data.qty || '') + '" style="min-width: 70px;" required></td>' +
                '<td><input type="number" step="0.01" min="0" class="form-control sale-price" style="min-width: 100px;" readonly></td>' +
                '<td><input type="number" step="0.01" class="form-control amount" style="min-width: 100px;" readonly></td>' +
                '<td><input type="number" step="0.01" min="0" max="100" class="form-control cgst-percent" value="' + (data.cgst_percent || '0') + '" style="min-width: 70px;" readonly></td>' +
                '<td><input type="number" step="0.01" min="0" max="100" class="form-control sgst-percent" value="' + (data.sgst_percent || '0') + '" style="min-width: 70px;" readonly></td>' +
                '<td><input type="number" step="0.01" min="0" max="100" class="form-control igst-percent" value="' + (data.igst_percent || '0') + '" style="min-width: 70px;" readonly></td>' +
                '<td><input type="number" step="0.01" class="form-control gst-amount" style="min-width: 110px;" readonly></td>' +
                '<td><input type="number" step="0.01" class="form-control net-amount" style="min-width: 110px;" readonly></td>' +
                '<td><button type="button" class="btn btn-sm btn-danger remove-row">Remove</button></td>';

            function syncProduct() {
                const selected = products.find(p => String(p.id) === String(row.querySelector('.product-id').value));
                const unitNameSelect = row.querySelector('.unit-name');
                if (selected) {
                    const currentUnitValue = unitNameSelect.value || data.unit_name || 'uom';
                    const baseLabel = selected.uom || 'Base';
                    const salesLabel = selected.sales_uom || 'Sales';
                    const basePrice = Number(selected.sales_price_bu || 0);
                    const salesPrice = Number(selected.sales_price_su || 0);

                    unitNameSelect.innerHTML =
                        '<option value="uom">P.Uom (' + baseLabel + ')</option>' +
                        '<option value="sales_uom">S.Uom (' + salesLabel + ')</option>';
                    const selectedUnit = currentUnitValue === 'sales_uom' ? 'sales_uom' : 'uom';
                    unitNameSelect.value = selectedUnit;
                    row.querySelector('.sale-price').value = (selectedUnit === 'sales_uom' ? salesPrice : basePrice).toFixed(2);
                } else {
                    unitNameSelect.innerHTML =
                        '<option value="uom">P.Uom</option>' +
                        '<option value="sales_uom">S.Uom</option>';
                    unitNameSelect.value = 'uom';
                    row.querySelector('.sale-price').value = '0.00';
                }
                row.querySelector('.cgst-percent').value = selected ? Number(selected.cgst_percent || 0).toFixed(2) : '0.00';
                row.querySelector('.sgst-percent').value = selected ? Number(selected.sgst_percent || 0).toFixed(2) : '0.00';
                row.querySelector('.igst-percent').value = selected ? Number(selected.igst_percent || 0).toFixed(2) : '0.00';
                recalcRow(row);
            }

            row.querySelector('.product-id').addEventListener('change', syncProduct);
            row.querySelector('.unit-name').addEventListener('change', syncProduct);
            row.querySelectorAll('.qty, .sale-price, .cgst-percent, .sgst-percent, .igst-percent').forEach(function (input) {
                input.addEventListener('input', function () {
                    recalcRow(row);
                });
            });

            row.querySelector('.remove-row').addEventListener('click', function () {
                if (body.querySelectorAll('tr').length === 1) {
                    row.querySelectorAll('input').forEach(function (input) {
                        if (!input.readOnly) {
                            input.value = '';
                        }
                    });
                    row.querySelector('.product-id').value = '';
                    recalcRow(row);
                    return;
                }
                row.remove();
                recalcTotals();
            });

            body.appendChild(row);
            syncProduct();
        }

        function recalcRow(row) {
            const qty = parseFloat(row.querySelector('.qty').value || 0);
            const salePrice = parseFloat(row.querySelector('.sale-price').value || 0);
            const amount = qty * salePrice;

            const cgstPercent = parseFloat(row.querySelector('.cgst-percent').value || 0);
            const sgstPercent = parseFloat(row.querySelector('.sgst-percent').value || 0);
            const igstPercent = parseFloat(row.querySelector('.igst-percent').value || 0);

            const cgstAmount = amount * cgstPercent / 100;
            const sgstAmount = amount * sgstPercent / 100;
            const igstAmount = amount * igstPercent / 100;

            const gstAmount = cgstAmount + sgstAmount + igstAmount;
            const netAmount = amount + gstAmount;

            row.querySelector('.amount').value = amount.toFixed(2);
            row.querySelector('.gst-amount').value = gstAmount.toFixed(2);
            row.querySelector('.net-amount').value = netAmount.toFixed(2);

            recalcTotals();
        }

        function recalcTotals() {
            let taxable = 0;
            let gst = 0;

            body.querySelectorAll('tr').forEach(function (row) {
                const amount = parseFloat(row.querySelector('.amount').value || 0);
                const gstAmount = parseFloat(row.querySelector('.gst-amount').value || 0);
                taxable += amount;
                gst += gstAmount;
            });

            document.getElementById('tot_taxable_amount').value = taxable.toFixed(2);
            document.getElementById('tot_gst_amount').value = gst.toFixed(2);
            const decimals = Math.max(0, Math.min(4, parseInt(roundOffInput.value || '2', 10)));
            document.getElementById('invoice_amount').value = (taxable + gst).toFixed(decimals);
        }

        document.getElementById('add-row-btn').addEventListener('click', function () {
            createRow({ unit_name: 'uom', cgst_percent: '0.00', sgst_percent: '0.00', igst_percent: '0.00' });
        });

        roundOffInput.addEventListener('input', recalcTotals);

        prefillRows.forEach(function (row) {
            createRow(row);
        });
    })();
</script>
@endpush
