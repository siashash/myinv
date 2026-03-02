@extends('headerfooter')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">Edit permission</h5></div>
                <div class="card-body">
                    <form action="{{ route('um.permissions.update', $permission) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group mb-3">
                            <label for="module_name">Module name</label>
                            @php $selectedModule = old('module_name', $permission->module_name); @endphp
                            <select id="module_name" name="module_name" class="form-control" required>
                                <option value="">Select module</option>
                                @foreach ($availableModules as $group => $modules)
                                    <optgroup label="{{ $group }}">
                                        @foreach ($modules as $module)
                                            <option value="{{ $module }}" {{ $selectedModule === $module ? 'selected' : '' }}>{{ $module }}</option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                                @if (! collect($availableModules)->flatten()->contains($selectedModule))
                                    <option value="{{ $selectedModule }}" selected>{{ $selectedModule }}</option>
                                @endif
                            </select>
                            @error('module_name')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary">Update permission</button>
                        <a href="{{ route('um.permissions.index') }}" class="btn btn-secondary">Back</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
