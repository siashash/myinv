@extends('headerfooter')

@section('content')
<div class="container-fluid px-4 mt-4">
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Sales report</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('reports.sales') }}" class="row">
                <div class="col-md-3 mb-3">
                    <label for="date_from">From date</label>
                    <input type="date" id="date_from" name="date_from" class="form-control" value="{{ $filters['date_from'] }}">
                </div>
                <div class="col-md-3 mb-3">
                    <label for="date_to">To date</label>
                    <input type="date" id="date_to" name="date_to" class="form-control" value="{{ $filters['date_to'] }}">
                </div>
                <div class="col-md-3 mb-3">
                    <label for="customer_id">Customer</label>
                    <select id="customer_id" name="customer_id" class="form-control">
                        <option value="">All customers</option>
                        @foreach ($customers as $customer)
                            <option value="{{ $customer->customer_id }}" {{ $filters['customer_id'] === (string) $customer->customer_id ? 'selected' : '' }}>
                                {{ $customer->customer_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary mr-2">Get records</button>
                    <a href="{{ route('reports.sales') }}" class="btn btn-secondary">Reset</a>
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
                            <th>Date</th>
                            <th>Invoice No</th>
                            <th>Customer Name</th>
                            <th>Product Name</th>
                            <th>Uom</th>
                            <th class="text-right">Qty</th>
                            <th class="text-right">Price</th>
                            <th class="text-right">Amount</th>
                            <th class="text-right">Cgst %</th>
                            <th class="text-right">Sgst %</th>
                            <th class="text-right">Igst %</th>
                            <th class="text-right">Total Tax Amount</th>
                            <th class="text-right">Invoice Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rows as $row)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($row->sale_date)->format('d-m-Y') }}</td>
                                <td>{{ $row->invoice_no }}</td>
                                <td>{{ $row->customer_name }}</td>
                                <td>{{ $row->product_name }}</td>
                                <td>{{ $row->uom }}</td>
                                <td class="text-right">{{ number_format((float) $row->qty, 3) }}</td>
                                <td class="text-right">{{ number_format((float) $row->rate, 2) }}</td>
                                <td class="text-right">{{ number_format((float) $row->amount, 2) }}</td>
                                <td class="text-right">{{ number_format((float) $row->cgst_percent, 2) }}</td>
                                <td class="text-right">{{ number_format((float) $row->sgst_percent, 2) }}</td>
                                <td class="text-right">{{ number_format((float) $row->igst_percent, 2) }}</td>
                                <td class="text-right">{{ number_format((float) $row->gst_amount, 2) }}</td>
                                <td class="text-right">{{ number_format((float) $row->invoice_amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="13" class="text-center py-3">No sales records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
