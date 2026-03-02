@extends('headerfooter')

@section('content')
<div class="container mt-4">
    <div class="card mb-4">
        <div class="card-header"><h5 class="mb-0">Stock report</h5></div>
        <div class="card-body">
            <form method="GET" action="{{ route('reports.stock') }}" class="row">
                <div class="col-md-4 mb-3">
                    <label for="entry_date">Entry date</label>
                    <input type="date" id="entry_date" name="entry_date" class="form-control" value="{{ $filters['entry_date'] }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label for="product_id">Product</label>
                    <select id="product_id" name="product_id" class="form-control">
                        <option value="">All products</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}" {{ $filters['product_id'] === (string) $product->id ? 'selected' : '' }}>
                                {{ $product->product_name }} ({{ $product->product_code }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary mr-2">Filter</button>
                    <a href="{{ route('reports.stock') }}" class="btn btn-secondary">Reset</a>
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

    @if ($selectedProduct)
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="mb-0">
                    Product wise stock - {{ $selectedProduct->product_name }} ({{ $selectedProduct->product_code }})
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>S.No</th>
                                <th>Date</th>
                                <th>Batch ID</th>
                                <th class="text-end">Purchase Qty</th>
                                <th class="text-end">Purchase Return Qty</th>
                                <th class="text-end">Net Qty</th>
                                <th class="text-end">Stock Balance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($productMovements as $movement)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ \Carbon\Carbon::parse($movement['entry_date'])->format('d-m-Y') }}</td>
                                    <td>{{ $movement['batch_id'] }}</td>
                                    <td class="text-end">{{ number_format($movement['purchase_qty'], 3) }}</td>
                                    <td class="text-end">{{ number_format($movement['purchase_return_qty'], 3) }}</td>
                                    <td class="text-end">{{ number_format($movement['net_qty'], 3) }}</td>
                                    <td class="text-end font-weight-bold">{{ number_format($movement['balance_stock'], 3) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-3">No movement records found for selected product.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
