@extends('headerfooter')

@section('content')
<div class="container mt-4">
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if ($canAdd)
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Create unit conversion</h5>
            </div>
            <div class="card-body">

                @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('units.store') }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="base_unit">Base unit</label>
                            <input
                                type="text"
                                id="base_unit"
                                name="base_unit"
                                class="form-control"
                                value="{{ old('base_unit') }}"
                                required
                            >
                            @error('base_unit')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="sales_unit">Sales unit</label>
                            <input
                                type="text"
                                id="sales_unit"
                                name="sales_unit"
                                class="form-control"
                                value="{{ old('sales_unit') }}"
                                maxlength="50"
                                required
                            >
                            @error('sales_unit')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="conversion_factor">Conversion factor</label>
                            <input
                                type="number"
                                step="0.0001"
                                min="0.0001"
                                id="conversion_factor"
                                name="conversion_factor"
                                class="form-control"
                                value="{{ old('conversion_factor', '1') }}"
                                required
                            >
                            @error('conversion_factor')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Save unit</button>
                </form>
            </div>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Unit conversion list</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Base unit</th>
                            <th>Sales unit</th>
                            <th>Conversion factor</th>
                            <th width="150">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($units as $unit)
                            <tr>
                                <td>{{ $unit->id }}</td>
                                <td>{{ $unit->base_unit }}</td>
                                <td>{{ $unit->sales_unit }}</td>
                                <td>{{ number_format($unit->conversion_factor, 4) }}</td>
                                <td class="text-nowrap">
                                    @if ($canEdit)
                                        <a href="{{ route('units.edit', $unit) }}" class="btn btn-sm btn-info">Edit</a>
                                    @endif

                                    @if ($canDelete)
                                        <form action="{{ route('units.destroy', $unit) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No units found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
