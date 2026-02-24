@extends('headerfooter')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">Edit accounts head</h5></div>
                <div class="card-body">
                    <form action="{{ route('ac_heads.update', $acHead) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group mb-3">
                            <label for="ac_headname">Accounts head name</label>
                            <input type="text" id="ac_headname" name="ac_headname" class="form-control" value="{{ old('ac_headname', $acHead->ac_headname) }}" required>
                            @error('ac_headname')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="form-group mb-3">
                            <label for="mode">Mode</label>
                            <select id="mode" name="mode" class="form-control" required>
                                <option value="">Select mode</option>
                                <option value="Credit" {{ old('mode', $acHead->mode) == 'Credit' ? 'selected' : '' }}>Credit</option>
                                <option value="Debit" {{ old('mode', $acHead->mode) == 'Debit' ? 'selected' : '' }}>Debit</option>
                            </select>
                            @error('mode')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary">Update accounts head</button>
                        <a href="{{ route('ac_heads.index') }}" class="btn btn-secondary">Back</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
