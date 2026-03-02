@extends('headerfooter')

@section('content')
<div class="container-fluid px-4 mt-4">
    <div class="card mb-4">
        <div class="card-header"><h5 class="mb-0">Sales receipts (Credit)</h5></div>
        <div class="card-body">
            <form method="GET" action="{{ route('sales-receipts.index') }}" class="row">
                <div class="col-md-4 mb-3">
                    <label for="customer_id">Customer name</label>
                    <select id="customer_id" name="customer_id" class="form-control">
                        <option value="">All customers</option>
                        @foreach ($customers as $customer)
                            <option value="{{ $customer->customer_id }}" {{ $selectedCustomerId === (string) $customer->customer_id ? 'selected' : '' }}>
                                {{ $customer->customer_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary mr-2">Get records</button>
                    <a href="{{ route('sales-receipts.index') }}" class="btn btn-secondary">Reset</a>
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
                            <th>Date</th>
                            <th>Invoice No</th>
                            <th>Customer</th>
                            <th class="text-right">Invoice Amount</th>
                            <th class="text-right">Sales Return</th>
                            <th class="text-right">Receivable Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rows as $row)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($row->sale_date)->format('d-m-Y') }}</td>
                                <td>{{ $row->invoice_no }}</td>
                                <td>{{ $row->customer_name }}</td>
                                <td class="text-right">{{ number_format((float) $row->total_amount, 2) }}</td>
                                <td class="text-right">{{ number_format((float) $row->return_amount, 2) }}</td>
                                <td class="text-right font-weight-bold">{{ number_format((float) $row->receivable_amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No credit sales found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
