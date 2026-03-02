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
                        <div class="form-group mb-3">
                            <label class="d-block">Allowed actions</label>
                            <input type="hidden" name="can_view" value="0">
                            <input type="hidden" name="can_add" value="0">
                            <input type="hidden" name="can_edit" value="0">
                            <input type="hidden" name="can_delete" value="0">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="can_view" name="can_view" value="1" {{ old('can_view', $rolePermission->can_view) ? 'checked' : '' }}>
                                <label class="form-check-label" for="can_view">View</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="can_add" name="can_add" value="1" {{ old('can_add', $rolePermission->can_add) ? 'checked' : '' }}>
                                <label class="form-check-label" for="can_add">Add new</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="can_edit" name="can_edit" value="1" {{ old('can_edit', $rolePermission->can_edit) ? 'checked' : '' }}>
                                <label class="form-check-label" for="can_edit">Edit</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="can_delete" name="can_delete" value="1" {{ old('can_delete', $rolePermission->can_delete) ? 'checked' : '' }}>
                                <label class="form-check-label" for="can_delete">Delete</label>
                            </div>
                            @error('can_view')
                                <div><small class="text-danger">{{ $message }}</small></div>
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
