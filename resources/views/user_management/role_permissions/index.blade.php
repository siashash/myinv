@extends('headerfooter')

@section('content')
<div class="container mt-4">
    <div class="card mb-4">
        <div class="card-header"><h5 class="mb-0">Create role permission</h5></div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            <form action="{{ route('um.role_permissions.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="role_id">Role</label>
                        <select id="role_id" name="role_id" class="form-control" required>
                            <option value="">Select role</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>{{ $role->role_name }}</option>
                            @endforeach
                        </select>
                        @error('role_id')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="permission_id">Permission</label>
                        <select id="permission_id" name="permission_id" class="form-control" required>
                            <option value="">Select permission</option>
                            @foreach ($permissions as $permission)
                                <option value="{{ $permission->id }}" {{ old('permission_id') == $permission->id ? 'selected' : '' }}>{{ $permission->module_name }}</option>
                            @endforeach
                        </select>
                        @error('permission_id')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Save role permission</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h5 class="mb-0">Role permission list</h5></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Role</th>
                            <th>Permission</th>
                            <th width="150">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rolePermissions as $rp)
                            <tr>
                                <td>{{ $rp->role?->role_name }}</td>
                                <td>{{ $rp->permission?->module_name }}</td>
                                <td class="text-nowrap">
                                    <a href="{{ route('um.role_permissions.edit', ['roleId' => $rp->role_id, 'permissionId' => $rp->permission_id]) }}" class="btn btn-sm btn-info">Edit</a>
                                    <form action="{{ route('um.role_permissions.destroy', ['roleId' => $rp->role_id, 'permissionId' => $rp->permission_id]) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center">No role permissions found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection


