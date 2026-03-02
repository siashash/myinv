@extends('headerfooter')

@section('content')
<div class="container mt-4">
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($canAdd)
        <div class="card mb-4">
            <div class="card-header"><h5 class="mb-0">Create customer</h5></div>
            <div class="card-body">
                <form action="{{ route('customers.store') }}" method="POST">
                    @csrf
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="customer_name">Customer name</label>
                        <input type="text" id="customer_name" name="customer_name" class="form-control" value="{{ old('customer_name') }}" required>
                        @error('customer_name') <small class="text-danger">{{ $message }}</small> @enderror
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
                <button type="submit" class="btn btn-primary">Save customer</button>
                </form>
            </div>
        </div>
    @endif

    <div class="card">
        <div class="card-header"><h5 class="mb-0">Customer list</h5></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Customer ID</th>
                            <th>Customer name</th>
                            <th>Email id</th>
                            <th>Phone</th>
                            <th>Address</th>
                            <th>GST no</th>
                            <th>Status</th>
                            <th width="150">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($customers as $customer)
                            <tr>
                                <td>{{ $customer->customer_id }}</td>
                                <td>{{ $customer->customer_name }}</td>
                                <td>{{ $customer->email_id }}</td>
                                <td>{{ $customer->phone }}</td>
                                <td>{{ $customer->address }}</td>
                                <td>{{ $customer->gst_no }}</td>
                                <td>{{ $customer->status ? 'Active' : 'Inactive' }}</td>
                                <td class="text-nowrap">
                                    @if ($canEdit)
                                        <a href="{{ route('customers.edit', $customer) }}" class="btn btn-sm btn-info">Edit</a>
                                    @endif
                                    @if ($canDelete)
                                        <form action="{{ route('customers.destroy', $customer) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center">No customers found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection


