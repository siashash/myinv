@extends('headerfooter')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/purchase-payments.css') }}">
@endpush

@section('content')
<div class="container-fluid px-4 mt-4">
    <div class="card mb-4">
        <div class="card-header"><h5 class="mb-0">Purchase payment</h5></div>
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

            <form method="GET" action="{{ route('purchase-payments.index') }}" class="row">
                <div class="col-md-4 mb-3">
                    <label for="supplier_id">Supplier name</label>
                    <select id="supplier_id" name="supplier_id" class="form-control" required>
                        <option value="">Select supplier</option>
                        @foreach ($suppliers as $supplier)
                            <option value="{{ $supplier->supplier_id }}" {{ $selectedSupplierId === (string) $supplier->supplier_id ? 'selected' : '' }}>
                                {{ $supplier->supplier_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary mr-2">Get records</button>
                    <a href="{{ route('purchase-payments.index') }}" class="btn btn-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    @if ($selectedSupplierId !== '')
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0 purchase-payment-table">
                        <thead class="table-light">
                            <tr>
                                <th>Purchase date</th>
                                <th>Supplier inv no</th>
                                <th class="num-col">Invoice amount</th>
                                <th class="num-col">Paid amount</th>
                                <th class="num-col">Balance amount</th>
                                <th class="num-col">Payment amount</th>
                                <th>Payment mode</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($purchases as $purchase)
                                @php $formId = 'payment-form-' . $purchase->id; @endphp
                                <tr>
                                    <td>{{ $purchase->purchase_date }}</td>
                                    <td>{{ $purchase->supplier_inv_no ?: '-' }}</td>
                                    <td class="num-col">{{ number_format($purchase->invoice_amount, 2) }}</td>
                                    <td class="num-col">{{ number_format($purchase->paid_amount, 2) }}</td>
                                    <td class="num-col balance-col">{{ number_format($purchase->balance_amount, 2) }}</td>
                                    <td class="num-col payment-amount-cell">
                                        <input
                                            type="number"
                                            step="0.01"
                                            min="0.01"
                                            max="{{ $purchase->balance_amount }}"
                                            name="payment_amount"
                                            form="{{ $formId }}"
                                            class="form-control payment-amount-input"
                                            value="{{ old('purchase_id') == $purchase->id ? old('payment_amount') : $purchase->balance_amount }}"
                                            {{ $purchase->balance_amount <= 0 ? 'disabled' : '' }}
                                            required
                                        >
                                    </td>
                                    <td class="payment-mode-cell">
                                        <select name="payment_mode" form="{{ $formId }}" class="form-control" {{ $purchase->balance_amount <= 0 ? 'disabled' : '' }} required>
                                            @foreach (['Cash', 'Cheque', 'UPI'] as $mode)
                                                <option value="{{ $mode }}" {{ old('purchase_id') == $purchase->id && old('payment_mode') === $mode ? 'selected' : '' }}>{{ $mode }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <form id="{{ $formId }}" method="POST" action="{{ route('purchase-payments.store') }}" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="supplier_id" value="{{ $selectedSupplierId }}">
                                            <input type="hidden" name="purchase_id" value="{{ $purchase->id }}">
                                            @if ($purchase->balance_amount > 0)
                                                <button type="submit" class="btn btn-sm btn-primary">Payment</button>
                                            @else
                                                <span class="badge badge-success">Paid</span>
                                            @endif
                                        </form>
                                        @if ($purchase->latestPayment)
                                            <form method="POST" action="{{ route('purchase-payments.cancel', $purchase->latestPayment) }}" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Cancel latest payment for this invoice?')">Cancel</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">No purchase records found for selected supplier.</td>
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
