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
                            <label for="prim_uom">Primary UOM</label>
                            <input
                                type="text"
                                id="prim_uom"
                                name="prim_uom"
                                class="form-control"
                                value="{{ old('prim_uom') }}"
                                required
                            >
                            @error('prim_uom')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="prim_uom_conv">Primary UOM conversion</label>
                            <input
                                type="number"
                                step="0.0001"
                                min="0.0001"
                                id="prim_uom_conv"
                                name="prim_uom_conv"
                                class="form-control"
                                value="{{ old('prim_uom_conv', '1') }}"
                                required
                            >
                            @error('prim_uom_conv')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="sec_uom">Secondary UOM</label>
                            <input
                                type="text"
                                id="sec_uom"
                                name="sec_uom"
                                class="form-control"
                                value="{{ old('sec_uom') }}"
                                maxlength="50"
                                required
                            >
                            @error('sec_uom')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="sec_uom_conv">Secondary UOM conversion</label>
                            <input
                                type="number"
                                step="0.0001"
                                min="0.0001"
                                id="sec_uom_conv"
                                name="sec_uom_conv"
                                class="form-control"
                                value="{{ old('sec_uom_conv', '1') }}"
                                required
                            >
                            @error('sec_uom_conv')
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
                            <th>Primary UOM</th>
                            <th>Primary conversion</th>
                            <th>Secondary UOM</th>
                            <th>Secondary conversion</th>
                            <th width="150">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($units as $unit)
                            <tr>
                                <td>{{ $unit->id }}</td>
                                <td>{{ $unit->prim_uom }}</td>
                                <td>{{ number_format($unit->prim_uom_conv, 4) }}</td>
                                <td>{{ $unit->sec_uom }}</td>
                                <td>{{ number_format($unit->sec_uom_conv, 4) }}</td>
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
                                <td colspan="6" class="text-center">No units found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
