@extends('headerfooter')

@section('content')
<div class="container mt-4">
    <div class="card mb-4">
        <div class="card-header"><h5 class="mb-0">{{ $title }}</h5></div>
        <div class="card-body">
            <form method="GET" action="{{ route('reports.account-book') }}" class="row">
                <div class="col-md-3 mb-3">
                    <label for="date_from">From date</label>
                    <input type="date" id="date_from" name="date_from" class="form-control" value="{{ $filters['date_from'] ?? '' }}">
                </div>
                <div class="col-md-3 mb-3">
                    <label for="date_to">To date</label>
                    <input type="date" id="date_to" name="date_to" class="form-control" value="{{ $filters['date_to'] ?? '' }}">
                </div>
                <div class="col-md-3 mb-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary mr-2">Filter</button>
                    <a href="{{ route('reports.account-book') }}" class="btn btn-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h5 class="mb-0">Entries</h5></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Transaction Details</th>
                            <th>Mode of Payment / Receipt</th>
                            <th class="text-end">Dr</th>
                            <th class="text-end">Cr</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rows as $row)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($row['date'])->format('d-m-Y') }}</td>
                                <td>{{ $row['transaction_details'] }}</td>
                                <td>{{ $row['mode'] }}</td>
                                <td class="text-end">{{ $row['side'] === 'Debit' ? number_format((float) $row['amount'], 2) : '-' }}</td>
                                <td class="text-end">{{ $row['side'] === 'Credit' ? number_format((float) $row['amount'], 2) : '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No entries found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
