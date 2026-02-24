@extends('headerfooter')

@section('content')
<div class="container mt-4">
    <div class="card mb-4">
        <div class="card-header"><h5 class="mb-0">Create supplier</h5></div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            <form action="{{ route('suppliers.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="supplier_name">Supplier name</label>
                        <input type="text" id="supplier_name" name="supplier_name" class="form-control" value="{{ old('supplier_name') }}" required>
                        @error('supplier_name') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="contact_name">Contact name</label>
                        <input type="text" id="contact_name" name="contact_name" class="form-control" value="{{ old('contact_name') }}" required>
                        @error('contact_name') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="email_id">Email id</label>
                        <input type="email" id="email_id" name="email_id" class="form-control" value="{{ old('email_id') }}">
                        @error('email_id') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="phone">Phone</label>
                        <input type="text" id="phone" name="phone" class="form-control" value="{{ old('phone') }}" required>
                        @error('phone') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="gst_no">GST no</label>
                        <input type="text" id="gst_no" name="gst_no" class="form-control" value="{{ old('gst_no') }}">
                        @error('gst_no') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="status">Status</label>
                        <select id="status" name="status" class="form-control" required>
                            <option value="1" {{ old('status', '1') == '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('status') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="address">Address</label>
                        <textarea id="address" name="address" class="form-control" rows="2">{{ old('address') }}</textarea>
                        @error('address') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Save supplier</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h5 class="mb-0">Supplier list</h5></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Supplier ID</th>
                            <th>Supplier name</th>
                            <th>Contact name</th>
                            <th>Email id</th>
                            <th>Phone</th>
                            <th>Address</th>
                            <th>GST no</th>
                            <th>Status</th>
                            <th width="150">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($suppliers as $supplier)
                            <tr>
                                <td>{{ $supplier->supplier_id }}</td>
                                <td>{{ $supplier->supplier_name }}</td>
                                <td>{{ $supplier->contact_name }}</td>
                                <td>{{ $supplier->email_id }}</td>
                                <td>{{ $supplier->phone }}</td>
                                <td>{{ $supplier->address }}</td>
                                <td>{{ $supplier->gst_no }}</td>
                                <td>{{ $supplier->status ? 'Active' : 'Inactive' }}</td>
                                <td class="text-nowrap">
                                    <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-sm btn-info">Edit</a>
                                    <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="9" class="text-center">No suppliers found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection


