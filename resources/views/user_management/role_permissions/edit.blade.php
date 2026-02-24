@extends('headerfooter')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">Edit role permission</h5></div>
                <div class="card-body">
                    <form action="{{ route('um.role_permissions.update', ['roleId' => $rolePermission->role_id, 'permissionId' => $rolePermission->permission_id]) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group mb-3">
                            <label for="role_id">Role</label>
                            <select id="role_id" name="role_id" class="form-control" required>
                                <option value="">Select role</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}" {{ old('role_id', $rolePermission->role_id) == $role->id ? 'selected' : '' }}>{{ $role->role_name }}</option>
                                @endforeach
                            </select>
                            @error('role_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="form-group mb-3">
                            <label for="permission_id">Permission</label>
                            <select id="permission_id" name="permission_id" class="form-control" required>
                                <option value="">Select permission</option>
                                @foreach ($permissions as $permission)
                                    <option value="{{ $permission->id }}" {{ old('permission_id', $rolePermission->permission_id) == $permission->id ? 'selected' : '' }}>{{ $permission->module_name }}</option>
                                @endforeach
                            </select>
                            @error('permission_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary">Update role permission</button>
                        <a href="{{ route('um.role_permissions.index') }}" class="btn btn-secondary">Back</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
