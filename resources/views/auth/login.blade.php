<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - My Inventory</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card">
                    <div class="card-header text-center">
                        <h4 class="mb-0">My Inventory Login</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('login.submit') }}" method="POST">
                            @csrf

                            <div class="form-group mb-3">
                                <label for="name">Username</label>
                                <input
                                    type="text"
                                    id="name"
                                    name="name"
                                    value="{{ old('name') }}"
                                    class="form-control @error('name') is-invalid @enderror"
                                    required
                                    autofocus
                                >
                                @error('name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="password">Password</label>
                                <input
                                    type="password"
                                    id="password"
                                    name="password"
                                    class="form-control @error('password') is-invalid @enderror"
                                    required
                                >
                                @error('password')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Login</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
