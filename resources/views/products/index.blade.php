@extends('headerfooter')

@section('content')
<div class="container mt-4">
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if ($canAdd)
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Create product</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('products.store') }}" method="POST">
                    @csrf
                    <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="category_id">Category</label>
                        <select id="category_id" name="category_id" class="form-control" required>
                            <option value="">Select category</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
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
                        </select>
                        @error('sub_category_id')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="product_name">Product name</label>
                        <input type="text" id="product_name" name="product_name" class="form-control" value="{{ old('product_name') }}" required>
                        @error('product_name')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="product_code">Product code</label>
                        <input type="text" id="product_code" name="product_code" class="form-control" value="{{ old('product_code') }}" required>
                        @error('product_code')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="hsn_code">Hsn code</label>
                        <input type="text" id="hsn_code" name="hsn_code" class="form-control" value="{{ old('hsn_code') }}" required>
                        @error('hsn_code')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="w-100"></div>

                    <div class="col-md-3 mb-3">
                        <label for="uom">Primary UOM</label>
                        <select id="uom" name="uom" class="form-control" required>
                            <option value="">Select primary UOM</option>
                            @foreach ($primaryUoms as $uomOption)
                                <option value="{{ $uomOption }}" {{ old('uom') === $uomOption ? 'selected' : '' }}>{{ $uomOption }}</option>
                            @endforeach
                        </select>
                        @error('uom')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-2 mb-3">
                        <label for="sales_price_bu">Primary UOM price</label>
                        <input type="number" step="0.01" min="0" id="sales_price_bu" name="sales_price_bu" class="form-control" value="{{ old('sales_price_bu', '0') }}" required>
                        @error('sales_price_bu')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-3 mb-3">
                        <label for="sales_uom">Secondary UOM</label>
                        <select id="sales_uom" name="sales_uom" class="form-control" required>
                            <option value="">Select secondary UOM</option>
                            @foreach ($secondaryUoms as $uomOption)
                                <option value="{{ $uomOption }}" {{ old('sales_uom') === $uomOption ? 'selected' : '' }}>{{ $uomOption }}</option>
                            @endforeach
                        </select>
                        @error('sales_uom')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-2 mb-3">
                        <label for="sales_price_su">Secondary UOM price</label>
                        <input type="number" step="0.01" min="0" id="sales_price_su" name="sales_price_su" class="form-control" value="{{ old('sales_price_su', '0') }}" required>
                        @error('sales_price_su')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="w-100"></div>

                    <div class="col-md-2 mb-3">
                        <label for="discount_amount">Discount amount</label>
                        <input type="number" step="0.01" min="0" id="discount_amount" name="discount_amount" class="form-control" value="{{ old('discount_amount', '0') }}" required>
                        @error('discount_amount')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-2 mb-3">
                        <label for="cgst_percent">Cgst %</label>
                        <input type="number" step="0.01" min="0" max="100" id="cgst_percent" name="cgst_percent" class="form-control" value="{{ old('cgst_percent', '0') }}" required>
                        @error('cgst_percent')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-2 mb-3">
                        <label for="sgst_percent">Sgst %</label>
                        <input type="number" step="0.01" min="0" max="100" id="sgst_percent" name="sgst_percent" class="form-control" value="{{ old('sgst_percent', '0') }}" required>
                        @error('sgst_percent')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-2 mb-3">
                        <label for="igst_percent">Igst %</label>
                        <input type="number" step="0.01" min="0" max="100" id="igst_percent" name="igst_percent" class="form-control" value="{{ old('igst_percent', '0') }}" required>
                        @error('igst_percent')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-2 mb-3">
                        <label for="status">Status</label>
                        <select id="status" name="status" class="form-control" required>
                            <option value="1" {{ old('status', '1') == '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('status')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Save product</button>
                </form>
            </div>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Product list</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Category</th>
                            <th>Sub category</th>
                            <th>Product name</th>
                            <th>Product code</th>
                            <th>Hsn code</th>
                            <th>Primary price</th>
                            <th>Secondary price</th>
                            <th>Primary UOM</th>
                            <th>Sec. UOM</th>
                            <th>Discount amount</th>
                            <th>Cgst %</th>
                            <th>Sgst %</th>
                            <th>Igst %</th>
                            <th>Status</th>
                            <th width="150">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($products as $product)
                            <tr>
                                <td>{{ $product->id }}</td>
                                <td>{{ $product->category?->category_name }}</td>
                                <td>{{ $product->subCategory?->sub_category_name }}</td>
                                <td>{{ $product->product_name }}</td>
                                <td>{{ $product->product_code }}</td>
                                <td>{{ $product->hsn_code }}</td>
                                <td>{{ number_format($product->sales_price_bu ?? 0, 2) }}</td>
                                <td>{{ number_format($product->sales_price_su ?? 0, 2) }}</td>
                                <td>{{ $product->uom }}</td>
                                <td>{{ $product->sales_uom ?? '-' }}</td>
                                <td>{{ number_format($product->discount_amount, 2) }}</td>
                                <td>{{ number_format($product->cgst_percent ?? 0, 2) }}</td>
                                <td>{{ number_format($product->sgst_percent ?? 0, 2) }}</td>
                                <td>{{ number_format($product->igst_percent ?? 0, 2) }}</td>
                                <td>
                                    <span class="badge {{ $product->status ? 'bg-success' : 'bg-danger' }}">
                                        {{ $product->status ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="text-nowrap">
                                    @if ($canEdit)
                                        <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-info">Edit</a>
                                    @endif
                                    @if ($canDelete)
                                        <form action="{{ route('products.destroy', $product) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="16" class="text-center">No products found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
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
        const baseUrl = "{{ url('/ajax/categories') }}";
        const oldSubCategoryId = "{{ old('sub_category_id') }}";

        if (!categorySelect || !subCategorySelect) {
            return;
        }

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

        categorySelect.addEventListener('change', function () {
            loadSubCategories(this.value);
        });

        if (categorySelect.value) {
            loadSubCategories(categorySelect.value, oldSubCategoryId);
        }

    })();
</script>
@endpush
