@extends('headerfooter')

@section('content')
<div class="container-fluid px-4 mt-4">
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Purchase return</h5>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="GET" action="{{ route('purchase-returns.index') }}" class="row" id="purchase-return-filter-form">
                <div class="col-md-4 mb-3">
                    <label for="supplier_id">Supplier name</label>
                    <select id="supplier_id" name="supplier_id" class="form-control" required>
                        <option value="">Select supplier</option>
                        @foreach ($suppliers as $supplier)
                            <option value="{{ $supplier->supplier_id }}" {{ $filters['supplier_id'] === (string) $supplier->supplier_id ? 'selected' : '' }}>
                                {{ $supplier->supplier_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-5 mb-3">
                    <label for="purchase_id">Purchase invoice</label>
                    <select id="purchase_id" name="purchase_id" class="form-control" {{ $filters['supplier_id'] === '' ? 'disabled' : '' }}>
                        <option value="">Select invoice</option>
                        @foreach ($purchases as $p)
                            <option value="{{ $p->id }}" {{ $filters['purchase_id'] === (string) $p->id ? 'selected' : '' }}>
                                {{ $p->supplier_inv_no ?: 'INV-' . $p->id }} | {{ \Carbon\Carbon::parse($p->purchase_date)->format('d-m-Y') }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary mr-2">Get records</button>
                    <a href="{{ route('purchase-returns.index') }}" class="btn btn-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    @if ($purchase)
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    Supplier invoice: {{ $purchase->supplier_inv_no ?: 'INV-' . $purchase->id }}
                    | Purchase date: {{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d-m-Y') }}
                </h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('purchase-returns.store') }}">
                    @csrf
                    <input type="hidden" name="supplier_id" value="{{ $purchase->supplier_id }}">
                    <input type="hidden" name="purchase_id" value="{{ $purchase->id }}">

                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="return_date">Return date</label>
                            <input type="date" id="return_date" name="return_date" class="form-control" value="{{ old('return_date', date('Y-m-d')) }}" required>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Product</th>
                                    <th>Uom</th>
                                    <th class="text-end">Purchase qty</th>
                                    <th class="text-end">Already returned</th>
                                    <th class="text-end">Available qty</th>
                                    <th class="text-end">Rate</th>
                                    <th class="text-end">Return qty</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($details as $i => $detail)
                                    <tr>
                                        <td>
                                            {{ $detail->product_name }}
                                            <input type="hidden" name="purchase_detail_id[]" value="{{ $detail->id }}">
                                        </td>
                                        <td>{{ $detail->sales_unit }}</td>
                                        <td class="text-end">{{ number_format($detail->qty, 3) }}</td>
                                        <td class="text-end">{{ number_format($detail->returned_qty, 3) }}</td>
                                        <td class="text-end">{{ number_format($detail->max_return_qty, 3) }}</td>
                                        <td class="text-end">{{ number_format($detail->return_rate, 4) }}</td>
                                        <td class="text-end">
                                            <input
                                                type="number"
                                                step="0.001"
                                                min="0"
                                                max="{{ $detail->max_return_qty }}"
                                                name="return_qty[]"
                                                class="form-control text-end"
                                                value="{{ old('return_qty.' . $i, '0') }}"
                                                {{ $detail->max_return_qty <= 0 || ! $canAdd ? 'readonly' : '' }}
                                            >
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        @if ($canAdd)
                            <button type="submit" class="btn btn-danger">Save return</button>
                        @else
                            <span class="badge badge-secondary">View only</span>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
(function () {
    const form = document.getElementById('purchase-return-filter-form');
    if (!form) {
        return;
    }

    const supplierSelect = document.getElementById('supplier_id');
    const purchaseSelect = document.getElementById('purchase_id');

    if (supplierSelect) {
        supplierSelect.addEventListener('change', function () {
            if (purchaseSelect) {
                purchaseSelect.value = '';
            }
            form.submit();
        });
    }
})();
</script>
@endpush
