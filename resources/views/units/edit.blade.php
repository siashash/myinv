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
                            <label for="prim_uom">Primary UOM</label>
                            <input
                                type="text"
                                id="prim_uom"
                                name="prim_uom"
                                class="form-control"
                                value="{{ old('prim_uom', $unit->prim_uom) }}"
                                required
                            >
                            @error('prim_uom')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="prim_uom_conv">Primary UOM conversion</label>
                            <input
                                type="number"
                                step="0.0001"
                                min="0.0001"
                                id="prim_uom_conv"
                                name="prim_uom_conv"
                                class="form-control"
                                value="{{ old('prim_uom_conv', $unit->prim_uom_conv) }}"
                                required
                            >
                            @error('prim_uom_conv')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="sec_uom">Secondary UOM</label>
                            <input
                                type="text"
                                id="sec_uom"
                                name="sec_uom"
                                class="form-control"
                                value="{{ old('sec_uom', $unit->sec_uom) }}"
                                maxlength="50"
                                required
                            >
                            @error('sec_uom')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="sec_uom_conv">Secondary UOM conversion</label>
                            <input
                                type="number"
                                step="0.0001"
                                min="0.0001"
                                id="sec_uom_conv"
                                name="sec_uom_conv"
                                class="form-control"
                                value="{{ old('sec_uom_conv', $unit->sec_uom_conv) }}"
                                required
                            >
                            @error('sec_uom_conv')
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
