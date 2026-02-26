@extends('headerfooter')

@section('content')
<div class="container-fluid px-4 mt-4">
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Stock report</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('reports.stock') }}" class="row">
                <div class="col-md-3 mb-3">
                    <label for="entry_date">Entry date</label>
                    <input type="date" id="entry_date" name="entry_date" class="form-control" value="{{ $filters['entry_date'] }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label for="supplier_id">Supplier name</label>
                    <select id="supplier_id" name="supplier_id" class="form-control">
                        <option value="">All suppliers</option>
                        @foreach ($suppliers as $supplier)
                            <option value="{{ $supplier->supplier_id }}" {{ $filters['supplier_id'] === (string) $supplier->supplier_id ? 'selected' : '' }}>
                                {{ $supplier->supplier_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary mr-2">Get records</button>
                    <a href="{{ route('reports.stock') }}" class="btn btn-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>S.No</th>
                            <th>Item Code</th>
                            <th>Item Name</th>
                            <th class="text-end">Qty</th>
                            <th class="text-end">Opening Stock</th>
                            <th class="text-end">Purchase</th>
                            <th class="text-end">Sales</th>
                            <th class="text-end">Purchase Return</th>
                            <th class="text-end">Sales Return</th>
                            <th class="text-end">Current Stock</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rows as $row)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $row->product_code }}</td>
                                <td>{{ $row->product_name }}</td>
                                <td class="text-end">{{ number_format($row->movement_qty, 3) }}</td>
                                <td class="text-end">{{ number_format($row->opening_stock, 3) }}</td>
                                <td class="text-end">{{ number_format($row->purchase_qty, 3) }}</td>
                                <td class="text-end">{{ number_format($row->sales_qty, 3) }}</td>
                                <td class="text-end">{{ number_format($row->purchase_return_qty, 3) }}</td>
                                <td class="text-end">{{ number_format($row->sales_return_qty, 3) }}</td>
                                <td class="text-end font-weight-bold">{{ number_format($row->current_stock, 3) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-3">No stock records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
