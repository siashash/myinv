@extends('headerfooter')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">Edit customer</h5></div>
                <div class="card-body">
                    <form action="{{ route('customers.update', $customer) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="customer_name">Customer name</label>
                                <input type="text" id="customer_name" name="customer_name" class="form-control" value="{{ old('customer_name', $customer->customer_name) }}" required>
                                @error('customer_name') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email_id">Email id</label>
                                <input type="email" id="email_id" name="email_id" class="form-control" value="{{ old('email_id', $customer->email_id) }}">
                                @error('email_id') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="phone">Phone</label>
                                <input type="text" id="phone" name="phone" class="form-control" value="{{ old('phone', $customer->phone) }}" required>
                                @error('phone') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="gst_no">GST no</label>
                                <input type="text" id="gst_no" name="gst_no" class="form-control" value="{{ old('gst_no', $customer->gst_no) }}">
                                @error('gst_no') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="status">Status</label>
                                <select id="status" name="status" class="form-control" required>
                                    <option value="1" {{ old('status', (string) $customer->status) == '1' ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ old('status', (string) $customer->status) == '0' ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @error('status') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="address">Address</label>
                                <textarea id="address" name="address" class="form-control" rows="2">{{ old('address', $customer->address) }}</textarea>
                                @error('address') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Update customer</button>
                        <a href="{{ route('customers.index') }}" class="btn btn-secondary">Back</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
