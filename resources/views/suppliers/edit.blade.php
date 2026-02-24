@extends('headerfooter')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">Edit supplier</h5></div>
                <div class="card-body">
                    <form action="{{ route('suppliers.update', $supplier) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="supplier_name">Supplier name</label>
                                <input type="text" id="supplier_name" name="supplier_name" class="form-control" value="{{ old('supplier_name', $supplier->supplier_name) }}" required>
                                @error('supplier_name') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="contact_name">Contact name</label>
                                <input type="text" id="contact_name" name="contact_name" class="form-control" value="{{ old('contact_name', $supplier->contact_name) }}" required>
                                @error('contact_name') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email_id">Email id</label>
                                <input type="email" id="email_id" name="email_id" class="form-control" value="{{ old('email_id', $supplier->email_id) }}">
                                @error('email_id') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="phone">Phone</label>
                                <input type="text" id="phone" name="phone" class="form-control" value="{{ old('phone', $supplier->phone) }}" required>
                                @error('phone') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="gst_no">GST no</label>
                                <input type="text" id="gst_no" name="gst_no" class="form-control" value="{{ old('gst_no', $supplier->gst_no) }}">
                                @error('gst_no') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="status">Status</label>
                                <select id="status" name="status" class="form-control" required>
                                    <option value="1" {{ old('status', (string) $supplier->status) == '1' ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ old('status', (string) $supplier->status) == '0' ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @error('status') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="address">Address</label>
                                <textarea id="address" name="address" class="form-control" rows="2">{{ old('address', $supplier->address) }}</textarea>
                                @error('address') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Update supplier</button>
                        <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">Back</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
