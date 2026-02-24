@extends('headerfooter')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">Edit role</h5></div>
                <div class="card-body">
                    <form action="{{ route('um.roles.update', $role) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group mb-3">
                            <label for="role_name">Role name</label>
                            <input type="text" id="role_name" name="role_name" class="form-control" value="{{ old('role_name', $role->role_name) }}" required>
                            @error('role_name')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary">Update role</button>
                        <a href="{{ route('um.roles.index') }}" class="btn btn-secondary">Back</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
