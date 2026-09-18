@extends('layout.admin.app')

@section('content')

<div class="main-content">

    <div class="card">

        <div class="card-header">
            <h5>Update Password</h5>
        </div>

        <div class="card-body">

            @if (Session::has('success'))
                <div class="alert alert-success">{{ Session::get('success') }}</div>
            @endif

            @if (Session::has('error'))
                <div class="alert alert-danger">{{ Session::get('error') }}</div>
            @endif

            <form action="{{ route('admin.password.update') }}" method="POST">
                @csrf

                <!-- Current Password -->
                <div class="mb-3">
                    <label>Current Password <span class="text-danger">*</span></label>
                    <input type="password" name="old_password" class="form-control" placeholder="Enter Current Password">
                    @error('old_password') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <!-- New Password -->
                <div class="mb-3">
                    <label>New Password <span class="text-danger">*</span></label>
                    <input type="password" name="password" class="form-control" placeholder="Enter New Password">
                    @error('password') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <!-- Confirm New Password -->
                <div class="mb-3">
                    <label>Confirm Password <span class="text-danger">*</span></label>
                    <input type="password" name="password_confirmation" class="form-control" placeholder="Enter Confirm Password">
                    @error('password_confirmation') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>

            </form>
        </div>
    </div>

</div>

@endsection
