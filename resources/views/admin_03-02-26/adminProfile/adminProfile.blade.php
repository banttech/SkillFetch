@extends('layout.admin.app')

@section('content')

<div class="content-card">
  <div class="div-main-haeds">
        <h2 class="page-title">Edit Profile</h2>
       
    </div>
        <div class=" admin-profile-card">
         

            <div class="">

                @if (Session::has('success'))
                    <div class="alert alert-success">{{ Session::get('success') }}</div>
                @endif

                @if (Session::has('error'))
                    <div class="alert alert-danger">{{ Session::get('error') }}</div>
                @endif

                <form class="add-ques" action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row">

                        <!-- Name -->
                        <div class="col-md-6 mb-3">
                            <label>Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="custom-input form-control" value="{{ Auth::user()->name }}" required>
                            @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <!-- Email -->
                        <div class="col-md-6 mb-3">
                            <label>Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="custom-input form-control" value="{{ Auth::user()->email }}"
                                required>
                            @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                    </div>

                    <div class="row">

                        <!-- Image -->
                        <div class="col-md-6 mb-3">
                            <label>Profile Image (200 x 200) <span class="text-danger">*</span></label>
                            <input type="file" name="image" class="custom-input form-control">
 @error('image') <span class="text-danger">{{ $message }}</span> @enderror
                            <p class="allowed_type">
                               Only JPG, JPEG and PNG extensions are allowed. Image size can't exceeds the size 200px X 200px.
                            </p>

                            <img src="{{ asset('admin_assets/images/' . Auth::user()->image) }}" id="previewImage"
                                class="preview-img mt-2" alt="profile" width="100" height="100">

                           
                        </div>

                    </div>

                    <div class="text-end mt-4">
                        <button class="btn btn btn-submit-form">Update</button>
                    </div>

                </form>

            </div>
        </div>

    </div>

@endsection

@section('scripts')
    <script>
        document.querySelector('input[name="image"]').addEventListener('change', function () {
            const file = this.files[0];
            const img = document.getElementById("previewImage");

            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => img.src = e.target.result;
                reader.readAsDataURL(file);
            }
        });
    </script>
@endsection