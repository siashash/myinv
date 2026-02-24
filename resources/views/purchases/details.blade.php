@extends('headerfooter')

@section('content')
<div class="container mt-4">
    <div class="card mb-4">
        <div class="card-header"><h5 class="mb-0">Purchase details</h5></div>
        <div class="card-body">
            <form method="GET" action="{{ route('purchases.details') }}" class="row">
                <div class="col-md-4 mb-3">
                    <label for="purchase_date">Purchase date</label>
                    <input type="date" id="purchase_date" name="purchase_date" class="form-control" value="{{ $filters['purchase_date'] }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label for="supplier_id">Supplier name</label>
                    <select id="supplier_id" name="supplier_id" class="form-control">
                        <option value="">All suppliers</option>
                        @foreach ($suppliers as $supplier)
                            <option value="{{ $supplier->supplier_id }}" {{ $filters['supplier_id'] == $supplier->supplier_id ? 'selected' : '' }}>
                                {{ $supplier->supplier_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary mr-2">Filter</button>
                    <a href="{{ route('purchases.details') }}" class="btn btn-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Purchase date</th>
                            <th>Supplier name</th>
                            <th>Supplier inv no</th>
                            <th>Purchase mode</th>
                            <th>Product name</th>
                            <th>Uom</th>
                            <th>Qty</th>
                            <th>Price</th>
                            <th>Cgst %</th>
                            <th>Sgst %</th>
                            <th>Igst %</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rows as $row)
                            <tr>
                                <td>{{ $row->purchase_date }}</td>
                                <td>{{ $row->supplier_name }}</td>
                                <td>{{ $row->supplier_inv_no }}</td>
                                <td>{{ $row->purchase_mode }}</td>
                                <td>{{ $row->product_name }}</td>
                                <td>{{ $row->sales_unit }}</td>
                                <td>{{ number_format($row->qty, 3) }}</td>
                                <td>{{ number_format($row->sale_price, 2) }}</td>
                                <td>{{ number_format($row->cgst_percent, 2) }}</td>
                                <td>{{ number_format($row->sgst_percent, 2) }}</td>
                                <td>{{ number_format($row->igst_percent, 2) }}</td>
                                <td>{{ number_format($row->net_amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="12" class="text-center">No purchase details found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
