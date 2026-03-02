@extends('headerfooter')

@section('content')
<div class="container mt-4">
    <div class="card mb-4">
        <div class="card-header"><h5 class="mb-0">Create permission</h5></div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            <form action="{{ route('um.permissions.store') }}" method="POST">
                @csrf
                <div class="form-group mb-3">
                    <label for="module_name">Module name</label>
                    <select id="module_name" name="module_name" class="form-control" required>
                        <option value="">Select module</option>
                        @foreach ($availableModules as $group => $modules)
                            <optgroup label="{{ $group }}">
                                @foreach ($modules as $module)
                                    <option value="{{ $module }}" {{ old('module_name') === $module ? 'selected' : '' }}>{{ $module }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                    @error('module_name')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary">Save permission</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h5 class="mb-0">Permission list</h5></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Module name</th>
                            <th width="150">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($permissions as $permission)
                            <tr>
                                <td>{{ $permission->id }}</td>
                                <td>{{ $permission->module_name }}</td>
                                <td class="text-nowrap">
                                    <a href="{{ route('um.permissions.edit', $permission) }}" class="btn btn-sm btn-info">Edit</a>
                                    <form action="{{ route('um.permissions.destroy', $permission) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center">No permissions found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection


