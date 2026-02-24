@extends('headerfooter')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Edit unit conversion</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('units.update', $unit) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group mb-3">
                            <label for="base_unit">Base unit</label>
                            <input
                                type="text"
                                id="base_unit"
                                name="base_unit"
                                class="form-control"
                                value="{{ old('base_unit', $unit->base_unit) }}"
                                required
                            >
                            @error('base_unit')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="sales_unit">Sales unit</label>
                            <input
                                type="text"
                                id="sales_unit"
                                name="sales_unit"
                                class="form-control"
                                value="{{ old('sales_unit', $unit->sales_unit) }}"
                                maxlength="50"
                                required
                            >
                            @error('sales_unit')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="conversion_factor">Conversion factor</label>
                            <input
                                type="number"
                                step="0.0001"
                                min="0.0001"
                                id="conversion_factor"
                                name="conversion_factor"
                                class="form-control"
                                value="{{ old('conversion_factor', $unit->conversion_factor) }}"
                                required
                            >
                            @error('conversion_factor')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">Update unit conversion</button>
                        <a href="{{ route('units.index') }}" class="btn btn-secondary">Back</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
