@extends('headerfooter')

@section('content')
<div class="container-fluid px-4 mt-4">
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Sales return</h5>
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

            <form method="GET" action="{{ route('sales-returns.index') }}" class="row" id="sales-return-filter-form">
                <div class="col-md-4 mb-3">
                    <label for="customer_id">Customer name</label>
                    <select id="customer_id" name="customer_id" class="form-control" required>
                        <option value="">Select customer</option>
                        @foreach ($customers as $customer)
                            <option value="{{ $customer->customer_id }}" {{ $filters['customer_id'] === (string) $customer->customer_id ? 'selected' : '' }}>
                                {{ $customer->customer_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-5 mb-3">
                    <label for="sale_id">Sales invoice</label>
                    <select id="sale_id" name="sale_id" class="form-control" {{ $filters['customer_id'] === '' ? 'disabled' : '' }}>
                        <option value="">Select invoice</option>
                        @foreach ($sales as $s)
                            <option value="{{ $s->id }}" {{ $filters['sale_id'] === (string) $s->id ? 'selected' : '' }}>
                                {{ $s->invoice_no }} | {{ \Carbon\Carbon::parse($s->sale_date)->format('d-m-Y') }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary mr-2">Get records</button>
                    <a href="{{ route('sales-returns.index') }}" class="btn btn-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    @if ($sale)
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    Sales invoice: {{ $sale->invoice_no }}
                    | Sale date: {{ \Carbon\Carbon::parse($sale->sale_date)->format('d-m-Y') }}
                </h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('sales-returns.store') }}">
                    @csrf
                    <input type="hidden" name="customer_id" value="{{ $sale->customer_id }}">
                    <input type="hidden" name="sale_id" value="{{ $sale->id }}">

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
                                    <th>Item Code</th>
                                    <th>Uom</th>
                                    <th class="text-end">Sale qty</th>
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
                                            <input type="hidden" name="sale_detail_id[]" value="{{ $detail->id }}">
                                        </td>
                                        <td>{{ $detail->item_code ?: '-' }}</td>
                                        <td>{{ $detail->uom }}</td>
                                        <td class="text-end">{{ number_format($detail->qty, 3) }}</td>
                                        <td class="text-end">{{ number_format($detail->returned_qty, 3) }}</td>
                                        <td class="text-end">{{ number_format($detail->max_return_qty, 3) }}</td>
                                        <td class="text-end">{{ number_format($detail->return_rate, 2) }}</td>
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
                            <button type="submit" class="btn btn-danger">Save sales return</button>
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
    const form = document.getElementById('sales-return-filter-form');
    if (!form) {
        return;
    }

    const customerSelect = document.getElementById('customer_id');
    const saleSelect = document.getElementById('sale_id');

    if (customerSelect) {
        customerSelect.addEventListener('change', function () {
            if (saleSelect) {
                saleSelect.value = '';
            }
            form.submit();
        });
    }
})();
</script>
@endpush
