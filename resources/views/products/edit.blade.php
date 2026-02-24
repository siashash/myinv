@extends('headerfooter')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Edit product</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('products.update', $product) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="category_id">Category</label>
                                <select id="category_id" name="category_id" class="form-control" required>
                                    <option value="">Select category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->category_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="sub_category_id">Sub category</label>
                                <select id="sub_category_id" name="sub_category_id" class="form-control" required>
                                    <option value="">Select sub category</option>
                                    @foreach ($subCategories as $subCategory)
                                        <option value="{{ $subCategory->id }}" {{ old('sub_category_id', $product->sub_category_id) == $subCategory->id ? 'selected' : '' }}>
                                            {{ $subCategory->sub_category_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('sub_category_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="product_name">Product name</label>
                                <input type="text" id="product_name" name="product_name" class="form-control" value="{{ old('product_name', $product->product_name) }}" required>
                                @error('product_name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="product_code">Product code</label>
                                <input type="text" id="product_code" name="product_code" class="form-control" value="{{ old('product_code', $product->product_code) }}" required>
                                @error('product_code')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="hsn_code">Hsn code</label>
                                <input type="text" id="hsn_code" name="hsn_code" class="form-control" value="{{ old('hsn_code', $product->hsn_code) }}" required>
                                @error('hsn_code')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="unit_id">Unit conversion</label>
                                <select id="unit_id" name="unit_id" class="form-control" required>
                                    <option value="">Select unit conversion</option>
                                    @foreach ($units as $unit)
                                        <option
                                            value="{{ $unit->id }}"
                                            data-base-unit="{{ $unit->base_unit }}"
                                            data-conversion-factor="{{ $unit->conversion_factor }}"
                                            {{ old('unit_id', $product->unit_id) == $unit->id ? 'selected' : '' }}
                                        >
                                            1 {{ $unit->base_unit }} = {{ number_format($unit->conversion_factor, 4) }} {{ $unit->sales_unit }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('unit_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="purchase_price">Purchase price</label>
                                <input type="number" step="0.01" min="0" id="purchase_price" name="purchase_price" class="form-control" value="{{ old('purchase_price', $product->purchase_price) }}" required>
                                @error('purchase_price')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="sales_price_bu">Sales price bu</label>
                                <input type="number" step="0.01" min="0" id="sales_price_bu" name="sales_price_bu" class="form-control" value="{{ old('sales_price_bu', $product->sales_price_bu ?? 0) }}" readonly>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="sales_price_su">Sales price su</label>
                                <input type="number" step="0.01" min="0" id="sales_price_su" name="sales_price_su" class="form-control" value="{{ old('sales_price_su', $product->sales_price_su ?? 0) }}" readonly>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="sale_price">Sale price</label>
                                <input type="number" step="0.01" min="0" id="sale_price" name="sale_price" class="form-control" value="{{ old('sale_price', $product->sale_price) }}" readonly>
                                @error('sale_price')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="discount_amount">Discount amount</label>
                                <input type="number" step="0.01" min="0" id="discount_amount" name="discount_amount" class="form-control" value="{{ old('discount_amount', $product->discount_amount) }}" required>
                                @error('discount_amount')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="final_price">Final price</label>
                                <input type="number" step="0.01" min="0" id="final_price" name="final_price" class="form-control" value="{{ old('final_price', $product->final_price) }}" readonly>
                                @error('final_price')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label id="opening_stock_label" for="opening_stock">Opening stock (Base unit)</label>
                                <input type="number" step="0.01" min="0" id="opening_stock" name="opening_stock" class="form-control" value="{{ old('opening_stock', $product->opening_stock) }}" required>
                                @error('opening_stock')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="cgst_percent">Cgst %</label>
                                <input type="number" step="0.01" min="0" max="100" id="cgst_percent" name="cgst_percent" class="form-control" value="{{ old('cgst_percent', $product->cgst_percent ?? 0) }}" required>
                                @error('cgst_percent')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="sgst_percent">Sgst %</label>
                                <input type="number" step="0.01" min="0" max="100" id="sgst_percent" name="sgst_percent" class="form-control" value="{{ old('sgst_percent', $product->sgst_percent ?? 0) }}" required>
                                @error('sgst_percent')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="igst_percent">Igst %</label>
                                <input type="number" step="0.01" min="0" max="100" id="igst_percent" name="igst_percent" class="form-control" value="{{ old('igst_percent', $product->igst_percent ?? 0) }}" required>
                                @error('igst_percent')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="status">Status</label>
                                <select id="status" name="status" class="form-control" required>
                                    <option value="1" {{ old('status', (string) $product->status) == '1' ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ old('status', (string) $product->status) == '0' ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @error('status')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Update product</button>
                        <a href="{{ route('products.index') }}" class="btn btn-secondary">Back</a>
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
        const categorySelect = document.getElementById('category_id');
        const subCategorySelect = document.getElementById('sub_category_id');
        const unitSelect = document.getElementById('unit_id');
        const openingStockLabel = document.getElementById('opening_stock_label');
        const baseUrl = "{{ url('/ajax/categories') }}";
        const selectedSubCategoryId = "{{ old('sub_category_id', $product->sub_category_id) }}";
        const purchasePrice = document.getElementById('purchase_price');
        const salesPriceBu = document.getElementById('sales_price_bu');
        const salesPriceSu = document.getElementById('sales_price_su');
        const salePrice = document.getElementById('sale_price');
        const discountAmount = document.getElementById('discount_amount');
        const cgstPercent = document.getElementById('cgst_percent');
        const sgstPercent = document.getElementById('sgst_percent');
        const igstPercent = document.getElementById('igst_percent');
        const finalPrice = document.getElementById('final_price');

        async function loadSubCategories(categoryId, selectedId = '') {
            subCategorySelect.innerHTML = '<option value="">Select sub category</option>';
            if (!categoryId) {
                return;
            }

            try {
                const response = await fetch(baseUrl + '/' + categoryId + '/subcategories');
                const data = await response.json();

                data.forEach(function (item) {
                    const option = document.createElement('option');
                    option.value = item.id;
                    option.textContent = item.sub_category_name;
                    if (String(selectedId) === String(item.id)) {
                        option.selected = true;
                    }
                    subCategorySelect.appendChild(option);
                });
            } catch (error) {
                console.error('Failed to load sub categories:', error);
            }
        }

        function syncOpeningStockLabel() {
            const selectedOption = unitSelect.options[unitSelect.selectedIndex];
            const baseUnit = selectedOption ? selectedOption.getAttribute('data-base-unit') : '';
            openingStockLabel.textContent = baseUnit
                ? 'Opening stock (' + baseUnit + ')'
                : 'Opening stock (Base unit)';
        }

        function getSelectedConversionFactor() {
            const selectedOption = unitSelect.options[unitSelect.selectedIndex];
            if (!selectedOption) {
                return 1;
            }
            const factor = parseFloat(selectedOption.getAttribute('data-conversion-factor') || 1);
            return factor > 0 ? factor : 1;
        }

        categorySelect.addEventListener('change', function () {
            loadSubCategories(this.value);
        });

        unitSelect.addEventListener('change', syncOpeningStockLabel);

        if (categorySelect.value) {
            loadSubCategories(categorySelect.value, selectedSubCategoryId);
        }

        function updatePrices() {
            const purchase = parseFloat(purchasePrice.value || 0);
            const conversionFactor = getSelectedConversionFactor();
            const bu = purchase;
            const su = purchase / conversionFactor;
            const discount = parseFloat(discountAmount.value || 0);
            const cgst = parseFloat(cgstPercent.value || 0);
            const sgst = parseFloat(sgstPercent.value || 0);
            const igst = parseFloat(igstPercent.value || 0);
            const sale = Math.max(0, purchase - discount);
            const tax = cgst + sgst + igst;
            const final = sale + (sale * tax / 100);

            salesPriceBu.value = bu.toFixed(2);
            salesPriceSu.value = su.toFixed(2);
            salePrice.value = sale.toFixed(2);
            finalPrice.value = final.toFixed(2);
        }

        syncOpeningStockLabel();
        unitSelect.addEventListener('change', updatePrices);
        purchasePrice.addEventListener('input', updatePrices);
        discountAmount.addEventListener('input', updatePrices);
        cgstPercent.addEventListener('input', updatePrices);
        sgstPercent.addEventListener('input', updatePrices);
        igstPercent.addEventListener('input', updatePrices);
        updatePrices();
    })();
</script>
@endpush
