@extends('headerfooter')

@section('content')
<div class="container mt-4">
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($canAdd)
        <div class="card mb-4">
            <div class="card-header"><h5 class="mb-0">Create accounts head</h5></div>
            <div class="card-body">
                <form action="{{ route('ac_heads.store') }}" method="POST">
                    @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="ac_headname">Accounts head name</label>
                        <input type="text" id="ac_headname" name="ac_headname" class="form-control" value="{{ old('ac_headname') }}" required>
                        @error('ac_headname')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="mode">Mode</label>
                        <select id="mode" name="mode" class="form-control" required>
                            <option value="">Select mode</option>
                            <option value="Credit" {{ old('mode') == 'Credit' ? 'selected' : '' }}>Credit</option>
                            <option value="Debit" {{ old('mode') == 'Debit' ? 'selected' : '' }}>Debit</option>
                        </select>
                        @error('mode')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Save accounts head</button>
                </form>
            </div>
        </div>
    @endif

    <div class="card">
        <div class="card-header"><h5 class="mb-0">Accounts head list</h5></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Accounts head name</th>
                            <th>Mode</th>
                            <th width="150">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($acHeads as $acHead)
                            <tr>
                                <td>{{ $acHead->id }}</td>
                                <td>{{ $acHead->ac_headname }}</td>
                                <td>{{ $acHead->mode }}</td>
                                <td class="text-nowrap">
                                    @if ($canEdit)
                                        <a href="{{ route('ac_heads.edit', $acHead) }}" class="btn btn-sm btn-info">Edit</a>
                                    @endif
                                    @if ($canDelete)
                                        <form action="{{ route('ac_heads.destroy', $acHead) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">No accounts heads found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection


