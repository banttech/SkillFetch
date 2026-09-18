@extends('layout.admin.app')

@section('content')
<div class="content-card">

    <div class="">
  <div class="div-main-haeds">
        <h2 class="page-title">Update Password</h2>
    </div>

      <div class=" admin-profile-card">

            @if (Session::has('success'))
                <div class="alert alert-success">{{ Session::get('success') }}</div>
            @endif

            @if (Session::has('error'))
                <div class="alert alert-danger">{{ Session::get('error') }}</div>
            @endif

            <form class="add-ques" action="{{ route('admin.password.update') }}" method="POST">
                @csrf

                <!-- Current Password -->
                <div class="mb-3">
                    <label>Current Password <span class="text-danger">*</span></label>
                    <input type="password" name="old_password" class="custom-input form-control"  placeholder="Enter Current Password">
                    @error('old_password') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <!-- New Password -->
                <div class="mb-3">
                    <label>New Password <span class="text-danger">*</span></label>
                    <input type="password" name="password" class="custom-input form-control"  placeholder="Enter New Password">
                    @error('password') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <!-- Confirm New Password -->
                <div class="mb-3">
                    <label>Confirm Password <span class="text-danger">*</span></label>
                    <input type="password" name="password_confirmation" class="custom-input form-control"  placeholder="Enter Confirm Password">
                    @error('password_confirmation') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="text-end mt-top-2">
                    <button type="submit" class="btn btn btn-submit-form">Update</button>
                </div>

            </form>
        </div>
    </div>

</div>

@endsection
