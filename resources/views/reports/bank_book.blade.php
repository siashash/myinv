@extends('headerfooter')

@section('content')
<div class="container mt-4">
    <div class="card">
        <div class="card-header"><h5 class="mb-0">{{ $title }}</h5></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Transaction Details</th>
                            <th>Side</th>
                            <th>Mode of Payment / Receipt</th>
                            <th class="text-end">Inv. Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rows as $row)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($row['date'])->format('d-m-Y') }}</td>
                                <td>{{ $row['transaction_details'] }}</td>
                                <td>{{ $row['side'] }}</td>
                                <td>{{ $row['mode'] }}</td>
                                <td class="text-end">{{ number_format((float) $row['amount'], 2) }}</td>
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
